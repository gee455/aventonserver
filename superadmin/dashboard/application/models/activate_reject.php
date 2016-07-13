<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include('../Models/ConDB.php');
include('../Models/class.phpmailer.php');
include('../Models/mandrill/src/Mandrill.php');
include('../Models/sendAMail.php');
include('../Models/PushWoosh.php');


require '../Models/aws.phar';
require_once '../Models/AwsPush.php';

$db = new ConDB();

function _sendApplePush($tokenArr, $aplContent, $userType) {

    $amazon = new AwsPush();
    $pushReturn = array();
    foreach ($tokenArr as $endpointArn)
        $pushReturn[] = $amazon->publishJson(array(
            'MessageStructure' => 'json',
            'TargetArn' => $endpointArn,
            'Message' => json_encode(array(
                'APNS' => json_encode(array(
                    'aps' => $aplContent
                ))
            )),
        ));

    if ($pushReturn[0]['MessageId'] == '')
        return array('errorNo' => 44, 't' => $aplContent, 'tok' => $tokenArr, 'ret' => $pushReturn);
    else
        return array('errorNo' => 46);

    if ($userType == '1') {
        $certPath = "'../cert/pocketCabs_push_cert.pem'";
        $certPass = "3Embed";
    } else {
        $certPath = "'../cert/PocketP.pem'";
        $certPass = "3Embed";
    }

    $ctx = stream_context_create();
    stream_context_set_option($ctx, 'ssl', 'local_cert', $certPath);
    stream_context_set_option($ctx, 'ssl', 'passphrase', $certPass);
    $apns_fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);

    if ($apns_fp) {

        $body['aps'] = $aplContent;

        $payload = json_encode($body);

        $msg = '';
        foreach ($tokenArr as $token) {
            $msg .= chr(0) . pack('n', 32) . pack('H*', $token) . pack('n', strlen($payload)) . $payload;
        }

        $result = fwrite($apns_fp, $msg, strlen($msg));

        if (!$result)
            return array('errorNo' => 46);
        else
            return array('errorNo' => 44);
    } else {
        return array('errorNo' => 30, 'error' => $errstr);
    }
}

function sendAndroidPush($tokenArr, $andrContent, $apiKey) {

//        print_r($tokenArr);

    $fields = array(
        'registration_ids' => $tokenArr,
        'data' => $andrContent,
    );

    $headers = array(
        'Authorization: key=' . $apiKey,
        'Content-Type: application/json'
    );
// Open connection
    $ch = curl_init();

// Set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, 'http://android.googleapis.com/gcm/send');

    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Disabling SSL Certificate support temporarly
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

// Execute post
    $result = curl_exec($ch);

    curl_close($ch);
//        echo 'Result from google:' . $result . '---';
    $res_dec = json_decode($result);

    if ($res_dec->success >= 1)
        return array('errorNo' => 44, 'result' => $result);
    else
        return array('errorNo' => 46, 'result' => $result);
}

