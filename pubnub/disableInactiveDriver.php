<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once '/var/www/html/roadyo1.0/Models/ConDB.php';
require_once '/var/www/html/roadyo1.0/Models/config.php';


$db = new ConDB();

$currTs = time() - APP_DRIVER_INACTIVATE_TIME; //disable all drivers who are inactive for APP_DRIVER_INACTIVATE_TIME time of seconds

$statusLog = $db->mongo->selectCollection('statusLog');

$location = $db->mongo->selectCollection('location');

$cond = array('lastTs' => array('$lte' => $currTs), 'status' => 3);

$cursor = $location->find($cond);

foreach ($cursor as $driver) {

//    print_r($driver);
    
    $logoutQry = "update user_sessions set loggedIn = '2' where oid = '" . $driver['user'] . "' and user_type = '1'";
    $logoutRes = mysql_query($logoutQry, $db->conn);
//
    if (mysql_affected_rows() > 0 || $logoutRes) {
//
        $updateWorkplaceIdQry = "update master set workplace_id = '' where mas_id = '" . $driver['user'] . "'";
        mysql_query($updateWorkplaceIdQry, $db->conn);
//
        $updateWorkplaceQry = "update workplace set Status = 2 where workplace_id = '" . $driver['carId'] . "'";
        mysql_query($updateWorkplaceQry, $db->conn);
        
        $statusLog->insert(array('master' => (int) $driver['user'], 'status' => 4, 'from' => 'cron_admin', 'time' => time()));

        $location->update(array('user' => (int) $driver['user']), array('$set' => array('status' => 4)));//, 'type' => 0, 'carId' => 0, 'chn' => '', 'listner' => '')));
    }
}
