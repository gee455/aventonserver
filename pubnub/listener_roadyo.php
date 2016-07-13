<?php

error_reporting(E_ALL);

ini_set("log_errors", 1);
ini_set("error_log", "/tmp/php-error-roadyo1.log");
$date = date("Y-m-d H:i:s");
error_log("$date: Hello! Running script /roadyo1.php" . PHP_EOL);

require('../Models/config.php');
require('../Models/Pubnub.php');

$pubnub = new Pubnub(PUBNUB_PUBLISH_KEY, PUBNUB_SUBSCRIBE_KEY);

$con = new MongoClient("mongodb://" . MONGODB_HOST . ":" . MONGODB_PORT . "");
$db = $con->selectDB(MONGODB_DB);
if (MONGODB_USER != '' && MONGODB_PASS != '')
    $db->authenticate(MONGODB_USER, MONGODB_PASS);

$favourite = $db->selectCollection('favourite');
$booking_route = $db->selectCollection('booking_route');

$location = $db->selectCollection('location');

$location->ensureIndex(array("location" => "2d"));

$vehicletype = $db->selectCollection('vehicleTypes');
$vehicletype->ensureIndex(array("location" => "2d"));

$use = array('pubnub' => $pubnub, 'location' => $location, 'favourite' => $favourite, 'booking_route' => $booking_route, 'db' => $db);

$pubnub->subscribe(array(
    "channel" => APP_PUBNUB_CHANNEL,
    "presence" => function($m){
             echo json_encode($m)."\n";
     },
    "callback" => function($message) use($use) {

        $a = (int) $message['message']['a'];

        $args = $message['message'];
//  echo json_encode($args)."\n";


        if ($a == 4) { //update driver location
            if ($args['devId'] == '')
                $cond = array("email" => strtolower($args['e_id']));
            else
                $cond = array("email" => strtolower($args['e_id']));

            $newdata = array('$set' => array("location" => array("longitude" => (double) $args['lg'], "latitude" => (double) $args['lt']), 'lastTs' => time()));

            $dat = $use['location']->update($cond, $newdata);
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

            $pubRes[] = $use['pubnub']->publish(array(
                'channel' => $args["chn"],
                'message' => $return
            ));
        } else if ($a == 3) {//Acknowledge message from passenger to driver
            $cond = array("email" => strtolower($args['e_id']));

            $master = $use['location']->findOne($cond);

            $pubRes[] = $use['pubnub']->publish(array(
                'channel' => $master["listner"],
                'message' => $args
            ));
        } else if ($a == 11) {
            if (isset($args['bid']) && (int) $args ['bid'] > 0) {

                if (!is_array($use['booking_route']->findOne(array('bid' => (int) $args['bid'])))) {
                    $use['booking_route']->insert(array('bid' => (int) $args['bid'], 'receiveDt' =>$args['receiveDt']));
                } else {
                    $use['booking_route']->update(array('bid' => (int) $args['bid']),array('receiveDt' => $args['receiveDt']));
                }
            }
        } else if ($a == 1) { // send passenger all the drivers in a specific radius
            $found = $foundEmails = $types = $foundEs = $typesData = $foundNew = array();

            $surg_price = '';

            $zonefactor = $use['db']->selectCollection('zones')->findOne(
                    array("polygons" =>
                        array('$geoIntersects' =>
                            array('$geometry' =>
                                array("type" => "Point", "coordinates" => array((double) $args['lg'], (double) $args['lt'])
                                )
                            )
                        )
                    )
            );
            if (is_array($zonefactor))
                $surg_price = $zonefactor['surge_price'];

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
                        'type_desc' => $doc['type_desc'],
                        'MapIcon' => $doc['type_map_image'],
                        'vehicle_img' => $doc['type_on_image'],
                        'vehicle_img_off' => $doc['type_off_image'],
                        'surg_price' => $surg_price,
                        'order' => $doc['vehicle_order']
                );
            }
            
            
                $typesDataNew =  $order = array();

                $types = array_filter(array_unique($types));
                

                foreach ($types as $t) {
                    $typesDataNew[] = $typesData[$t];
                    $order[$t] = $typesData[$t]['order'];
                }
                array_multisort($order, SORT_ASC, $typesDataNew);
                

                $resultArr = $use['db']->selectCollection('$cmd')->findOne(array(
                    'geoNear' => 'location',
                    'near' => array(
                        (double) $args['lg'], (double) $args['lt']
                    ), 'spherical' => true, 'maxDistance' => 50000 / 6378137, 'distanceMultiplier' => 6378137,
                    'query' => array('status' => 3)) //, 'EnougMoney' => array('$ne' => 0)
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

            $masArr =  array();
            foreach ($typesDataNew as $typeData) {
                $masArr[] = array('tid' => $typeData['type_id'], 'mas' => (is_array($found[$typeData['type_id']])) ? $found[$typeData['type_id']] : array());
             }

            if (count($typesDataNew) > 0)
                $return = array('a' => 2, 'masArr' => $masArr, "tp" => $args['tp'], 'st' => $args['st'], 'flag' => 0,'types' => $typesDataNew);
            else
                $return = array('a' => 2, 'flag' => 1, 'types' => $typesDataNew);

            if ($args["chn"] != '')
                $pubRes[] = $use['pubnub']->publish(array(
                    'channel' => $args["chn"],
                    'message' => $return
                ));

//         echo json_encode($args)."\n";
       //         echo json_encode($return)."\n\n";
        }
//            echo $a;

        $keepListening = true;
        return $keepListening;
    }
        ));
?>