if ($_REQUEST['item_type'] == '1') {
    $pushWoosh = new PushWoosh();

    $emailData = $masters = array();

    if ($_REQUEST['status'] == '1') {
        if (is_array($_REQUEST['item_list'])) {
            foreach ($_REQUEST['item_list'] as $li) {
                $userExploded = explode('-', $li);
                $masters[] = (int) $userExploded[0];
            }
        } else {
            $userExploded = explode('-', $_REQUEST['item_list']);
            $masters[] = (int) $userExploded[0];
        }
    } else {
        $masters = $_REQUEST['item_list'];
    }


    $query1 = " select distinct mas.first_name,mas.email,mas.password,mas.workplace_id from master mas where mas.mas_id IN (" . implode(',', $masters) . ")";
    $res1 = mysql_query($query1, $db->conn);
    while ($driver = mysql_fetch_assoc($res1)) {
        $emailData[] = array('email' => $driver['email'], 'name' => $driver['first_name'], 'pass' => $driver['password'], 'car' => $driver['workplace_id']);
    }

    if ($_REQUEST['status'] == '1') {

        if (is_array($_REQUEST['item_list'])) {
//            $list1 = explode(',', $_REQUEST['item_list']);
            foreach ($_REQUEST['item_list'] as $li) {
                $userExploded = explode('-', $li);
                $updatemasterQry = "update master set status = '" . $_REQUEST['to_do'] . "',company_id = '" . $userExploded[1] . "' where mas_id = '" . $userExploded[0] . "'";
                $updatemasterRes = mysql_query($updatemasterQry, $db->conn);
            }
        } else {
            $userExploded = explode('-', $_REQUEST['item_list']);
            $updatemasterQry = "update master set status = '" . $_REQUEST['to_do'] . "',company_id = '" . $userExploded[1] . "' where mas_id = '" . $userExploded[0] . "'";
            $updatemasterRes = mysql_query($updatemasterQry, $db->conn);
        }
    } else {


        $updatemasterQry = "update master set status = '" . $_REQUEST['to_do'] . "' where mas_id IN (" . implode(',', $_REQUEST['item_list']) . ")";
        $updatemasterRes = mysql_query($updatemasterQry, $db->conn);
        /*  } */
//        echo $updatemasterQry . '--2--';
    }
    if (mysql_affected_rows() > 0 || $updatemasterRes) {
        $err = array();
        $mail = new sendAMail($db->host);
        if ($_REQUEST['to_do'] == '3') {
            foreach ($emailData as $master)
                $err[] = $mail->masterActivated($master['email'], $master['name'], $master['password']);
        } else if ($_REQUEST['to_do'] == '4') {

            $updateWorkplaceIdQry = "update master set workplace_id = '' where mas_id  IN (" . implode(',', $_REQUEST['item_list']) . ")";
            mysql_query($updateWorkplaceIdQry, $db->conn);

            foreach ($emailData as $car) {
                $updateWorkplaceQry = "update workplace set Status = 2 where workplace_id = '" . $car['car'] . "'";
                mysql_query($updateWorkplaceQry, $db->conn);
            }

            $query = "update appointment set status = '5',extra_notes = 'Admin rejected driver, so cancelled the booking',cancel_status = '8' where mas_id IN (" . implode(',', $_REQUEST['item_list']) . ") and status IN (6,7,8)";
            $res = mysql_query($query, $db->conn);

            $location = $db->mongo->selectCollection('location');
            $newdata = array('$set' => array("status" => (int) $_REQUEST['to_do'], 'carId' => 0, 'type' => 0));

            foreach ($emailData as $master)
                $err[] = $mail->masterSuspended($master['email'], $master['name']);

//        print_r($list);

            foreach ($masters as $doc) {
                $location->update(array("user" => (int) $doc), $newdata);
            }
//            mysql_query('delete from user_sessions where user_type = 1 and oid IN (' . implode(', ', $_REQUEST['item_list']) . ')');
        }

        if ($_REQUEST['to_do'] == '4') {

            $updateSession = "update user_sessions set loggedIn = 2 where oid IN (" . implode(',', $masters) . ") and loggedIn = 1 and user_type = 1";
            mysql_query($updateSession, $db->conn);

            $nt = 12;
            $passMsg = "Your profile has been rejected on $db->appName, contact pocketcab customer care";
            $getTokensQry = "select * from user_sessions where oid IN (" . implode(',', $masters) . ") and loggedIn = 1 and user_type = 1 and LENGTH(push_token) > 63";
            $getTokensRes = mysql_query($getTokensQry, $db->conn);
//    echo $getTokensQry;

            while ($token = mysql_fetch_assoc($getTokensRes)) {
                if ($token['type'] == '2') {
                    $ret[] = sendAndroidPush(array($token['push_token']), array('action' => $nt, 'payload' => $passMsg), $db->driverAndroidPushApiKey);
                } else {
                    $ret[] = _sendApplePush(array($token['push_token']), array('nt' => $nt, 'alert' => $passMsg, 'sound' => 'default'), '1');
//                    $pushReturn2 = $pushWoosh->pushDriver($passMsg, array('nt' => $nt), array($token['push_token']));
//                    if ($pushReturn2['info']['http_code'] == 200)
//                        $ret[] = array('errorNo' => 44);
//                    else
//                        $ret[] = array('errorNo' => 46);
                }
            }
        }
//        else {
//            $nt = 11;
//            $passMsg = "Your profile is activated on $db->appName";
//        }



        $res = array('flag' => 0, 'message' => 'Process completed', 'error' => $updatemasterQry, 'test' => $doc . '-' . $_REQUEST['to_do']);
    } else {
//        echo '--4--';
        $res = array('flag' => 1, 'message' => 'Error occured, consult developer', 'error' => $updatemasterQry . $_REQUEST['item_list'] . '[]' . $_REQUEST['status']);
    }


    echo json_encode($res);
}

