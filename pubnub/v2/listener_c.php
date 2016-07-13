<?php

error_reporting(1);

date_default_timezone_set('Asia/Kolkata');

ini_set("log_errors", 1);
ini_set("error_log", "/tmp/php-error.log");
$date = date("Y-m-d H:i:s");
error_log("$date: Hello! Running script /roadyo_base.php" . PHP_EOL);

require('../../Models/config_v2.php');
require('../../Models/Pubnub.php');

$pubnub = new Pubnub(PUBNUB_PUBLISH_KEY, PUBNUB_SUBSCRIBE_KEY);

$con = new MongoClient("mongodb://" . MONGODB_HOST . ":" . MONGODB_PORT . "");
$db = $con->selectDB(MONGODB_DB);
if (MONGODB_USER != '' && MONGODB_PASS != '')
    $db->authenticate(MONGODB_USER, MONGODB_PASS);

$favourite = $db->selectCollection('favourite');

$noDrivers = $db->selectCollection('NoRidersFound');

$booking_route = $db->selectCollection('booking_route');

$location = $db->selectCollection('location');

$location->ensureIndex(array("location" => "2d"));

$use = array('pubnub' => $pubnub, 'location' => $location, 'favourite' => $favourite, 'booking_route' => $booking_route, 'db' => $db, 'noDrivers' => $noDrivers);

$pubnub->subscribe(array(
    "channel" => APP_PUBNUB_CHANNEL,
    "callback" => function($message) use($use) {

        $a = (int) $message['message']['a'];

        $args = $message['message'];

        if ($a == 11 || $a == '11') { //update receive time of push for a booking
            if ($args['bid'] != '') {//$args['receiveDt']
                $sqlConn = mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASS);
                if ($sqlConn) {
                    mysql_select_db(MYSQL_DB, $sqlConn);
                    $updateApptQry = "update appointment set push_rec_dt = '" . date('Y-m-d H:i:s', time()) . "' where appointment_id = '" . $args['bid'] . "'";
                    mysql_query($updateApptQry, $sqlConn);
                } else {
                    $use['noDrivers']->insert(array('qry' => $updateApptQry, 'res' => mysql_affected_rows(), 'args' => $args));
                }
            }
        } else if ($a == 4) { //update driver location
            if ($args['devId'] == '')
                $cond = array("email" => strtolower($args['e_id']));
            else
                $cond = array("email" => strtolower($args['e_id']));

            $newdata = array('$set' => array("location" => array("longitude" => (double) $args['lg'], "latitude" => (double) $args['lt']), 'lastTs' => gmmktime()));

//		print_r($newdata);

            $use['location']->update($cond, $newdata);
            if (isset($args['bid']) && (int) $args ['bid'] > 0) {
                $data = array("longitude" => (double) $args['lg'], "latitude" => (double) $args['lt']);
                if (is_array($use['booking_route']->findOne(array('bid' => (int) $args['bid'])))) {
                    $use['booking_route']->update(
                            array('bid' => (int) $args['bid'], 'route' => array('$ne' => $data))
                            , array('$push' => array('route' => $data))
                            , array("upsert" => false)
                    );
                } else {
                    $use['booking_route']->insert(array('bid' => (int) $args['bid'], 'route' => array($data)));
                }
            }
        } else if ($a == 2) {//update driver about his status
            $cond = array("email" => strtolower($args['e_id']));

            $master = $use['location']->findOne($cond);

            if ($master['chn'] == 'qd_' . $args ['d_id'])
                $return = array('a' => 2, 's' => $master['status']);
            else
                $return = array('a' => 2, 's' => 0);

            if ($args['chn'] != '')
                $pubRes[] = $use['pubnub']->publish(array(
                    'channel' => $args["chn"],
                    'message' => $return
                ));
        } else if ($a == 3) {//Acknowledge message from passenger to driver
            $cond = array("email" => strtolower($args['e_id']));

            $master = $use['location']->findOne($cond);

            if ($master["listner"] != '')
                $pubRes[] = $use['pubnub']->publish(array(
                    'channel' => $master["listner"],
                    'message' => $args
                ));
        } else if ($a == 11) {
            $cond = array("pasEmail" => strtolower($args['pid']));

            $favouritesData = $use['favourite']->find($cond);

            $driversArr = array();

            foreach ($favouritesData as $fav) {
                $driversArr[] = $fav['driver'];
            }

            if (count($driversArr) <= 0)
                $pubRes[] = $use['pubnub']->publish(array(
                    'channel' => $master["chn"],
                    'message' => array('a' => 11, 'flag' => 1)
                ));

            $resultArr = $this->mongo->selectCollection('$cmd')->findOne(array(
                'geoNear' => 'location',
                'near' => array(
                    (double) $args['ent_longitude'], (double) $args['ent_latitude']
                ), 'spherical' => true, 'maxDistance' => 50000 / 6378137, 'distanceMultiplier' => 6378137,
                'query' => array('user' => array('$in' => $driversArr)))
            );

            $md_arr = $es = array();
//                    
            foreach ($resultArr['results'] as $res) {

                $doc = $res['obj'];
                $es[] = $doc['email'];
                $md_arr[] = array('chn' => $doc['chn'], 'e' => $doc['email'], 'lt' => $doc ['location']['latitude'], 'lg' => $doc ['location']['longitude'], 'd' => ($res ['dis']));
            }

            if ($master["chn"] != '')
                if (count($md_arr) > 0)
                    $pubRes[] = $use['pubnub']->publish(array(
                        'channel' => $master["chn"],
                        'message' => array('a' => 11, 'flag' => 0, 'masArr' => $md_arr, 'es' => $es)
                    ));
                else
                    $pubRes[] = $use['pubnub']->publish(array(
                        'channel' => $master["chn"],
                        'message' => array('a' => 11, 'flag' => 1)
                    ));
        } else if ($a == 1) { // send passenger all the drivers in a specific radius
            $found = $foundEmails = $types = $foundEs = $typesData = $foundNew = array();

            if ($args['st'] == '3') { // getting data for live drivers
                $cond = array(
                    'geoNear' => 'vehicleTypes',
                    'near' => array(
                        (double) $args['lg'], (double) $args['lt']
                    ), 'spherical' => true, 'maxDistance' => 50000 / 6378137, 'distanceMultiplier' => 6378137);

                $resultArr1 = $use['db']->selectCollection('$cmd')->findOne($cond);

                foreach ($resultArr1['results'] as $res) {

                    $doc = $res['obj'];

                    $types[] = (int) $doc['type'];

                    $typesData[$doc['type']] = array(
                        'type_id' => (int) $doc['type'],
                        'type_name' => $doc['type_name'],
                        'max_size' => (int) $doc['max_size'],
                        'basefare' => (float) $doc['basefare'],
                        'min_fare' => (float) $doc['min_fare'],
                        'price_per_min' => (float) $doc['price_per_min'],
                        'price_per_km' => (float) $doc['price_per_km'],
                        'type_desc' => $doc['type_desc']
                    );
                }

                $typesDataNew = array();

                $types = array_filter(array_unique($types));
                sort($types);

                foreach ($types as $t) {
                    $typesDataNew[] = $typesData[$t];
                }

                $resultArr = $use['db']->selectCollection('$cmd')->findOne(array(
                    'geoNear' => 'location',
                    'near' => array(
                        (double) $args['lg'], (double) $args['lt']
                    ), 'spherical' => true, 'maxDistance' => 3500 / 6378137, 'distanceMultiplier' => 6378137,
                    'query' => array('status' => 3))
                );

                foreach ($resultArr['results'] as $res) {

                    $doc = $res['obj'];

//                        $types[] = (int) $doc['type'];

                    if (count($foundEs[$doc['type']]) < 5)
                        $foundEs[$doc['type']][] = $doc['email'];

                    $found[$doc['type']][] = array('chn' => $doc['chn'], 'e' => $doc['email'], 'lt' => $doc ['location']['latitude'], 'lg' => $doc ['location']['longitude'], 'd' => ($res ['dis']));
                }
            } else { // scope for later booking 
            }

            $typesFiltered = array_unique(array_filter($types));

            foreach ($typesFiltered as $type) {

                $es[] = array('tid' => $type, 'em' => (is_array($foundEs[$type])) ? $foundEs
                            [$type] : array());
                $masArr[] = array('tid' => $type, 'mas' => (is_array($found[$type])) ? $found[$type] : array());
            }

            if (count($typesDataNew) > 0) {
                $return = array('a' => 2, 'masArr' => $masArr, "tp" => $args['tp'], 'st' => $args['st'], 'flag' => 0, 'es' => $es, 'types' => $typesDataNew);
            } else {
                $return = array('a' => 2, 'flag' => 1, 'types' => $typesDataNew);

                $data = array("longitude" => (double) $args['lg'], "latitude" => (double) $args['lt'], 'time' => gmmktime());

                if (is_array($use['noDrivers']->findOne(array('email' => $args['pid'])))) {
                    $use['noDrivers']->update(
                            array('email' => $args['pid'], 'route' => array('$ne' => $data))
                            , array('$push' => array('route' => $data))
                            , array("upsert" => false)
                    );
                } else {
                    $use['noDrivers']->insert(array('email' => $args['pid'], 'route' => array($data)));
                }
            }
            if ($args["chn"] != '')
                $pubRes[] = $use['pubnub']->publish(array(
                    'channel' => $args["chn"],
                    'message' => $return
                ));

//                print_r($args);
            //    print_r($return);
        }
//            echo $a;

        $keepListening = true;
        return $keepListening;
    }
        ));
?>