if ($_REQUEST['item_type'] == '2') {

    $updatemasterQry = "update slave set status = '" . $_REQUEST['to_do'] . "' where slave_id IN (" . implode(', ', $_REQUEST['item_list']) . ")";
    $updatemasterRes = mysql_query($updatemasterQry, $db->conn);

    if (mysql_affected_rows() > 0 || $updatemasterRes) {

        $pushWoosh = new PushWoosh();

        if ($_REQUEST['to_do'] == '4') {
            $nt = 12;
            $passMsg = "Your profile has been suspended on $db->appName, contact pocketcab customer care";
        } else {
            $nt = 11;
            $passMsg = "Your profile is activated on $db->appName";
        }

        $getTokensQry = "select * from user_sessions where oid IN (" . implode(',', $_REQUEST['item_list']) . ") and loggedIn = 1 and user_type = 2 and LENGTH(push_token) > 63";
        $getTokensRes = mysql_query($getTokensQry, $db->conn);
//    echo $getTokensQry;

        while ($token = mysql_fetch_assoc($getTokensRes)) {
            if ($token['type'] == '2') {
                $ret[] = sendAndroidPush(array($token['push_token']), array('action' => $nt, 'payload' => $passMsg), $db->paxAndroidPushApiKey);
            } else {
                $ret[] = _sendApplePush(array($token['push_token']), array('nt' => $nt, 'alert' => $passMsg, 'sound' => 'default'), '2');
//                $pushReturn2 = $pushWoosh->pushPassenger($passMsg, array('nt' => $nt), array($token['push_token']));
//                if ($pushReturn2['info']['http_code'] == 200)
//                    $ret[] = array('errorNo' => 44);
//                else
//                    $ret[] = array('errorNo' => 46);
            }
        }

        $res = array('flag' => 0, 'message' => 'Process completed', 'error' => $updatemasterQry);
    } else {
        $res = array('flag' => 1, 'message' => 'Sorry process failed', 'error' => $updatemasterQry);
    }

    echo json_encode($res);
}


if ($_REQUEST['item_type'] == '10') {

    $updatemasterQry = "update master_ratings set status = '" . $_REQUEST['to_do'] . "' where review_dt IN (" . implode(', ', $_REQUEST['item_list']) . ")";
    $updatemasterRes = mysql_query($updatemasterQry, $db->conn);

    if (mysql_affected_rows() > 0 || $updatemasterRes) {
        $res = array('flag' => 0, 'message' => 'Process completed', 'error' => $updatemasterQry);
    } else {
        $res = array('flag' => 1, 'message' => 'Sorry process failed', 'error' => $updatemasterQry);
    }

    echo json_encode($res);
}
if ($_REQUEST['item_type'] == '19') {

    $updatemasterQry = "update  master set status = '" . $_REQUEST['to_do'] . "'  where mas_id IN (" . implode(', ', $_REQUEST['item_list']) . ")";
    $updatemasterRes = mysql_query($updatemasterQry, $db->conn);

    if (mysql_affected_rows() > 0 || $updatemasterRes) {
        $res = array('flag' => 0, 'message' => 'Process completed', 'error' => $updatemasterQry);
    } else {
        $res = array('flag' => 1, 'message' => 'Sorry process failed', 'error' => $updatemasterQry);
    }

    echo json_encode($res);
}


if ($_REQUEST['item_type'] == '8') {

    $updatemasterQry = "update workplace set Status = '" . $_REQUEST['to_do'] . "' where workplace_id IN (" . implode(', ', $_REQUEST['item_list']) . ")";
    $updatemasterRes = mysql_query($updatemasterQry, $db->conn);

    if (mysql_affected_rows() > 0 || $updatemasterRes) {


        if ($_REQUEST['to_do'] == '4') {

            $location = $db->mongo->selectCollection('location');
            $newdata = array('$set' => array("status" => (int) $_REQUEST['to_do'], 'carId' => 0, 'type' => 0));

            $nt = 12;
            $passMsg = "Your car has been suspended from admin, contact your company for more details";

            $getTokensQry = "select * from user_sessions where oid IN (select mas_id from master where workplace_id IN (" . implode(',', $_REQUEST['item_list']) . ")) and loggedIn = 1 and user_type = 1 and LENGTH(push_token) > 63";
            $getTokensRes = mysql_query($getTokensQry, $db->conn);
//    echo $getTokensQry;

            while ($token = mysql_fetch_assoc($getTokensRes)) {

                $updateSession = "update user_sessions set loggedIn = 2 where sid = '" . $token['sid'] . "'";
                mysql_query($updateSession, $db->conn);

                $query = "update appointment set status = '5',extra_notes = 'Admin rejected vehicle, so cancelled the booking',cancel_status = '8' where mas_id = '" . $token['oid'] . "' and status IN (6,7,8)";
                $res = mysql_query($query, $db->conn);

                $location->update(array('user' => (int) $token['oid']), $newdata);

                if ($token['type'] == '2') {
                    $ret[] = sendAndroidPush(array($token['push_token']), array('action' => $nt, 'payload' => $passMsg), $db->driverAndroidPushApiKey);
                } else {
                    $ret[] = _sendApplePush(array($token['push_token']), array('nt' => $nt, 'alert' => $passMsg, 'sound' => 'default'), '1');
//                    $pushReturn2 = $pushWoosh->pushDriver($passMsg, array('nt' => $nt), array($token['push_token']));
//                    if ($pushReturn2['info']['http_code'] == 200)
//                        $ret[] = array('errorNo' => 44);
//                    else
//                        $ret[] = array('errorNo' => 46);
                }
            }
        }
        $res = array('flag' => 0, 'message' => 'Process completed', 'error' => $updatemasterQry);
    } else {
        $res = array('flag' => 1, 'message' => 'Password reset failed', 'error' => $updatemasterQry);
    }

    echo json_encode($res);
}

if ($_REQUEST['item_type'] == '11') {

    $updatemasterQry = "update user_sessions set loggedin = '" . $_REQUEST['to_do'] . "' where oid IN (" . implode(', ', $_REQUEST['item_list']) . ")";
    $updatemasterRes = mysql_query($updatemasterQry, $db->conn);

    if (mysql_affected_rows() > 0 || $updatemasterRes) {
        $res = array('flag' => 0, 'message' => 'Process completed', 'error' => $updatemasterQry);
    } else {
        $res = array('flag' => 1, 'message' => 'Password reset failed', 'error' => $updatemasterQry);
    }

    echo json_encode($res);
}


if ($_REQUEST['item_type'] == '3') {

    $updatemasterQry = "update company_info set Status = '" . $_REQUEST['to_do'] . "' where company_id IN (" . implode(', ', $_REQUEST['item_list']) . ")";
    $updatemasterRes = mysql_query($updatemasterQry, $db->conn);

    if (mysql_affected_rows() > 0 || $updatemasterRes) {

        if ($_REQUEST['to_do'] == '4') {
            $location = $db->mongo->selectCollection('location');
            $newdata = array('$set' => array("status" => (int) $_REQUEST['to_do'], 'carId' => 0, 'type' => 0));

            $nt = 12;
            $passMsg = "Your company has been suspended from admin, contact company for more details";

            $getTokensQry = "select * from user_sessions where oid IN (select mas_id from master where company_id IN (" . implode(',', $_REQUEST['item_list']) . ")) and loggedIn = 1 and user_type = 1 and LENGTH(push_token) > 63";
            $getTokensRes = mysql_query($getTokensQry, $db->conn);
//    echo $getTokensQry;

            while ($token = mysql_fetch_assoc($getTokensRes)) {

                $updateSession = "update user_sessions set loggedIn = 2 where sid = '" . $token['sid'] . "'";
                mysql_query($updateSession, $db->conn);

                $query = "update appointment set status = '5',extra_notes = 'Admin rejected vehicle, so cancelled the booking',cancel_status = '8' where mas_id = '" . $token['oid'] . "' and status IN (6,7,8)";
                $res = mysql_query($query, $db->conn);

                $location->update(array('user' => (int) $token['oid']), $newdata);

                if ($token['type'] == '2') {
                    $ret[] = sendAndroidPush(array($token['push_token']), array('action' => $nt, 'payload' => $passMsg), $db->driverAndroidPushApiKey);
                } else {
                    $ret[] = _sendApplePush(array($token['push_token']), array('nt' => $nt, 'alert' => $passMsg, 'sound' => 'default'), '1');
//                    $pushReturn2 = $pushWoosh->pushDriver($passMsg, array('nt' => $nt), array($token['push_token']));
//                    if ($pushReturn2['info']['http_code'] == 200)
//                        $ret[] = array('errorNo' => 44);
//                    else
//                        $ret[] = array('errorNo' => 46);
                }
            }
        }


        $res = array('flag' => 0, 'message' => 'Process completed', 'error' => $updatemasterQry);
    } else {
        $res = array('flag' => 1, 'message' => 'Password reset failed', 'error' => $updatemasterQry);
    }

    echo json_encode($res);
}

if ($_REQUEST['item_type'] == '4') {

    $delvechiletype = "delete from workplace_types  where  type_id IN (" . implode(', ', $_REQUEST['item_list']) . ")";
    mysql_query($delvechiletype, $db->conn);

    if (mysql_affected_rows() > 0) {

        $delvechile = "delete from workplace  where type_id IN (" . implode(', ', $_REQUEST['item_list']) . ")";
        mysql_query($delvechile, $db->conn);
        // delete all the vehicles having company id

        $getmasters = " select mas_id from master where type_id IN (" . implode(', ', $_REQUEST['item_list']) . ")";
        $getmasterres = mysql_query($getmasters, $db->conn);
        $masIdArr = array();
        while ($result = mysql_fetch_array($getmasterres)) {
            $masIdArr[] = (int) $result['mas_id'];
        }

        // Get all master ids in this company

        $updatemasterQry = "update master set type_id = 0,company_id = 0,workplace_id = 0 where company_id IN (" . implode(', ', $_REQUEST['item_list']) . ")";
        mysql_query($updatemasterQry, $db->conn);

// update master table as car id 0 type id 0 company id 0 where company id is the given one 

        $delsession = "delete from user_sessions  where oid IN (" . implode(', ', $masIdArr) . ") AND user_type = 1 ";
        mysql_query($delsession, $db->conn);

        // Delete all the sessions for masters you got, delete from user_sessions where oid in (master_ids) and user_type = 1

        $location = $db->mongo->selectCollection('location');
        $vehicleTypes = $db->mongo->selectCollection('vehicleTypes');

        $updateArr = array('$set' => array('type' => 0, 'carId' => 0, 'status' => 5));
        $location->update(array('user' => array('$in' => $masIdArr)), array('$set' => $updateArr), array('multiple' => 1));

        //update all master data in mongo db

        $typesArr = array();

        foreach ($_REQUEST['item_list'] as $type) {
            $typesArr[] = (int) $type;
        }

        //remove all types from mongo db as well

        $vehicleTypes->remove(array('type' => array('$in' => $typesArr)), array('multiple' => 1));

        $res = array('flag' => 0, 'message' => 'Process completed', 'error' => $delvechiletype);
    } else {
        $res = array('flag' => 1, 'message' => 'Password reset failed', 'error' => $delvechiletype);
    }

    echo json_encode($res);
}

if ($_REQUEST['item_type'] == '9') {
    $delvechile = "update  workplace set Status= '" . $_REQUEST['to_do'] . "' where workplace_id IN (" . implode(', ', $_REQUEST['item_list']) . ")";
    $delvechileres = mysql_query($delvechile, $db->conn);

    if (mysql_affected_rows() > 0 || $delvechileres) {

        $selectDriver = "select mas_id from master where workplace_id IN (" . implode(', ', $_REQUEST['item_list']) . ")";
        $selectDriverRes = mysql_query($selectDriver, $db->conn);

        while ($driver = mysql_fetch_assoc($selectDriverRes))
            $drivers[] = $driver['oid'];

        $res = array('flag' => 0, 'message' => 'Process completed', 'error' => $delvechile);
    } else {
        $res = array('flag' => 1, 'message' => 'Password reset failed', 'error' => $delvechile);
    }
    echo json_encode($res);
}

if ($_REQUEST['item_type'] == '21') {

    $note = $_REQUEST['mgmt_note'];

    $updateReport = "update reports set admin_note = '" . $note . "',report_status = 2 where report_id IN (" . implode(', ', $_REQUEST['item_list']) . ")";
    mysql_query($updateReport, $db->conn);

    if (mysql_affected_rows() >= 0) {

        $selectAllReportsQry = "select appointment_id from reports where report_id IN (" . implode(', ', $_REQUEST['item_list']) . ")";
        $selectAllReportsRes = mysql_query($selectAllReportsQry, $db->conn);

        $apptIds = array();

        while ($report = mysql_fetch_assoc($selectAllReportsRes)) {
            $apptIds[] = $report['appointment_id'];
        }

        $delvechile = "update appointment set payment_status = 3 where appointment_id IN (" . implode(', ', $apptIds) . ")";
        mysql_query($delvechile, $db->conn);

        echo json_encode(array('errFlag' => 0, 'message' => $updateReport));
    } else {
        echo json_encode(array('errFlag' => 1, 'message' => 'Cannot resolve the dispute currently, try after some time.'));
    }
}
?>
