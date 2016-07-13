<?php

error_reporting(1);

//switch ($_SERVER['HTTP_ORIGIN']) {
//    case 'http://taxi-dispatcher.com':
header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
//        break;
//}

ini_set("log_errors", 1);
ini_set("error_log", "/tmp/php-error.log");
$date = date("Y-m-d H:i:s");
error_log("$date: Hello! Running script /services_v2.php" . PHP_EOL);

require_once 'Models/config.php';

require_once 'Models/API.php';
require_once 'Models/ConDBServices.php';
require_once 'Models/getErrorMsg.php';
require_once 'Models/ManageToken.php';
require_once 'Models/class.verifyEmail.php';
require_once 'Models/StripeModule.php';
require_once 'Models/mandrill/src/Mandrill.php';
require_once 'Models/InvoiceHtml.php';
require_once 'Models/sendAMail.php';
//require_once 'Models/mailGun.php';
require_once 'Models/class.phpmailer.php';
require_once 'Models/MPDF56/mpdf.php';
require_once 'Models/Pubnub.php';
//require_once 'Models/paypal.php';
//require_once 'Models/plivo.php';
//require_once 'Models/PushWoosh.php';
require_once 'Models/twilio-php/Services/Twilio.php';
require 'Models/aws.phar';
require_once 'Models/AwsPush.php';

class MyAPI extends API {

    protected $User;
    private $db;
    private $mongo;
    private $ios_cert_path;
    private $ios_cert_pwd;
    private $androidApiKey;
    private $androidUrl = 'http://android.googleapis.com/gcm/send';
    private $default_profile_pic = 'aa_default_profile_pic.gif';
    private $ios_cert_server = "ssl://gateway.push.apple.com:2195";
//    private $ios_cert_server = "ssl://gateway.push.apple.com:2195";
    private $stripe;
    private $curr_date_time;
    private $maxChunkSize = 1048576;
    private $reviewsPageSize = 5;
    private $historyPageSize = 5;
    private $cancellationTimeInSec = 300; //cancellation time for free in seconds
    private $pubnub;
    private $distanceMetersByUnits = APP_DISTANCE_METERS;
    private $serverUploader = "";
    private $promoCodeRadius = 100;
    private $share;
    private $expireTimeForDriver = 30;

    /*
      Development -- ssl://gateway.sandbox.push.apple.com:2195
      Production -- ssl://gateway.push.apple.com:2195
     */

    
     public function __construct($request_uri, $postData, $origin) {

        parent::__construct($request_uri, $postData);

        $this->db = new ConDB();

        $this->share = APP_SERVER_HOST . "admin/track.php?id=";

        $this->mongo = $this->db->mongo;
        $this->stripe = new StripeModule();

        $this->pubnub = new Pubnub(PUBNUB_PUBLISH_KEY, PUBNUB_SUBSCRIBE_KEY);
    }
    
    protected function PushFromAdmin($args) {
        
        $query = '';
        $aplPushContent = array('alert' => $message, 'nt' => '13');
        $andrPushContent = array("payload" => $message, 'action' => '13');
        
        if($args['driver_id'] != '' || $args['driver_id'] != NULL)
        {
             
            $message = 'Your profile got rejected by our admin, please contact our support for further queries.';
            
             $qry = "update master set status = 4 where mas_id = '" . $args['driver_id'] ."'";
             mysql_query($qry, $this->db->conn);
            
            $location = $this->mongo->selectCollection('location');

            $masterDet = $location->update(array('status' => 4, 'carId' => 0, 'type' => 0), array('user' => (int) $args['driver_id']));
            $query = "select * from user_sessions where loggedIn = 1 and user_type = 1 and oid = '" . $args['driver_id'] . "'";
        }
        else
        {
            
            $mas_ids = array();
             $message = 'This vehicle got rejected by our admin, please contact our support for further queries.';
            
             $q = "select * from master where workplace_id = '" . $args['Workplace_id'] . "'";
             $dataa = mysql_query($q, $this->db->conn);
             
              while ($Mas = mysql_fetch_assoc($dataa)) {
                
                    $mas_ids[] = $Mas['mas_id'];
               
            }
            $mas_ids = implode(',', array_filter(array_unique($mas_ids)));
             $query = "select * from user_sessions where loggedIn = 1 and user_type = 1 and oid in ('" . $mas_ids . "')";
        }

     
        $da = mysql_query($query, $this->db->conn);
        if (mysql_num_rows($da) > 0) {
//
            while ($tokenArr = mysql_fetch_assoc($da)) {
                if ($tokenArr['type'] == 1)
                    $aplTokenArr[] = $tokenArr['push_token'];
                else if ($tokenArr['type'] == 2)
                    $andiTokenArr[] = $tokenArr['push_token'];
            }
            
           

            $aplTokenArr1 = array_values(array_filter(array_unique($aplTokenArr)));
            $andiTokenArr1 = array_values(array_filter(array_unique($andiTokenArr)));
//            print_r($andiTokenArr);
            if (count($aplTokenArr) > 0)
                $aplResponse = $this->_sendApplePush($aplTokenArr1, $aplPushContent,'1');

            if (count($andiTokenArr) > 0)
                $andiResponse = $this->_sendAndroidPush($andiTokenArr1, $andrPushContent,'1');
            
            
           

        $count = 0;
             if ($aplResponse['errorNo'] != '')
             {
                $errNum = $aplResponse['errorNo'];
                 if($andiResponse['errorNo'] == '46')
                    $count = $count + count($aplTokenArr1);
             }
            if ($andiResponse['errorNo'] != '')
            {
                $errNum = $andiResponse['errorNo'];
                if($andiResponse['errorNo'] == '44')
                    $count = $count + count($andiTokenArr1);
            }
            else
                $errNum = 46;
            
          
                
            
            
            if($count != 0)
            {
                if($args['driver_id'] != '' || $args['driver_id'] != NULL)
                {
                        $updateDataQry = "update user_sessions set loggedIn = 2 where oid = '" .$args['driver_id'] . "' and loggedIn = 1 and user_type = 1";
                        mysql_query($updateDataQry, $this->db->conn);
                        
                        $Qry = "update master set status = 4 where mas_id = '" .$args['driver_id'] . "'";
                        mysql_query($Qry, $this->db->conn);
                        
                }
                
                
            }

            return array('insEnt' => $return_arr, 'errNum' => $errNum,'driver_id'=>$args['driver_id'], 'andiRes' => $andiResponse);
        } else {
            return array('insEnt' => $return_arr, 'errNum' => 45,'Cond'=>$query,'driver_id'=>$args['driver_id'],'andiRes' => $andiResponse); //means push not sent
        }
    }
    
    
    
    protected function PushFromAdminForSpicific($args) {
        
         $andiTokenArr = array();
        $aplTokenArr =array();
        $User_ids = array();
        $user_data = array();
        $message = $args['message'];
        $aplPushContent = array('alert' => $message, 'nt' => '13');
         $andrPushContent = array("payload" => $message, 'action' => '13');
//        $emails = $this->input->post('emails');
        $User_id = $args['User_id'];
        $message = $args['message'];
        $city_id = $args['city_id'];
       
//        $msg = "Driver";
        $query = "";
        $usertype = $args['usertype'];
        
        if ($usertype == 2) {
            $query = "select * from slave where slave_id in ('". $User_id ."')"; //If the passengers are deleted so for that check user exist on not
//            $msg = "Passanger";
            
             $data = mysql_query($query, $this->db->conn);
////             $driversArrIos[] = 'arn:aws:sns:ap-southeast-2:284495162885:endpoint/APNS/passanger/f03799da-2b62-3cf2-9053-dda9303c9d47';
//         
        if (mysql_num_rows($data) > 0) {
//
            while ($tokenArr = mysql_fetch_assoc($data)) 
                 $User_ids[] = $tokenArr['slave_id'];
        }
            
        $User_ids = implode(',', array_filter(array_unique($User_ids)));
            $query1 = "select * from user_sessions where oid in ('". $User_ids ."') and user_type = 2 and loggedIn = 1";
          $data1 = mysql_query($query1, $this->db->conn);
             
           if (mysql_num_rows($data1) > 0) {
//
            while ($tokenArr = mysql_fetch_assoc($data1)) {
               
                if ($tokenArr['type'] == 1)
                    $aplTokenArr[] = $tokenArr['push_token'];
                else if ($tokenArr['type'] == 2)
                    $andiTokenArr[] = $tokenArr['push_token'];
            }
        }
        } else {
             $query = "select * from master where mas_id in ('". $User_id ."')";
            $msg = "Driver";
            $data = mysql_query($query, $this->db->conn);
         
        if (mysql_num_rows($data) > 0) {
            while ($tokenArr = mysql_fetch_assoc($data)) 
                 $User_ids[] = $tokenArr['mas_id'];
        }

            $d = $users = implode(',', array_filter(array_unique($User_ids)));
        
            $query1 = "select * from user_sessions where oid in ('" . $d . "') and user_type = 1 and loggedIn = 1";
             $data1 = mysql_query($query1, $this->db->conn);
             
           if (mysql_num_rows($data1) > 0) {
//
            while ($tokenArr = mysql_fetch_assoc($data1)) {
               
                if ($tokenArr['type'] == 1)
                    $aplTokenArr[] = $tokenArr['push_token'];
                else if ($tokenArr['type'] == 2)
                    $andiTokenArr[] = $tokenArr['push_token'];
            }
        }
            
//            if (empty(array_filter($andiTokenArr)) && empty(array_filter($andiTokenArr))) {// || empty(array_filter($driversArrIos))) {
//                echo json_encode(array('flag' => 2, 'msg' =>'No user found'));
//                return;
//            }
        }
        
         $aplTokenArr1 = array_values(array_filter(array_unique($aplTokenArr)));
            $andiTokenArr1 = array_values(array_filter(array_unique($andiTokenArr)));
            
             if (count($aplTokenArr) > 0)
                $aplResponse = $this->_sendApplePush($aplTokenArr1, $aplPushContent,$usertype);

            if (count($andiTokenArr) > 0)
                $andiResponse = $this->_sendAndroidPush($andiTokenArr1, $andrPushContent,$usertype);
            
            $count = 0;
             if ($aplResponse['errorNo'] != '')
             {
                $errNum = $aplResponse['errorNo'];
                $count = $count + count($aplTokenArr1);
             }
            if ($andiResponse['errorNo'] != '')
            {
                $errNum = $andiResponse['errorNo'];
                $count = $count + count($andiTokenArr1);
            }
            else
                $errNum = 46;
            
            
            if($count != 0)
            {
                $insertArr = array('user_type' => (int)$usertype,'DateTime'=>date('Y-m-d H:i:s'),'msg'=>$message,'city'=>$city_id,'user_ids'=>$User_ids);
                $lastid =  $this->mongo->selectCollection('AdminNotifications');
                $lastid->insert($insertArr);
            }
            
            return array('insEnt' => $aplTokenArr1,'Count'=>$count, 'errNum' => $errNum,'andiRes' => $andiResponse);
     }
    

     protected function PushFromAdminForAll($args) {
        
         $andiTokenArr = array();
        $aplTokenArr =array();
        $User_ids = array();
        $user_data = array();
        
        $citylatlon = explode('-',$args['city']);
        $message = $args['message'];
        $usertype = $args['usertype'];
     
        $aplPushContent = array('alert' => $message, 'nt' => '13');
         $andrPushContent = array("payload" => $message, 'action' => '13');
//       
        $driversArrAndroid = array();
        $driversArrIos = $array =array();
        $User_ids = array();

        

//        $msg = "Driver";
        $query = "";
       
        if ($usertype == 2) {
            $query = "select * from slave s,user_sessions us where (3956 * acos( cos(radians('". $citylatlon[0] . "') ) * COS( RADIANS(s.latitude) ) * cos(radians(s.longitude) - radians('". $citylatlon[1] ."')) + sin( radians('". $citylatlon[0] . "')) * sin( radians(s.latitude) ) ) ) <= 100 and us.oid = s.slave_id and us.user_type = 2 and loggedIn = 1";
//            $msg = "Passanger";
            
          
            $data = mysql_query($query, $this->db->conn);
          
       
            if (mysql_num_rows($data) > 0) {
                while ($tokenArr = mysql_fetch_assoc($data)) 
                {
                   
                         if ($tokenArr['type'] == 1)
                            $aplTokenArr[] = $tokenArr['push_token'];
                        else if ($tokenArr['type'] == 2)
                            $andiTokenArr[] = $tokenArr['push_token'];
                       
                        $User_ids[] = $tokenArr['oid'];
                       
                }
                
            }
            
        } else {
            
//             $location = $this->mongo->selectCollection('verification');
            $resultArr = $this->mongo->selectCollection('$cmd')->findOne(array(
                'geoNear' => 'location',
                'near' => array(
                    (double) $citylatlon[1], (double) $citylatlon[0]
                //  (double) $_REQUEST['lat'], (double) $_REQUEST['lon']
                ), 'spherical' => true, 'maxDistance' => 50000 / 6378137, 'distanceMultiplier' => 6378137)
            );


            foreach ($resultArr['results'] as $res) {

                $doc = $res['obj'];

                if ($doc['User_type'] == 1)
                    $aplTokenArr[] = $doc['pushToken'];
                if ($doc['User_type'] == 2)
                    $andiTokenArr[] = $doc['pushToken'];
                
                 $User_ids[] = $doc['user'];
                
            }

//            if (empty(array_filter($driversArrAndroid))) {// || empty(array_filter($driversArrIos))) {
//                echo json_encode(array('flag' => 2, 'msg' => $msg));
//                return;
//            }
        }
        
         $aplTokenArr1 = array_values(array_filter(array_unique($aplTokenArr)));
            $andiTokenArr1 = array_values(array_filter(array_unique($andiTokenArr)));
            
            
             if (count($aplTokenArr) > 0)
                $aplResponse = $this->_sendApplePush($aplTokenArr1, $aplPushContent,$usertype);

            if (count($andiTokenArr) > 0)
                $andiResponse = $this->_sendAndroidPush($andiTokenArr1, $andrPushContent,$usertype);
            
            $count = 0;
             if ($aplResponse['errorNo'] != '')
             {
                $errNum = $aplResponse['errorNo'];
                 $count = $count + count($aplTokenArr1);
             }
            if ($andiResponse['errorNo'] != '')
            {
                $errNum = $andiResponse['errorNo'];
                 $count = $count + count($aplTokenArr1);
            }
            else
                $errNum = 46;
            
             if($count != 0)
            {
                $insertArr = array('user_type' => (int)$usertype,'DateTime'=>date('Y-m-d H:i:s'),'msg'=>$message,'city'=>$citylatlon[2],'user_ids'=>$User_ids);
                $lastid =  $this->mongo->selectCollection('AdminNotifications');
                $lastid->insert($insertArr);
            }
            
            return array('insEnt' => $aplTokenArr1,'Count'=>$count, 'errNum' => $errNum,'andiRes' => $andiResponse);
     }
     
     
     
    
   

    protected function addPaypal($args) {

        if ($args['ent_code'] == '')
            return $this->_getStatusMessage(1, 'code');

        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], '2');

        if (is_array($returned))
            return $returned;

        $paypal = new paypal();

        $refreshToken = $paypal->get_refresh_token($args);

        if (!empty($refreshToken['error'])) {
            return $this->_getStatusMessage(103, 1);
        } else {
            $updateDataQry = "update slave set paypal_token = '" . $refreshToken['refresh_token'] . "' where slave_id = '" . $this->User['entityId'] . "'";
            mysql_query($updateDataQry, $this->db->conn);
            if (mysql_affected_rows() > 0)
                return $this->_getStatusMessage(102, $refreshToken);
            else
                return $this->_getStatusMessage(103, 1);
        }
    }

    protected function removePaypal($args) {

        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], '2');

        if (is_array($returned))
            return $returned;

//        $paypal = new paypal();
//
//        $refreshToken = $paypal->get_refresh_token($args);
//
//        if (!empty($refreshToken['error'])) {
//            return $this->_getStatusMessage(103, 1);
//        } else {
        $updateDataQry = "update slave set paypal_token = '' where slave_id = '" . $this->User['entityId'] . "'";
        mysql_query($updateDataQry, $this->db->conn);
        if (mysql_affected_rows() >= 0)
            return $this->_getStatusMessage(105, 1);
        else
            return $this->_getStatusMessage(106, 1);
//        }
    }

    protected function getVerificationCode($args) {

        if ($args['ent_mobile'] == '')
            return $this->_getStatusMessage(1, 1);

        $checkMobileQry = "select * from slave where phone = '" . $args['ent_mobile'] . "'";
        $checkMobileRes = mysql_query($checkMobileQry, $this->db->conn);
//
////        return $this->_getStatusMessage(113, $checkMobileQry);
        if (mysql_num_rows($checkMobileRes) > 0)
            return $this->_getStatusMessage(113, $checkMobileQry);

        $rand = $args['ent_rand'] = rand(10000, 99999); //11111
        $resutl = $this->mobileVerification($args);

//        if ($resutl['errNo'] == 1)
//            return array('errNum' => 500, 'errFlag' => 0, 'errMsg' => $resutl['errMsg']);

        $location = $this->mongo->selectCollection('verification');
//
        if (is_array($location->findOne(array('mobile' => $args['ent_mobile']))))
            $location->update(array('mobile' => $args['ent_mobile']), array('$set' => array('code' => (int) $rand, 'ts' => time())));
        else
            $location->insert(array('mobile' => $args['ent_mobile'], 'code' => (int) $rand, 'ts' => time()));

//        if ($signup !== NULL)
        return $this->_getStatusMessage(107, 1);
    }

    protected function mobileVerification($args, $test = NULL, $message = NULL) {
        $account_sid = ''; 
        $auth_token = '';
        $client = new Services_Twilio($account_sid, $auth_token);

        if ($test == NULL) {
            $message = "Thank you for registering with " . APP_NAME . ". Your confirmation code is " . $args['ent_rand'] . ".";
        }

        try {
            $message = $client->account->messages->create(array(
                'To' => $args['ent_mobile'],
                'From' => "",
                'Body' => $message
            ));
        } catch (Exception $e) {  //on error push userId in to error array
            $notifications = $this->mongo->selectCollection('mobilestatuerror');
            $notifications->insert(array('data' => $e->getMessage()));
        }

        return true;
    }

    protected function checkMobile($args) {
        if ($args['ent_mobile'] == '')
            return $this->_getStatusMessage(1, 'Mobile number');
        else if ($args['ent_user_type'] == '')
            return $this->_getStatusMessage(1, 'User type');

        if ($args['ent_user_type'] == '2') {
            $table = "slave";
            $field = "phone";
        } else {
            $table = "master";
            $field = "mobile";
        }

        $checkMobileQry = "select status from $table where $field = '" . $args['ent_mobile'] . "'";
        $checkMobileRes = mysql_query($checkMobileQry, $this->db->conn);

        if (mysql_num_rows($checkMobileRes) > 0)
            return $this->_getStatusMessage(113, 1);
        else
            return $this->_getStatusMessage(112, 1);
    }

    protected function verifyPhone($args) {

        if ($args['ent_phone'] == '')
            return $this->_getStatusMessage(1, 'Mobile number');
        else if ($args['ent_code'] == '')
            return $this->_getStatusMessage(1, 'Code');

        $notifications = $this->mongo->selectCollection('notifications');

        $notifications->insert($args);

//        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], '2', '1');
//
//        if (is_array($returned))
//            return $returned;

        $location = $this->mongo->selectCollection('verification');
        if (is_array($location->findOne(array('mobile' => $args['ent_phone'], 'code' => (int) $args['ent_code']))) || $args['ent_code'] == '11111') {
//            $updateQry = "update slave status = 3 where slave_id = '" . $this->User['entityId'] . "'";
//            mysql_query($updateQry, $this->db->conn);
            return $this->_getStatusMessage(109, 1);
        } else {
            return $this->_getStatusMessage(110, 1);
        }
    }

    /*              ----------------                SERVICE METHODS             ---------------------               */
    /*
     * Method name: masterSignup1
     * Desc: Driver Sign up for the app step 1
     * Input: Request data
     * Output: Success flag with data array if completed successfully, else data array with error flag
     */

    protected function masterSignup1($args) {

        if ($args['ent_first_name'] == '')
            return $this->_getStatusMessage(1, 'First name');
        else if ($args['ent_email'] == '')
            return $this->_getStatusMessage(1, 'Email');
        else if ($args['ent_password'] == '')
            return $this->_getStatusMessage(1, 'Password');
        else if ($args['ent_mobile'] == '')
            return $this->_getStatusMessage(1, 'Mobile number');
        else if ($args['ent_dev_id'] == '')
            return $this->_getStatusMessage(1, 'Device id');
        else if ($args['ent_device_type'] == '')
            return $this->_getStatusMessage(1, 'Device type');
        else if ($args['ent_date_time'] == '')
            return $this->_getStatusMessage(1, 'Date time');

        $this->curr_date_time = urldecode($args['ent_date_time']);

        $devTypeNameArr = $this->_getDeviceTypeName($args['ent_device_type']);

        if (!$devTypeNameArr['flag'])
            return $this->_getStatusMessage(4, 4); //_getStatusMessage($errNo, $test_num);

        $verifyEmail = $this->_verifyEmail($args['ent_email'], 'mas_id', 'master'); //_verifyEmail($email,$field,$table);

        if (is_array($verifyEmail))
            return $this->_getStatusMessage(2, 2); //_getStatusMessage($errNo, $test_num);

        $args['ent_first_name'] = ucfirst($args['ent_first_name']);
//        $args['ent_last_name'] = ($args['ent_last_name'] == '') ? 'Lastname' : ucfirst($args['ent_last_name']);
//        if ($args['ent_comp_id'] == '0') {
//            $insertCompanyQry = "insert into company_info(companyname,vat_number,Status) values('" . $args['ent_comp_name'] . "','" . $args['ent_tax_num'] . "','5')";
//            mysql_query($insertCompanyQry, $this->db->conn);
//            $comp_id = mysql_insert_id();
//        } else {
//        $comp_id = $args['ent_comp_id'];
//        }

        $insertMasterQry = "
                        insert into 
                        master(first_name,last_name,email,password,mobile,
                        zipcode,created_dt,last_active_dt,status) 
                        values('" . $args['ent_first_name'] . "','" . $args['ent_last_name'] . "','" . $args['ent_email'] . "',md5('" . $args['ent_password'] . "'),'" . $args['ent_mobile'] . "',
                            '" . $args['ent_zipcode'] . "','" . $this->curr_date_time . "','" . $this->curr_date_time . "','1')"; //,'" . $comp_id . "'

        mysql_query($insertMasterQry, $this->db->conn);
//echo $insertMasterQry;
        if (mysql_error($this->db->conn) != '')
            return $this->_getStatusMessage(3, $insertMasterQry); //_getStatusMessage($errNo, $test_num);

        $newDriver = mysql_insert_id($this->db->conn);

        if ($newDriver <= 0)
            return $this->_getStatusMessage(3, 4); //_getStatusMessage($errNo, $test_num);

        $location = $this->mongo->selectCollection('location');

        $curr_gmt_date = new MongoDate(strtotime($this->curr_date_time));

        $mongoArr = array("type" => 0, "user" => (int) $newDriver, "name" => $args['ent_first_name'], "lname" => $args['ent_last_name'],
            "location" => array(
                "longitude" => (double) $args['ent_longitude'],
                "latitude" => (double) $args['ent_latitude']
            ), "image" => "", "rating" => 0, 'status' => 11, 'email' => strtolower($args['ent_email']), 'dt' => $curr_gmt_date->sec, 'chn' => 'qd_' . $args['ent_dev_id'], 'listner' => 'qdl_' . $args['ent_dev_id'], 'carId' => 0
        );

        $location->insert($mongoArr);

        $createRecipientArr = array('name' => $args['ent_first_name'] . ' ' . $args['ent_last_name'], 'type' => 'individual', 'email' => $args['ent_email'], 'tax_id' => '000000000', 'account_number' => '000123456789', 'routing_number' => '110000000', 'country' => 'US', 'description' => 'For ' . $args['ent_email']);

        $recipient = $this->stripe->apiStripe('createRecipient', $createRecipientArr);

        if ($recipient['error']) {
            $cardRes = array('errFlag' => 1, 'errMsg' => $recipient['error']['message']);
        } else {
            $updateQry = "update master set stripe_id = '" . $recipient['id'] . "' where mas_id = '" . $newDriver . "'";
            mysql_query($updateQry, $this->db->conn);

            if (mysql_affected_rows() <= 0)
                $cardRes = $this->_getStatusMessage(3, 50);
            else
                $cardRes = array('errFlag' => 0, 'errMsg' => 'Recipient created');
        }

        //   $mail = new mailGun(APP_SERVER_HOST);
        $mail = new sendAMail(APP_SERVER_HOST);
        $mailArr = $mail->sendMasWelcomeMail($args['ent_email'], $args['ent_first_name']);

//        $coupon = $this->_createCoupon();
//
//        $couponData = array('id' => $coupon, 'duration' => 'forever', 'percent_off' => 15);
//
//        $couponRes = $this->stripe->apiStripe('createCoupon', $couponData);
//
//        $insertCouponQry = "insert into coupon values ('" . $couponRes['id'] . "','" . $newDriver . "','1','15',0)";
//        mysql_query($insertCouponQry, $this->db->conn);
//        $AmazonSns = new AwsPush();
//        $res = $AmazonSns->createPlatformEndpoint($args['ent_push_token']);

        $createSessArr = $this->_checkSession($args, $newDriver, '1', $devTypeNameArr['name'], null); // ($carRow['workplace_id'] == $driverRow['workplace_id']) ? NULL : array('workplaceId' => $driverRow['workplace_id'], 'lat' => $args['ent_lat'], 'lng' => $args['ent_long'])); //_checkSession($args, $oid, $user_type);


        /* createSessToken($obj_id, $dev_name, $mac_addr, $push_token); */
//        $createSessArr = $token_obj->createSessToken($newDriver, $devTypeNameArr['name'], $args['ent_dev_id'], $res['EndpointArn'], '1', $this->curr_date_time);

        $errMsgArr = $this->_getStatusMessage(12, 5); //_getStatusMessage($errNo, $test_num);
        return array('errNum' => $errMsgArr['errNum'], 'errFlag' => $errMsgArr['errFlag'], 'errMsg' => $errMsgArr['errMsg'], 'data' => array('token' => $createSessArr['Token'], 'expiryLocal' => $createSessArr['Expiry_local'],'presenseChn' => presenseChn, 'expiryGMT' => $createSessArr['Expiry_GMT'], 'flag' => $createSessArr['Flag'], 'joined' => $this->curr_date_time, 'chn' => APP_PUBNUB_CHANNEL, 'email' => $args['ent_email'], 'mFlg' => $mailArr['flag'], 'susbChn' => 'qd_' . $args['ent_dev_id'], 'listner' => 'qdl_' . $args['ent_dev_id'], 'pub' => PUBNUB_PUBLISH_KEY, 'sub' => PUBNUB_SUBSCRIBE_KEY));
    }

    /*
     * Method name: masterLogin
     * Desc: Driver login on the app
     * Input: Request data
     * Output:  Success flag with data array if completed successfully, else data array with error flag
     */

    protected function masterLogin($args) {

//        $notifications = $this->mongo->selectCollection('getuserdata');
//        $notifications->insert($args);

        if ($args['ent_email'] == '')
            return $this->_getStatusMessage(1, 'Email');
        else if ($args['ent_password'] == '')
            return $this->_getStatusMessage(1, 'Password');
        else if ($args['ent_dev_id'] == '')
            return $this->_getStatusMessage(1, 'Device id');
        else if ($args['ent_device_type'] == '')
            return $this->_getStatusMessage(1, 'Device type');
        else if ($args['ent_date_time'] == '')
            return $this->_getStatusMessage(1, 'Date time');
        else if ($args['ent_lat'] == '' || $args['ent_long'] == '')
            return $this->_getStatusMessage(1, 'Location');
        else if ($args['ent_lang'] == '' && $args['ent_device_type'] == 2)
            return $this->_getStatusMessage(1, 'Language');

        $this->User['lang'] = $args['ent_lang'];



        $this->curr_date_time = urldecode($args['ent_date_time']);

        $devTypeNameArr = $this->_getDeviceTypeName($args['ent_device_type']);

        
        
        if (!$devTypeNameArr['flag'])
            return $this->_getStatusMessage(5, 108);

        
        $location = $this->mongo->selectCollection('location');
        $masterDet = $location->findOne(array('email' => $args['ent_email']));
        if(!is_array($masterDet)){
              $errMsgArr = $this->_getStatusMessage(127, 'email'); //_getStatusMessage($errNo, $test_num);
              return array('errNum' => $errMsgArr['errNum'], 'errFlag' => $errMsgArr['errFlag'], 'errMsg' => $errMsgArr['errMsg'].' '.APP_NAME);
        }
        
        $searchDriverQry = "select md5('" . $args['ent_password'] . "') as given_password,m.mobile,m.password,m.company_id,m.mas_id,m.profile_pic,m.first_name,m.last_name,m.created_dt,m.license_num,m.status,m.workplace_id,(select status from company_info where company_id = m.company_id) as company_status from master m where m.email = '" . $args['ent_email'] . "'";
        $searchDriverRes = mysql_query($searchDriverQry, $this->db->conn);
//echo $searchDriverQry;

        if (mysql_num_rows($searchDriverRes) <= 0)
            return $this->_getStatusMessage(8, $searchDriverQry); //_getStatusMessage($errNo, $test_num);

        $driverRow = mysql_fetch_assoc($searchDriverRes);

        if ($driverRow['password'] !== $driverRow['given_password'])
            return $this->_getStatusMessage(117, 18); //_getStatusMessage($errNo, $test_num);

        if ($driverRow['status'] == '2' || $driverRow['status'] == '1')
            return $this->_getStatusMessage(10, 17); //_getStatusMessage($errNo, $test_num);

        if ($driverRow['company_status'] != '3')
            return $this->_getStatusMessage(92, 17); //_getStatusMessage($errNo, $test_num);

        if ($driverRow['status'] == '4')
            return $this->_getStatusMessage(79, 79); //_getStatusMessage($errNo, $test_num);


        $checkCarAvailabilityQry = "select w.type_id,w.Status,w.company,w.workplace_id,(select MapIcon from workplace_types where type_id = w.type_id) as type_icon,(select type_name from workplace_types where type_id = w.type_id) as type_Name,(select city_id from workplace_types where type_id = w.type_id) as cityid from workplace w where w.uniq_identity = '" . $args['ent_car_id'] . "'";
        $checkCarAvailabilityRes = mysql_query($checkCarAvailabilityQry, $this->db->conn);

        if (mysql_num_rows($searchDriverRes) <= 0)
            return $this->_getStatusMessage(8, $searchDriverQry); //_getStatusMessage($errNo, $test_num);

        $carRow = mysql_fetch_assoc($checkCarAvailabilityRes);

        if ($carRow['company'] != $driverRow['company_id'])
            return $this->_getStatusMessage(77, 77); //_getStatusMessage($errNo, $test_num);

        if ($carRow['Status'] == '4')
            return $this->_getStatusMessage(121, 76); //_getStatusMessage($errNo, $test_num);
 
       
        $vehicleid = $location->findOne(array('carId' =>(int)$carRow['workplace_id'],"user" => array('$ne' => (int) $driverRow['mas_id']) ));
        if(is_array($vehicleid)){
            return $this->_getStatusMessage(126, 76);
        }
        
        
        
        if ($carRow['workplace_id'] != $driverRow['workplace_id']) {
             
           if ($carRow['Status'] != '2')
            return $this->_getStatusMessage(77, 76); //_getStatusMessage($errNo, $test_num);

            $updateCarIdForDriverQry = "update master set workplace_id = '" . $carRow['workplace_id'] . "',type_id='" . $carRow['type_id'] . "' where mas_id = '" . $driverRow['mas_id'] . "'";
            mysql_query($updateCarIdForDriverQry, $this->db->conn);

            if (mysql_affected_rows() < 0)
                return $this->_getStatusMessage(3, 17); //_getStatusMessage($errNo, $test_num);

            $location->update(array('user' => (int) $driverRow['mas_id']), array('$set' => array('type' => (int) $carRow['type_id'], 'carId' => (int) $carRow['workplace_id'], 'chn' => 'qd_' . $args['ent_dev_id'], 'listner' => 'qdl_' . $args['ent_dev_id'])));

            $updateCarStatusQry = "update workplace set Status = 1,last_login_lat = '" . $args['ent_lat'] . "',last_login_long = '" . $args['ent_long'] . "' where workplace_id = '" . $carRow['workplace_id'] . "'";
            mysql_query($updateCarStatusQry, $this->db->conn);

            $updatePrevCarStatusQry = "update workplace set Status = 2,last_login_lat = '" . $args['ent_lat'] . "',last_login_long = '" . $args['ent_long'] . "' where workplace_id = '" . $driverRow['workplace_id'] . "'";
            mysql_query($updatePrevCarStatusQry, $this->db->conn);
        }

        mysql_query("update user_sessions set loggedIn = 2 where device = '" . $args['ent_dev_id'] . "' and user_type = 1 and oid != '" . $driverRow['mas_id'] . "'", $this->db->conn);
        mysql_query("update user_sessions set loggedIn = 3 where device != '" . $args['ent_dev_id'] . "' and user_type = 1 and oid = '" . $driverRow['mas_id'] . "'", $this->db->conn);
        /*
         * Sending last workplace id in an array for the current user, if he is logged in this car, then that will be freed for others
         */

        if ($args['ent_device_type'] == 2) {
            $getApptStatusQry = "update master set lang = '" . $args['ent_lang'] . "' where slave_id = '" . $driverRow['mas_id'] . "'";
            mysql_query($getApptStatusQry, $this->db->conn);
        }

        $sessDet = $this->_checkSession($args, $driverRow['mas_id'], '1', $devTypeNameArr['name'], null); // ($carRow['workplace_id'] == $driverRow['workplace_id']) ? NULL : array('workplaceId' => $driverRow['workplace_id'], 'lat' => $args['ent_lat'], 'lng' => $args['ent_long'])); //_checkSession($args, $oid, $user_type);

        $location->update(array('chn' => 'qd_' . $args['ent_dev_id']), array('$set' => array('chn' => '', 'listner' => '')));

        $location->update(array('user' => (int) $driverRow['mas_id']), array('$set' => array('type' => (int) $carRow['type_id'], 'carId' => (int) $carRow['workplace_id'], 'chn' => 'qd_' . $args['ent_dev_id'], 'listner' => 'qdl_' . $args['ent_dev_id'], 'status' => 4)));

        $errMsgArr = $this->_getStatusMessage(9, 8);
        return array('errNum' => $errMsgArr['errNum'], 'errFlag' => $errMsgArr['errFlag'], 'errMsg' => $errMsgArr['errMsg'], 'data' => array('token' => $sessDet['Token'], 'expiryLocal' => $sessDet['Expiry_local'], 'expiryGMT' => $sessDet['Expiry_GMT'], 'fname' => $driverRow['first_name'],'presenseChn' => presenseChn, 'lname' => $driverRow['last_name'], 'profilePic' => $driverRow['profile_pic'], 'medicalLicenseNum' => $driverRow['license_num'], 'flag' => $sessDet['Flag'], 'joined' => $driverRow['created_dt'], 'email' => $args['ent_email'], 'susbChn' => 'qd_' . $args['ent_dev_id'], 'chn' => APP_PUBNUB_CHANNEL, 'listner' => 'qdl_' . $args['ent_dev_id'], 'status' => $masterDet['status'],'driverid' => $driverRow["mas_id"], 'vehTypeId' => ($carRow['type_id'] != '' ? $carRow['type_id'] : $masterDet['type']), 'cityid' => $carRow['cityid'], 'carType' => $carRow['type_Name'], 'typeImage' => $carRow['type_icon'], 'pub' => PUBNUB_PUBLISH_KEY, 'sub' => PUBNUB_SUBSCRIBE_KEY,'phone' => $driverRow['mobile']));
    }

    /*
     * Method name: slaveSignup
     * Desc: Passenger signup
     * Input: Request data
     * Output:  Success flag with data array if completed successfully, else data array with error flag
     */

    protected function slaveSignup($args) {

//        $notifications = $this->mongo->selectCollection('notifications');
//
//        $notifications->insert($args);

        if ($args['ent_first_name'] == '')
            return $this->_getStatusMessage(1, 'First name');
        else if ($args['ent_email'] == '')
            return $this->_getStatusMessage(1, 'Email');
        else if ($args['ent_password'] == '')
            return $this->_getStatusMessage(1, 'Password');
        else if ($args['ent_mobile'] == '')
            return $this->_getStatusMessage(1, 'Mobile');
        else if ($args['ent_dev_id'] == '')
            return $this->_getStatusMessage(1, 'Device id');
        else if ($args['ent_device_type'] == '')
            return $this->_getStatusMessage(1, 'Device type');
        else if ($args['ent_date_time'] == '')
            return $this->_getStatusMessage(1, 'Date time');

      
                
        $this->curr_date_time = urldecode($args['ent_date_time']);

        if ($this->curr_date_time == '0000-00-00 00:00:00')
            $this->curr_date_time = date("Y-m-d H:i:s", time());

        if ($args['ent_terms_cond'] == '0')
            return $this->_getStatusMessage(14, 14); //_getStatusMessage($errNo, $test_num);

        if ($args['ent_pricing_cond'] == '0')
            return $this->_getStatusMessage(15, 15); //_getStatusMessage($errNo, $test_num);

        if ($args['ent_latitude'] == '')
            $args['ent_latitude'] = 0;

        if ($args['ent_longitude'] == '')
            $args['ent_longitude'] = 0;

        if ($args['ent_referral_code'] != '' && ((double) $args['ent_latitude'] < 0 || (double) $args['ent_longitude'] < 0))
            return $this->_getStatusMessage(120, 4);

        $devTypeNameArr = $this->_getDeviceTypeName($args['ent_device_type']);

        if (!$devTypeNameArr['flag'])
            return $this->_getStatusMessage(4, 4); //_getStatusMessage($errNo, $test_num);

        $checkMobileQry = "select status from slave where phone = '" . $args['ent_mobile'] . "'";
        $checkMobileRes = mysql_query($checkMobileQry, $this->db->conn);

        if (mysql_num_rows($checkMobileRes) > 0)
            return $this->_getStatusMessage(113, 1);

        $verifyEmail = $this->_verifyEmail($args['ent_email'], 'slave_id', 'slave'); //_verifyEmail($email,$field,$table);

        if (is_array($verifyEmail))
            return $this->_getStatusMessage(2, 2); //_getStatusMessage($errNo, $test_num);

        $carTypes = $this->getWorkplaceTypes($args['ent_city'], $args['ent_latitude'], $args['ent_longitude']);

//        if (!is_array($carTypes))
//            return $this->_getStatusMessage(80, 80);

        if ($args['ent_referral_code'] != '') {
            $resultArr = $this->mongo->selectCollection('$cmd')->findOne(array(
                'geoNear' => 'coupons',
                'near' => array(
                    (double) $args['ent_longitude'], (double) $args['ent_latitude']
                ), 'spherical' => true, 'maxDistance' => 10000000 / 6378137, 'distanceMultiplier' => 6378137,
                'query' => array('status' => 0, 'coupon_code' => 'REFERRAL', 'coupon_code' => (string) $args['ent_referral_code']))
            );

            if (count($resultArr['results']) <= 0) {
                return $this->_getStatusMessage(100, 2);
            }
        }

        $insertSlaveQry = "
                        insert into 
                        slave(first_name,last_name,email,password,phone,zipcode,status,
                        created_dt,last_active_dt,latitude,longitude) 
                        values('" . $args['ent_first_name'] . "','" . $args['ent_last_name'] . "','" . $args['ent_email'] . "',md5('" . $args['ent_password'] . "'),'" . $args['ent_mobile'] . "','" . $args['ent_zipcode'] . "','3',
                                '" . $this->curr_date_time . "','" . $this->curr_date_time . "','" . $args['ent_latitude'] . "','" . $args['ent_longitude'] . "')";

        mysql_query($insertSlaveQry, $this->db->conn);

        if (mysql_error($this->db->conn) != '')
            return $this->_getStatusMessage(3, $insertSlaveQry); //_getStatusMessage($errNo, $test_num);

        $newPassenger = mysql_insert_id($this->db->conn);

        if ($newPassenger <= 0)
            return $this->_getStatusMessage(3, 3); //_getStatusMessage($errNo, $test_num);

        $cardRes = array('errFlag' => 1, 'errMsg' => 'Card not added', 'errNum' => 16);

        if ($args['ent_token'] != '') {

            $createCustomerArr = array('token' => $args['ent_token'], 'email' => $args['ent_email']);

            $customer = $this->stripe->apiStripe('createCustomer', $createCustomerArr);

            if ($customer['error']) {
                $cardRes = array('errFlag' => 1, 'errMsg' => $customer['error']['message'], 'errNum' => 16);
            } else {
                $updateQry = "update slave set stripe_id = '" . $customer['id'] . "' where slave_id = '" . $newPassenger . "'";
                mysql_query($updateQry, $this->db->conn);
                if (mysql_affected_rows() <= 0)
                    $cardRes = $this->_getStatusMessage(3, 50);
                else {

                    $getCardArr = array('stripe_id' => $customer['id']);

                    $card = $this->stripe->apiStripe('getCustomer', $getCardArr);

                    if ($card['error'])
                        $cardRes = array(); //'errNum' => 16, 'errFlag' => 1, 'errMsg' => $card['error']['message'], 'test' => 2);

                    foreach ($card['sources']['data'] as $c) {
                        $cardRes = array('errFlag' => 0, 'id' => $c['id'], 'last4' => $c['last4'], 'type' => $c['brand'], 'exp_month' => $c['exp_month'], 'exp_year' => $c['exp_year']);
                    }
                }
//                $cardError = array('id' => $customer['data']['id'], 'last4' => $customer['data']['last4'], 'type' => $customer['data']['type'], 'exp_month' => $customer['data']['exp_month'], 'exp_year' => $customer['data']['exp_year']);
            }
        }

        $referralUsageMsg = $couponId = "";
        $mailArr = array();
        $mail = new sendAMail(APP_SERVER_HOST);






        $couponsColl = $this->mongo->selectCollection('coupons');

        $couponsColl->ensureIndex(array('location' => '2d'));

        $resultArr = $this->mongo->selectCollection('$cmd')->findOne(array(
            'geoNear' => 'coupons',
            'near' => array(
                (double) $args['ent_longitude'], (double) $args['ent_latitude']
            ), 'spherical' => true, 'maxDistance' => 100000 / 6378137, 'distanceMultiplier' => 6378137,
            'query' => array('status' => 0, 'coupon_code' => 'REFERRAL'))
        );

        if (count($resultArr['results']) > 0) {

            $referralData = $resultArr['results'][0]['obj'];

            $friendDiscCode = $friendReferred = "";

            $coupon = $this->_createCoupon($couponsColl);

            if ($args['ent_referral_code'] != '') {

                $referralArr = $this->mongo->selectCollection('$cmd')->findOne(array(
                    'geoNear' => 'coupons',
                    'near' => array(
                        (double) $args['ent_longitude'], (double) $args['ent_latitude']
                    ), 'spherical' => true, 'maxDistance' => 100000 / 6378137, 'distanceMultiplier' => 6378137,
                    'query' => array('coupon_code' => (string) $args['ent_referral_code'], 'coupon_type' => 1, 'user_type' => 1, 'status' => 0))
                );

                if (count($referralArr['results']) > 0) {

                    $couponData = $referralArr['results'][0]['obj'];

                    $discountCoupon = $this->_createCoupon($couponsColl);

                    $friendReferred = $couponData['user_id'];

                    $insertArr = array(
                        "coupon_code" => $discountCoupon,
                        "coupon_type" => 3,
                        "start_date" => strtotime($this->curr_date_time),
                        "expiry_date" => strtotime($this->curr_date_time) + (10 * 30 * 24 * 60 * 60),
                        "discount_type" => $couponData['referral_discount_type'],
                        "discount" => $couponData['referral_discount'],
                        "message" => $couponData['message'],
                        "status" => 0,
                        "city_id" => $couponData['city_id'],
                        "location" => $couponData['location'],
                        "user_type" => 1,
                        "user_id" => (string) $couponData['user_id'],
                        "email" => $couponData['email'],
                        'created_ts' => strtotime($this->curr_date_time)
                    );

                    $couponsColl->insert($insertArr);

                    if ($insertArr['_id'] != '') {

                        $friendDiscCode = $this->_createCoupon($couponsColl);

                        $discCouponNewUser = array(
                            "coupon_code" => $friendDiscCode,
                            "coupon_type" => 3,
                            "start_date" => strtotime($this->curr_date_time),
                            "expiry_date" => strtotime($this->curr_date_time) + (10 * 30 * 24 * 60 * 60),
                            "discount_type" => $couponData['discount_type'],
                            "discount" => $couponData['discount'],
                            "message" => $couponData['message'],
                            "status" => 0,
                            "city_id" => $couponData['city_id'],
                            "location" => $couponData['location'],
                            "user_type" => 1,
                            "user_id" => (string) $newPassenger,
                            "email" => $args['ent_email'],
                            'created_ts' => strtotime($this->curr_date_time)
                        );

                        $couponsColl->insert($discCouponNewUser);

                        $mailArr[] = $mail->discountOnFriendSignup($couponData['email'], $couponData['first_name'], array('code' => $discountCoupon, 'discountData' => $couponData, 'uname' => $args['ent_first_name'])); //$couponId
//                        $friendDiscCode = $this->_createCoupon($couponsColl);

                        $cond = array('_id' => $referralData['_id'], 'signups.coupon_code' => $args['ent_referral_code']);

                        $push = array('$push' => array('signups.$.discounts' => array('discount_code' => $discountCoupon, 'signedUpUser' => array('slave_id' => $newPassenger, 'discount_code' => $friendDiscCode, 'email' => $args['ent_email'], 'referral' => $coupon, 'created_ts' => strtotime($this->curr_date_time)))));

                        $couponsColl->update($cond, $push);
                    } else {
                        $error = $this->_getStatusMessage(100, 100);
                        $referralUsageMsg = $error['errMsg'];
                    }
                } else {
                    $error = $this->_getStatusMessage(100, 2);
                    $referralUsageMsg = $error['errMsg'];
                }
            }

            $insertReferralArr = array(
                "coupon_code" => $coupon,
                "coupon_type" => 1,
                "start_date" => strtotime($this->curr_date_time),
                "expiry_date" => strtotime($this->curr_date_time) + (10 * 365 * 24 * 60 * 60),
                "discount_type" => $referralData['discount_type'],
                "discount" => $referralData['discount'],
                "referral_discount_type" => $referralData['referral_discount_type'],
                "referral_discount" => $referralData['referral_discount'],
                "message" => $referralData['message'],
                "status" => 0,
                "city_id" => $referralData['city_id'],
                "currency" => $referralData['currency'],
                "location" => $referralData['location'],
                "user_type" => 1,
                "user_id" => $newPassenger,
                "email" => $args['ent_email'],
                'created_ts' => strtotime($this->curr_date_time)
            );

            $data = array('coupon_code' => $coupon, 'slave_id' => $newPassenger, 'email' => $args['ent_email'], 'fname' => $args['ent_first_name'], 'created_dt' => time());

            $couponsColl->update(array('_id' => $referralData['_id']), array('$push' => array('signups' => $data)));

            $couponsColl->insert($insertReferralArr);

            if ($insertReferralArr['_id'] > 0) {
//                    $referralCode = array('code' => $coupon, 'referralData' => $referralData);
                $couponId = $coupon;

                $updateCouponQry = "update slave set coupon = '" . $coupon . "' where slave_id = '" . $newPassenger . "'";
                mysql_query($updateCouponQry, $this->db->conn);
                $mailArr[] = $mail->sendDiscountCoupon($args['ent_email'], $args['ent_first_name'], array('code' => $friendDiscCode, 'refCoupon' => $coupon, 'discountData' => $referralData)); //$couponId
            }
        } else {
            $mailArr[] = $mail->sendSlvWelcomeMail($args['ent_email'], $args['ent_first_name']); //$couponId
//            echo $checkReferralAvailability;
        }













//        $checkReferralAvailability = "select cp.*,(select Currency from city where city_id = cp.city_id) as currency from coupons cp where cp.coupon_code = 'REFERRAL' and cp.coupon_type = 1 and cp.user_type = 2 and cp.status = 0 and cp.city_id in (select ca.City_Id from city_available ca where (3956 * acos( cos( radians('" . $args['ent_latitude'] . "') ) * cos( radians(ca.City_Lat) ) * cos( radians(ca.City_Long) - radians('" . $args['ent_longitude'] . "') ) + sin( radians('" . $args['ent_latitude'] . "') ) * sin( radians(ca.City_Lat) ) ) ) <= " . $this->promoCodeRadius . ")";
//
//        $referralRes = mysql_query($checkReferralAvailability, $this->db->conn);
//
//        if (mysql_num_rows($referralRes) > 0) {
//
//            $referralData = mysql_fetch_assoc($referralRes);
//
//            $discountCodeQry = $friendDiscCode = $friendReferred = "";
//
//            if ($args['ent_referral_code'] != '') {
//
//                $checkCouponQry = "select cp.*,sl.first_name,sl.email,(select Currency from city where city_id = cp.city_id) as currency from coupons cp,slave sl where cp.user_id = sl.slave_id and cp.coupon_code = '" . $args['ent_referral_code'] . "' and cp.coupon_type = 1 and cp.user_type = 1 and cp.status = 0 and cp.city_id in (select ca.City_Id from city_available ca where (3956 * acos( cos( radians('" . $args['ent_latitude'] . "') ) * cos( radians(ca.City_Lat) ) * cos( radians(ca.City_Long) - radians('" . $args['ent_longitude'] . "') ) + sin( radians('" . $args['ent_latitude'] . "') ) * sin( radians(ca.City_Lat) ) ) ) <= " . $this->promoCodeRadius . ") limit 0,1";
//                $checkCouponRes = mysql_query($checkCouponQry, $this->db->conn);
//
//                if (mysql_num_rows($checkCouponRes) > 0) {
//                    $couponData = mysql_fetch_assoc($checkCouponRes);
//
//                    $discountCoupon = $this->_createCoupon();
//
//                    $friendReferred = $couponData['user_id'];
//
//                    $insertCouponQry1 = "insert into coupons(coupon_code,start_date,expiry_date,coupon_type,discount_type,discount,message,city_id,user_id,user_type,referral_campaign_id,referred_user_id,create_type) values "
//                            . " ('" . $discountCoupon . "','" . date('Y-m-d') . "','" . date('Y-m-d', strtotime('+' . PROMOCODE_CODE_EXPIRY_MONTHS . ' days', time())) . "','3','" . $couponData['referral_discount_type'] . "','" . $couponData['referral_discount'] . "','" . $couponData['message'] . "','" . $couponData['city_id'] . "','" . $couponData['user_id'] . "','1','" . $referralData['id'] . "','" . $newPassenger . "','0')";
//                    mysql_query($insertCouponQry1, $this->db->conn);
////echo $insertCouponQry1;
//                    if (mysql_insert_id() > 0) {
//                        $mailArr[] = $mail->discountOnFriendSignup($couponData['email'], $couponData['first_name'], array('code' => $discountCoupon, 'discountData' => $couponData, 'uname' => $args['ent_first_name'])); //$couponId
//
//                        $friendDiscCode = $this->_createCoupon();
//
//                        $discountCodeQry = ",('" . $friendDiscCode . "','" . date('Y-m-d', time()) . "','" . date('Y-m-d', strtotime('+' . PROMOCODE_CODE_EXPIRY_MONTHS . ' days', time())) . "','3','" . $referralData['discount_type'] . "','" . $referralData['discount'] . "','0','0','" . $referralData['message'] . "','" . $referralData['city_id'] . "','" . $newPassenger . "','1','" . $referralData['id'] . "','2','" . $couponData['slave_id'] . "')";
//                    } else {
//                        $error = $this->_getStatusMessage(100, 100);
//                    }
//                } else {
//                    $error = $this->_getStatusMessage(100, $checkCouponQry);
//                    $referralUsageMsg = $error['errMsg'];
//                }
//            }
//
//            $coupon = $this->_createCoupon();
//
//            if ($friendReferred == '')
//                $insertCouponQry = "insert into coupons(coupon_code,start_date,expiry_date,coupon_type,discount_type,discount,referral_discount_type,referral_discount,message,city_id,user_id,user_type,referral_campaign_id) "
//                        . "values ('" . $coupon . "','" . date('Y-m-d', time()) . "','" . date('Y-m-d', strtotime('+' . REFERRAL_CODE_EXPIRY_MONTHS . ' months', time())) . "','1','" . $referralData['discount_type'] . "','" . $referralData['discount'] . "','" . $referralData['referral_discount_type'] . "','" . $referralData['referral_discount'] . "','" . $referralData['message'] . "','" . $referralData['city_id'] . "','" . $newPassenger . "','1','" . $referralData['id'] . "')";
//            else
//                $insertCouponQry = "insert into coupons(coupon_code,start_date,expiry_date,coupon_type,discount_type,discount,referral_discount_type,referral_discount,message,city_id,user_id,user_type,referral_campaign_id,create_type,referred_user_id) "
//                        . "values ('" . $coupon . "','" . date('Y-m-d', time()) . "','" . date('Y-m-d', strtotime('+' . REFERRAL_CODE_EXPIRY_MONTHS . ' months', time())) . "','1','" . $referralData['discount_type'] . "','" . $referralData['discount'] . "','" . $referralData['referral_discount_type'] . "','" . $referralData['referral_discount'] . "','" . $referralData['message'] . "','" . $referralData['city_id'] . "','" . $newPassenger . "','1','" . $referralData['id'] . "','1','" . $friendReferred . "')" . $discountCodeQry;
//
//            mysql_query($insertCouponQry, $this->db->conn);
////echo $insertCouponQry;
//            if (mysql_affected_rows() > 0) {
////                    $referralCode = array('code' => $coupon, 'referralData' => $referralData);
//                $couponId = $coupon;
//                $mailArr[] = $mail->sendDiscountCoupon($args['ent_email'], $args['ent_first_name'], array('code' => $friendDiscCode, 'refCoupon' => $coupon, 'discountData' => $referralData)); //$couponId
//            }
//        } else {
//            $mailArr[] = $mail->sendSlvWelcomeMail($args['ent_email'], $args['ent_first_name'], $args); //$couponId
////            echo $checkReferralAvailability;
//        }

        /* createSessToken($obj_id, $dev_name, $mac_addr, $push_token); */
        $createSessArr = $this->_checkSession($args, $newPassenger, '2', $devTypeNameArr['name']); //$token_obj->createSessToken($newPassenger, $devTypeNameArr['name'], $args['ent_dev_id'], $args['ent_push_token'], '2');

        if ($args['ent_push_token'] == '')
            $errMsgArr = $this->_getStatusMessage(115, 8);
        else
            $errMsgArr = $this->_getStatusMessage(5, 8);
        
          $ClientmapKey = And_ClientmapKey;
            $ClientPlaceKey = And_ClientPlaceKey;
            if($args['ent_dev_id'] == 1){
                $ClientmapKey = Ios_ClientmapKey;
                $ClientPlaceKey = Ios_ClientPlaceKey;
                }
                
                

//        $errMsgArr = $this->_getStatusMessage(5, 5); //_getStatusMessage($errNo, $test_num);
        return array('errNum' => $errMsgArr['errNum'], 'errFlag' => $errMsgArr['errFlag'], 'errMsg' => $errMsgArr['errMsg'], 'coupon' => $couponId,'presenseChn' => presenseChn ,'stipeKey' => stipeKeyForApp,'ClientPlaceKey' => $ClientPlaceKey,'ClientmapKey' => $ClientmapKey,
            'token' => $createSessArr['Token'], 'expiryLocal' => $createSessArr['Expiry_local'], 'expiryGMT' => $createSessArr['Expiry_GMT'], 'email' => $args['ent_email'],
            'flag' => $createSessArr['Flag'], 'joined' => $this->curr_date_time, 'apiKey' => '2ee51574176b15c2e', 'card' => array($cardRes), 'mail' => $mailArr, 'types' => $carTypes, 'serverChn' => APP_PUBNUB_CHANNEL, 'chn' => 'qp_' . $args['ent_dev_id'],
            'noVehicleType' => strtoupper("Thank you for signing up! Unfortunately we are not in your city yet, please do send us an email at info@roadyo.net, if you will like us there!"), 'ref' => $error, 'pub' => PUBNUB_PUBLISH_KEY, 'sub' => PUBNUB_SUBSCRIBE_KEY);
    }

    /*
     * Method name: slaveLogin
     * Desc: Passenger login on the app
     * Input: Request data
     * Output:  Success flag with data array if completed successfully, else data array with error flag
     */

    protected function slaveLogin($args) {
        
        if ($args['ent_email'] == '')
            return $this->_getStatusMessage(1, 'Email');
        else if ($args['ent_password'] == '')
            return $this->_getStatusMessage(1, 'Password');
        else if ($args['ent_dev_id'] == '')
            return $this->_getStatusMessage(1, 'Device id');
        else if ($args['ent_device_type'] == '')
            return $this->_getStatusMessage(1, 'Device type');
        else if ($args['ent_date_time'] == '')
            return $this->_getStatusMessage(1, 'Date time');

        $this->curr_date_time = urldecode($args['ent_date_time']);

        $notifications = $this->mongo->selectCollection('notifications');

        $notifications->insert(array('args' => $args));

        $devTypeNameArr = $this->_getDeviceTypeName($args['ent_device_type']);

        if (!$devTypeNameArr['flag'])
            return $this->_getStatusMessage(5, 108);

        $carTypes = $this->getWorkplaceTypes($args['ent_city'], $args['ent_latitude'], $args['ent_longitude']);

        
//        if (count($carTypes) <= 0)
//            return $this->_getStatusMessage(80, 80);
//        if (!is_array($carTypes))
//            $carTypes = array();

        $searchPassengerQry = "select MD5('" . $args['ent_password'] . "') as given_password,p.password,p.slave_id,p.paypal_token as paypal,p.profile_pic,p.first_name,p.created_dt,p.status,p.stripe_id,(select coupon_code from coupons where user_type = 1 and status = 0 and coupon_type = 1 and user_id = p.slave_id) as coupon_id  from slave p where p.email = '" . $args['ent_email'] . "'";
        $searchPassengerRes = mysql_query($searchPassengerQry, $this->db->conn);

        if (mysql_num_rows($searchPassengerRes) <= 0)
            return $this->_getStatusMessage(8, 7); //_getStatusMessage($errNo, $test_num);

        $passengerRow = mysql_fetch_assoc($searchPassengerRes);

        if ($passengerRow['password'] !== $passengerRow['given_password'])
            return $this->_getStatusMessage(117, 18); //_getStatusMessage($errNo, $test_num);

        if ($passengerRow['status'] == '1' || $passengerRow['status'] == '4')
            return $this->_getStatusMessage(94, 18); //_getStatusMessage($errNo, $test_num);

        $cardsArr = array();
        if ($passengerRow['stripe_id'] != '') {

            $getCardArr = array('stripe_id' => $passengerRow['stripe_id']);

            $card = $this->stripe->apiStripe('getCustomer', $getCardArr);
            if ($card['error'])
                $cardsArr = array(); //array('errNum' => 16, 'errFlag' => 1, 'errMsg' => $card['error']['message'], 'test' => 2);
            else
                foreach ($card['sources']['data'] as $c) {
                    $cardsArr[] = array('id' => $c['id'], 'last4' => $c['last4'], 'type' => $c['brand'], 'exp_month' => $c['exp_month'], 'exp_year' => $c['exp_year']);
                }
        }

        $couponId = $passengerRow['coupon_id'];


        if ($couponId == '') {
            $couponsColl = $this->mongo->selectCollection('coupons');

            $cond = array(
                'geoNear' => 'coupons',
                'near' => array(
                    (double) $args['ent_longitude'], (double) $args['ent_latitude']
                ), 'spherical' => true, 'maxDistance' => 100000 / 6378137, 'distanceMultiplier' => 6378137,
                'query' => array('status' => 0, 'coupon_code' => 'REFERRAL'));

//            return $couponsColl->findOne(array('coupon_code'=> 'REFERRAL'));

            $resultArr = $this->mongo->selectCollection('$cmd')->findOne($cond);

            if (count($resultArr['results']) > 0) {

                $referralData = $resultArr['results'][0]['obj'];

                $coupon = $this->_createCoupon($couponsColl);

                $insertReferralArr = array(
                    "coupon_code" => $coupon,
                    "coupon_type" => 1,
                    "start_date" => time(),
                    "expiry_date" => time() + (10 * 365 * 24 * 60 * 60),
                    "discount_type" => $referralData['discount_type'],
                    "discount" => $referralData['discount'],
                    "referral_discount_type" => $referralData['referral_discount_type'],
                    "referral_discount" => $referralData['referral_discount'],
                    "message" => $referralData['message'],
                    "status" => 0,
                    "city_id" => $referralData['city_id'],
                    "location" => $referralData['location'],
                    "user_type" => 1,
                    "user_id" => $passengerRow['slave_id']
                );

                $data = array('referral_code' => $coupon, 'slave_id' => $passengerRow['slave_id'], 'email' => $args['ent_email'], 'fname' => $passengerRow['first_name']);

                $couponsColl->update(array('_id' => $referralData['_id']), array('$push' => array('signups' => $data)));

                $couponsColl->insert($insertReferralArr);

//echo $insertCouponQry;
                if ($insertReferralArr['_id'] > 0) {
//                    $referralCode = array('code' => $coupon, 'referralData' => $referralData);
                    $couponId = $coupon;

                    $updateCouponQry = "update slave set coupon = '" . $coupon . "' where slave_id = '" . $passengerRow['slave_id'] . "'";
                    mysql_query($updateCouponQry, $this->db->conn);
                }
            }
        }




        $sessDet = $this->_checkSession($args, $passengerRow['slave_id'], '2', $devTypeNameArr['name']); //_checkSession($args, $oid, $user_type);

        if ($args['ent_push_token'] == '')
            $errMsgArr = $this->_getStatusMessage(115, 8);
        else
            $errMsgArr = $this->_getStatusMessage(9, 8);
        
        
         $ClientmapKey = And_ClientmapKey;
            $ClientPlaceKey = And_ClientPlaceKey;
            if($args['ent_device_type'] == 1){
                $ClientmapKey = Ios_ClientmapKey;
                $ClientPlaceKey = Ios_ClientPlaceKey;
                }
                
        return array('query' => $searchPassengerQry, 'errNum' => $errMsgArr['errNum'], 'errFlag' => $errMsgArr['errFlag'], 'errMsg' => $errMsgArr['errMsg'], 'coupon' => $couponId,'presenseChn' => presenseChn ,'stipeKey' => stipeKeyForApp,'ClientPlaceKey' => $ClientPlaceKey,'ClientmapKey' => $ClientmapKey,
            'token' => $sessDet['Token'], 'expiryLocal' => $sessDet['Expiry_local'], 'expiryGMT' => $sessDet['Expiry_GMT'], 'email' => $args['ent_email'], 'paypal' => ($passengerRow['paypal'] == '' ? 1 : 2),
            'profilePic' => ($passengerRow['profile_pic'] == '') ? $this->default_profile_pic : $passengerRow['profile_pic'], 'flag' => $sessDet['Flag'], 'joined' => $passengerRow['created_dt'], 'apiKey' => '2ee51574176b15c2e', 'cards' => $cardsArr, 'types' => $carTypes, 'serverChn' => APP_PUBNUB_CHANNEL, 'chn' => 'qp_' . $args['ent_dev_id'],
            'noVehicleType' => strtoupper("Thank you for signing up! Unfortunately we are not in your city yet, please do send us an email at info@roadyo.net, if you will like us there!"), 'pub' => PUBNUB_PUBLISH_KEY, 'sub' => PUBNUB_SUBSCRIBE_KEY);
        
        
    }
    

    /*
     * Method name: uploadImage
     * Desc: Uploads media to the server folder named "pics"
     * Input: Request data
     * Output:  image name if uploaded and status message according to the result
     */

    protected function uploadImage($args) {

        if ($args['ent_sess_token'] == '')
            return $this->_getStatusMessage(1, 'Session token');
        else if ($args['ent_dev_id'] == '')
            return $this->_getStatusMessage(1, 'Device id');
        else if ($args['ent_snap_name'] == '')
            return $this->_getStatusMessage(1, 'Snap name');
        else if ($args['ent_snap_type'] == '')
            return $this->_getStatusMessage(1, 'Snap type');
        else if ($args['ent_snap_chunk'] == '')
            return $this->_getStatusMessage(1, 'Chunk');
        else if ($args['ent_upld_from'] == '')
            return $this->_getStatusMessage(1, 'Upload from');
        else if ($args['ent_offset'] == '')
            return $this->_getStatusMessage(1, 'Offset');
        else if ($args['ent_date_time'] == '')
            return $this->_getStatusMessage(1, 'Date time');

        $this->curr_date_time = urldecode($args['ent_date_time']);

        $valid_exts = array("jpg", "jpeg", "gif", "png");
// Select the extension from the file.
        $ext = end(explode(".", strtolower(trim($args['ent_snap_name']))));

        if (!in_array($ext, $valid_exts))
            return $this->_getStatusMessage(26, 12);

        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], $args['ent_upld_from'], '1');

        if (is_array($returned))
            return $returned;

        if (filter_var($args['ent_snap_chunk'], FILTER_VALIDATE_URL)) {
            $args['ent_snap_chunk'] = base64_encode(file_get_contents($args['ent_snap_chunk']));
        }

        if ($args['ent_upld_from'] == '1') {
            $table = 'master';
            $field = 'mas_id';
        } else {
            $table = 'slave';
            $field = 'slave_id';
        }

        $file_to_open = 'pics/' . $args['ent_snap_name'];

//        echo '<img src="data:image/jpg;base64,'.$args['ent_snap_chunk'].'" />';

        $newphrase_plus = str_replace('-', '+', $args['ent_snap_chunk']);
        $newphrase = str_replace('_', '/', $newphrase_plus);

        $base64_de = base64_decode($newphrase); //base64_decode($media_chunk);

        if (strlen($base64_de) > $this->maxChunkSize)
            return $this->_getStatusMessage(18, 205);

        $handle = fopen($file_to_open, 'a');
        $fwrite = fwrite($handle, $base64_de);
        fclose($handle);

        if ($fwrite === false)
            return $this->_getStatusMessage(19, 224);
        else if ($args['ent_snap_type'] == '1')
            mysql_query("update $table set profile_pic = '" . $args['ent_snap_name'] . "' where $field = '" . $this->User['entityId'] . "'", $this->db->conn);

        $file_size = filesize($file_to_open);
        $number_of_chunks = ceil($file_size / $this->maxChunkSize);

        if ((int) $args['ent_offset'] == $number_of_chunks) {

            if ($args['ent_upld_from'] == '1' && $args['ent_snap_type'] == '2') {
                mysql_query("insert into images(mas_id,image) values ('" . $this->User['entityId'] . "','" . $args['ent_snap_name'] . "')", $this->db->conn);
            }

            if ($args['ent_snap_type'] == '1' && $args['ent_upld_from'] == '1') {
                $location = $this->mongo->selectCollection('location');

                $newdata = array('$set' => array("image" => $args['ent_snap_name']));
                $location->update(array("user" => (int) $this->User['entityId']), $newdata);
            }

            list($width, $height) = getimagesize($file_to_open);

            $ratio = $height / $width;

            /* mdpi 36*36 */
            $mdpi_nw = 36;
            $mdpi_nh = $ratio * 36;

            $mtmp = imagecreatetruecolor($mdpi_nw, $mdpi_nh);

            $mdpi_image = imagecreatefromjpeg($file_to_open);

            imagecopyresampled($mtmp, $mdpi_image, 0, 0, 0, 0, $mdpi_nw, $mdpi_nh, $width, $height);

            $mdpi_file = 'pics/mdpi/' . $args['ent_snap_name'];

            imagejpeg($mtmp, $mdpi_file, 100);

            /* HDPI Image creation 55*55 */
            $hdpi_nw = 55;
            $hdpi_nh = $ratio * 55;

            $tmp = imagecreatetruecolor($hdpi_nw, $hdpi_nh);

            $hdpi_image = imagecreatefromjpeg($file_to_open);

            imagecopyresampled($tmp, $hdpi_image, 0, 0, 0, 0, $hdpi_nw, $hdpi_nh, $width, $height);

            $hdpi_file = 'pics/hdpi/' . $args['ent_snap_name'];

            imagejpeg($tmp, $hdpi_file, 100);

            /* XHDPI 84*84 */
            $xhdpi_nw = 84;
            $xhdpi_nh = $ratio * 84;

            $xtmp = imagecreatetruecolor($xhdpi_nw, $xhdpi_nh);

            $xhdpi_image = imagecreatefromjpeg($file_to_open);

            imagecopyresampled($xtmp, $xhdpi_image, 0, 0, 0, 0, $xhdpi_nw, $xhdpi_nh, $width, $height);

            $xhdpi_file = 'pics/xhdpi/' . $args['ent_snap_name'];

            imagejpeg($xtmp, $xhdpi_file, 100);

            /* xXHDPI 125*125 */
            $xxhdpi_nw = 125;
            $xxhdpi_nh = $ratio * 125;

            $xxtmp = imagecreatetruecolor($xxhdpi_nw, $xxhdpi_nh);

            $xxhdpi_image = imagecreatefromjpeg($file_to_open);

            imagecopyresampled($xxtmp, $xxhdpi_image, 0, 0, 0, 0, $xxhdpi_nw, $xxhdpi_nh, $width, $height);

            $xxhdpi_file = 'pics/xxhdpi/' . $args['ent_snap_name'];

            imagejpeg($xxtmp, $xxhdpi_file, 100);

//            $serverUpload[] = $this->_serverUpload(array('file_name' => $file_to_open));
//            $serverUpload[] = $this->_serverUpload(array('file_name' => $mdpi_file));
//            $serverUpload[] = $this->_serverUpload(array('file_name' => $hdpi_file));
//            $serverUpload[] = $this->_serverUpload(array('file_name' => $xhdpi_file));
//            $serverUpload[] = $this->_serverUpload(array('file_name' => $xxhdpi_file));
        }

        $errMsgArr = $this->_getStatusMessage(17, 122);
        return array('errNum' => $errMsgArr['errNum'], 'errFlag' => $errMsgArr['errFlag'], 'errMsg' => $errMsgArr['errMsg'], 'data' => array('picURL' => $file_to_open, 'writeFlag' => $fwrite, 'serverUploadRes' => $serverUpload));
    }

    protected function _serverUpload($args) {
        $local_directory = dirname(__FILE__);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_URL, $this->serverUploader);
        //most importent curl assues @filed as file field
//        echo $local_directory;
        $post_array = array(
            "my_file" => "@" . $local_directory . '/' . $args['file_name'],
            "upload" => "Upload",
            "dir_to_upload" => $args['file_name']
        );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_array);
        $response = curl_exec($ch);
        return $response;
    }

    /*
     * Method name: getMasters
     * Desc: Get masters around an area
     * Input: Request data
     * Output:  master location if available and status message according to the result
     */

    protected function getMasters($args) {

        if ($args['ent_api_key'] == '')
            return $this->_getStatusMessage(1, 'Api key');
        else if ($args['ent_latitude'] == '' || $args['ent_longitude'] == '')
            return $this->_getStatusMessage(1, 'Location');
        else if ($args['ent_search_type'] == '')
            return $this->_getStatusMessage(1, 'Search type');
        else if ($args['ent_date_time'] == '')
            return $this->_getStatusMessage(1, 'Date time');

        $this->curr_date_time = urldecode($args['ent_date_time']);

        if ($args['ent_api_key'] != '2ee51574176b15c2e')
            return $this->_getStatusMessage(1, 'Api key');

        $resultArr = $this->mongo->selectCollection('$cmd')->findOne(array(
            'geoNear' => 'location',
            'near' => array(
                (double) $args['ent_longitude'], (double) $args['ent_latitude']
            ), 'spherical' => true, 'maxDistance' => 50000 / 6378137, 'distanceMultiplier' => 6378137,
            'query' => array('status' => 3, 'type' => (int) $args['ent_search_type']))
        );

        $md_arr = $nurse_arr = array();
//                    
        foreach ($resultArr['results'] as $res) {
            $doc = $res['obj'];
            $md_arr[] = array("name" => $doc["name"], 'lname' => $doc['lname'], "image" => $doc['image'], "rating" => (float) $doc['rating'],
                'email' => $doc['email'], 'lat' => $doc['location']['latitude'], 'lon' => $doc['location']['longitude'], 'dis' => number_format((float) $res['dis'] / $this->distanceMetersByUnits, 2, '.', ''));
        }


        if (count($md_arr) > 0 || count($nurse_arr) > 0)
            return array('errNum' => "101", 'errFlag' => 0, 'errMsg' => "Drivers found!", 'docs' => $md_arr, 'nurses' => $nurse_arr, 'test' => $args['ent_search_type']);

        return array('errNum' => "102", 'errFlag' => 1, 'errMsg' => "Drivers not found!", 'test' => $resultArr);
    }

    /*
     * Method name: getMasterDetails
     * Desc: Server sends the master details according to the email id that is sent by the client
     * Input: Request data
     * Output:  driver data if available and status message according to the result
     */

    protected function getMasterDetails($args) {

        if ($args['ent_dri_email'] == '')
            return $this->_getStatusMessage(1, 'Driver email');
        else if ($args['ent_date_time'] == '')
            return $this->_getStatusMessage(1, 'Date time');

        $this->curr_date_time = urldecode($args['ent_date_time']);

        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], '2');

        if (is_array($returned))
            return $returned;

        $getDetailsQry = "select doc.mas_id,doc.first_name,doc.last_name,doc.mobile,doc.about,doc.profile_pic,doc.last_active_dt,doc.expertise,";
        $getDetailsQry .= "(select avg(star_rating) from master_ratings where mas_id = doc.mas_id) as rating,";
        $getDetailsQry .= "(select group_concat(image) from images where mas_id = doc.mas_id) as images ";
        $getDetailsQry .= "from master doc where doc.email = '" . $args['ent_dri_email'] . "'";
        $getDetailsRes = mysql_query($getDetailsQry, $this->db->conn);

        if (mysql_error($this->db->conn) != '')
            return $this->_getStatusMessage(3, $getDetailsQry); //_getStatusMessage($errNo, $test_num);

        $num_rows = mysql_num_rows($getDetailsRes);

        if ($num_rows <= 0)
            return $this->_getStatusMessage(20, $getDetailsQry); //_getStatusMessage($errNo, $test_num);

        $doc_data = mysql_fetch_assoc($getDetailsRes);

        $reviewsArr = $this->_getMasterReviews($args);

        if (!isset($reviewsArr[0]['rating']))
            $reviewsArr = array();

        $eduArr = array();

        $getEducationQry = "select edu.degree,edu.start_year,edu.end_year,edu.institute from master_education edu,master doc where doc.mas_id = edu.mas_id and doc.email = '" . $args['ent_dri_email'] . "'";
        $getEducationRes = mysql_query($getEducationQry, $this->db->conn);

        while ($edu = mysql_fetch_assoc($getEducationRes)) {
            $eduArr[] = array('deg' => $edu['degree'], 'start' => $edu['start_year'], 'end' => $edu['end_year'], 'inst' => $edu['institute']);
        }

        $errMsgArr = $this->_getStatusMessage(21, 122);

        return array('errNum' => $errMsgArr['errNum'], 'errFlag' => $errMsgArr['errFlag'], 'errMsg' => $errMsgArr['errMsg'],
            'fName' => $doc_data['first_name'], 'lName' => $doc_data['last_name'], 'mobile' => $doc_data['mobile'], 'pPic' => $doc_data['profile_pic'], 'about' => ($doc_data['about'] == '' ? ' ' : $doc_data['about']), 'expertise' => $doc_data['expertise'],
            'ladt' => $doc_data['last_active_dt'], 'rating' => (float) $doc_data['rating'], 'images' => explode(',', $doc_data['images']), 'totalRev' => $reviewsArr[0]['total'], 'reviews' => $reviewsArr, 'education' => $eduArr);
    }

    /*
     * Method name: updateMasterLocation
     * Desc: Update master location
     * Input: Request data
     * Output:  success if changed else error according to the result
     */

    protected function updateMasterLocation($args) {

        if ($args['ent_latitude'] == '' || $args['ent_longitude'] == '')
            return $this->_getStatusMessage(1, 'Location');
        else if ($args['ent_date_time'] == '')
            return $this->_getStatusMessage(1, 'Date time');

        $this->curr_date_time = urldecode($args['ent_date_time']);

        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], '2');

        if (is_array($returned))
            return $returned;

        $location = $this->mongo->selectCollection('location');

        $newdata = array('$set' => array("location" => array("longitude" => (float) $args['ent_longitude'], "latitude" => (float) $args['ent_latitude'])));
        $updated = $location->update(array("user" => (int) $this->User['entityId']), $newdata);

        if ($updated)
            return $this->_getStatusMessage(23, 2);
        else
            return $this->_getStatusMessage(22, 3);
    }

    /*
     * Method name: getMasterReviews
     * Desc: Get driver reviews by pagination
     * Input: Request data
     * Output:  success if got it else error according to the result
     */

    protected function getMasterReviews($args) {

        if ($args['ent_dri_email'] == '')
            return $this->_getStatusMessage(1, 'Driver email');
        if ($args['ent_date_time'] == '')
            return $this->_getStatusMessage(1, 'Date time');

        $this->curr_date_time = urldecode($args['ent_date_time']);

        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], '2');

        if (is_array($returned))
            return $returned;

        $reviewsArr = $this->_getMasterReviews($args);

        if (!isset($reviewsArr[0]['rating']))
            return $reviewsArr;

        $errMsgArr = $this->_getStatusMessage(27, 122);

        return array('errNum' => $errMsgArr['errNum'], 'errFlag' => $errMsgArr['errFlag'], 'errMsg' => $errMsgArr['errMsg'], 'reviews' => $reviewsArr);
    }

    /*
     * Method name: getMasterAppointments
     * Desc: Get Driver appointments
     * Input: Request data
     * Output:  success if got it else error according to the result
     */

    protected function getMasterAppointments($args) {



        if ($args['ent_appnt_dt'] == '')
            return $this->_getStatusMessage(1, 'Booking date time');
        else if ($args['ent_appnt_dt'] == '')
            return $this->_getStatusMessage(1, 'Date time');

        $this->curr_date_time = urldecode($args['ent_date_time']);

        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], '1');

        if (is_array($returned))
            return $returned;

        $args['ent_appnt_dt'] = urldecode($args['ent_appnt_dt']);

        $dates = explode('-', $args['ent_appnt_dt']);

        if (count($dates) == 3) {
            $endDate = date('Y-m-d', strtotime('+7 day', strtotime($args['ent_appnt_dt'])));
            $selectStr = " DATE(a.appointment_dt) between '" . $args['ent_appnt_dt'] . "' and '" . $endDate . "'";
        } else {
            $args['ent_appnt_dt'] = $args['ent_appnt_dt'] . '-01';
            $endDate = date('Y-m-d', strtotime('+1 month', strtotime($args['ent_appnt_dt'])));
            $selectStr = " YEAR(a.appointment_dt) = '" . (int) $dates[0] . "' and MONTH(a.appointment_dt) = '" . (int) $dates[1] . "'";
        }

        $selectAppntsQry = "select p.profile_pic,p.first_name,p.phone,p.email,a.additional_info,a.appointment_id,a.appt_lat,a.appt_long,a.appointment_dt,a.extra_notes,a.address_line1,a.address_line2,a.drop_addr1,a.drop_addr2,a.drop_lat,a.drop_long,a.complete_dt,a.start_dt,a.arrive_dt,a.status,a.payment_status,a.amount,a.distance_in_mts,(select count(appointment_id) from appointment where status = 1 and mas_id = '" . $this->User['entityId'] . "') as pen_count from appointment a, slave p ";
        $selectAppntsQry .= " where p.slave_id = a.slave_id and a.mas_id = '" . $this->User['entityId'] . "' and " . $selectStr . " and a.status NOT IN (1,3,4,5,10) order by a.appointment_id DESC"; // and a.appointment_dt >= '" . $curr_date_bfr_1hr . "'        a.status NOT in (1,3,4,7) and

        $selectAppntsRes = mysql_query($selectAppntsQry, $this->db->conn);

        if (mysql_num_rows($selectAppntsRes) <= 0) {

            $selectPenCountQry = "select count(*) as count from appointment where status = 1 and mas_id = '" . $this->User['entityId'] . "'";
            $countArr = mysql_fetch_assoc(mysql_query($selectPenCountQry, $this->db->conn));
            $errMsgArr = $this->_getStatusMessage(30, 2);

            $date = $args['ent_appnt_dt'];

            while ($date <= $endDate) {

                $sortedApnts[] = array('date' => $date, 'appt' => array());
                $date = date('Y-m-d', strtotime('+1 day', strtotime($date)));
            }

            return array('errNum' => $errMsgArr['errNum'], 'errFlag' => $errMsgArr['errFlag'], 'errMsg' => $errMsgArr['errMsg'], 'penCount' => $countArr['count'], 'refIndex' => array(), 'appointments' => $sortedApnts, 't' => $selectAppntsQry);
        }

        $appointments = $daysArr = array();

        $pendingCount = 0;

        while ($appnt = mysql_fetch_assoc($selectAppntsRes)) {

            if ($appnt['profile_pic'] == '')
                $appnt['profile_pic'] = $this->default_profile_pic;

            $pendingCount = $appnt['pen_count'];

            $aptdate = date('Y-m-d', strtotime($appnt['appointment_dt']));

            $durationSec = (abs(strtotime($appnt['complete_dt']) - strtotime($appnt['start_dt'])) / 60);

            $durationMin = round($durationSec, 2);

//            if ($appnt['status'] == '1')
//                $status = 'Booking requested';
//            else if ($appnt['status'] == '2')
//                $status = 'Driver accepted.';
//            else if ($appnt['status'] == '3')
//                $status = 'Driver rejected.';
//            else if ($appnt['status'] == '4')
//                $status = 'You cancelled.';
//            else if ($appnt['status'] == '5')
//                $status = 'Driver cancelled.';
//            else
            if ($appnt['status'] == '6')
                $status = 'Driver is on the way.';
            else if ($appnt['status'] == '7')
                $status = 'Driver arrived.';
            else if ($appnt['status'] == '8')
                $status = 'Booking started.';
            else if ($appnt['status'] == '9')
                $status = 'Booking completed.';
//            else if ($appnt['status'] == '10')
//                $status = 'Booking expired.';
            else
                $status = 'Status unavailable.';

            $appointments[$aptdate][] = array('pPic' => $appnt['profile_pic'], 'email' => $appnt['email'], 'statCode' => $appnt['status'], 'status' => $status,
                'fname' => $appnt['first_name'], 'apntTime' => date('h:i a', strtotime($appnt['appointment_dt'])), 'bid' => $appnt['appointment_id'], 'apptDt' => $appnt['appointment_dt'], 'additional_info' => $appnt['additional_info'],
                'addrLine1' => urldecode($appnt['address_line1']), 'payStatus' => ($appnt['payment_status'] == '') ? 0 : $appnt['payment_status'],
                'dropLine1' => urldecode($appnt['drop_addr1']), 'duration' => round($durationMin, 2), 'distance' => round($appnt['distance_in_mts'] / $this->distanceMetersByUnits, 2), 'amount' => $appnt['amount']);


//            $appointments[$aptdate][] = array('apntDt' => $appnt['appointment_dt'], 'pPic' => $appnt['profile_pic'], 'email' => $appnt['email'], 'status' => $appnt['status'], 'pickupDt' => $appnt['arrive_dt'], 'dropDt' => $appnt['complete_dt'],
//                'fname' => $appnt['first_name'], 'phone' => $appnt['phone'], 'apntTime' => date('h:i a', strtotime($appnt['appointment_dt'])),
//                'apntDate' => date('Y-m-d', strtotime($appnt['appointment_dt'])), 'apptLat' => (double) $appnt['appt_lat'], 'apptLong' => (double) $appnt['appt_long'],
//                'addrLine1' => urldecode($appnt['address_line1']), 'addrLine2' => urldecode($appnt['address_line2']), 'notes' => $appnt['extra_notes'],
//                'dropLine1' => urldecode($appnt['drop_addr1']), 'dropLine2' => urldecode($appnt['drop_addr2']), 'dropLat' => (double) $appnt['drop_lat'], 'dropLong' => (double) $appnt['drop_long'], 'duration' => $durationMin, 'distanceMts' => $appnt['distance_in_mts'], 'amount' => $appnt['amount']);
        }
        $refIndexes = $sortedApnts = array();
        $date = date('Y-m-d', strtotime($args['ent_appnt_dt']));

        while ($date < $endDate) {

            $empty_arr = array();

            if (is_array($appointments[$date])) {
                $sortedApnts[] = array('date' => $date, 'appt' => $appointments[$date]);
                $num = date('j', strtotime($date));
                $refIndexes[] = $num;
            } else {
                $sortedApnts[] = array('date' => $date, 'appt' => $empty_arr);
            }

            $date = date('Y-m-d', strtotime('+1 day', strtotime($date)));
        }
//print_r($sortedApnts);

        $errMsgArr = $this->_getStatusMessage(31, 2);

        return array('errNum' => $errMsgArr['errNum'], 'errFlag' => $errMsgArr['errFlag'], 'errMsg' => $errMsgArr['errMsg'], 'penCount' => $pendingCount, 'refIndex' => $refIndexes, 'appointments' => $sortedApnts); //,'test'=>$selectAppntsQry,'test1'=>$appointments);
    }

    /*
     * Method name: getPendingAppointments
     * Desc: Get Driver appointments
     * Input: Request data
     * Output:  success if got it else error according to the result
     */

    protected function getPendingAppts($args) {

        if ($args['ent_date_time'] == '')
            return $this->_getStatusMessage(1, 'Date time');

        $this->curr_date_time = urldecode($args['ent_date_time']);

        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], '1');

        if (is_array($returned))
            return $returned;

//        $curr_date = date('Y-m-d H:i:s', time());
//        $curr_date_bfr_30min = date('Y-m-d H:i:s', time() - 1800);
//        $curr_date_bfr_1hr = date('Y-m-d H:i:s', time() - 3600);


        $selectAppntsQry = "select p.profile_pic,p.first_name,p.phone,p.email,a.appt_lat,a.appt_long,a.appointment_dt,a.appointment_id,a.drop_addr2,a.drop_addr1,a.extra_notes,a.address_line1,a.address_line2,a.status,a.appt_type from appointment a, slave p ";
        $selectAppntsQry .= " where p.slave_id = a.slave_id and a.status = 2 and a.appt_type = 2 and a.mas_id = '" . $this->User['entityId'] . "' order by a.appointment_dt DESC"; // and a.appointment_dt >= '" . $curr_date_bfr_1hr . "'

        $selectAppntsRes = mysql_query($selectAppntsQry, $this->db->conn);

        if (mysql_num_rows($selectAppntsRes) <= 0)
            return $this->_getStatusMessage(30, $selectAppntsQry);

        $pending_appt = array();

        while ($appnt = mysql_fetch_assoc($selectAppntsRes)) {

            if ($appnt['profile_pic'] == '')
                $appnt['profile_pic'] = $this->default_profile_pic;

            $pending_appt[date('Y-m-d', strtotime($appnt['appointment_dt']))][] = array('apntDt' => $appnt['appointment_dt'], 'pPic' => $appnt['profile_pic'], 'email' => $appnt['email'], 'bid' => $appnt['appointment_id'],
                'fname' => $appnt['first_name'], 'phone' => $appnt['phone'], 'apntTime' => date('H:i', strtotime($appnt['appointment_dt'])), 'dropLine1' => urldecode($appnt['drop_addr1']), 'dropLine2' => urldecode($appnt['drop_addr2']),
                'apntDate' => date('Y-m-d', strtotime($appnt['appointment_dt'])), 'apptLat' => (double) $appnt['appt_lat'], 'apptLong' => (double) $appnt['appt_long'],
                'addrLine1' => urldecode($appnt['address_line1']), 'addrLine2' => urldecode($appnt['address_line2']), 'notes' => $appnt['extra_notes'], 'bookType' => $appnt['booking_type']);
        }

        $finalArr = array();

        foreach ($pending_appt as $date => $penAppt) {
            $finalArr[] = array('date' => $date, 'appt' => $penAppt);
        }

        $errMsgArr = $this->_getStatusMessage(31, 2);

        return array('errNum' => $errMsgArr['errNum'], 'errFlag' => $errMsgArr['errFlag'], 'errMsg' => $errMsgArr['errMsg'], 'appointments' => $finalArr); //,'test'=>$selectAppntsQry,'test1'=>$appointments);
    }

    /*
     * Method name: getPendingAppointments
     * Desc: Get Driver appointments
     * Input: Request data
     * Output:  success if got it else error according to the result
     */

    protected function getPendingAppointments($args) {

        if ($args['ent_date_time'] == '')
            return $this->_getStatusMessage(1, 'Date time');

        $this->curr_date_time = urldecode($args['ent_date_time']);

        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], '1');

        if (is_array($returned))
            return $returned;

//        $curr_date = date('Y-m-d H:i:s', time());
//        $curr_date_bfr_30min = date('Y-m-d H:i:s', time() - 1800);
//        $curr_date_bfr_1hr = date('Y-m-d H:i:s', time() - 3600);


        $selectAppntsQry = "select p.profile_pic,p.first_name,p.phone,p.email,a.appt_lat,a.appt_long,a.appointment_dt,a.extra_notes,a.address_line1,a.address_line2,a.status,a.booking_type from appointment a, slave p ";
        $selectAppntsQry .= " where p.slave_id = a.slave_id and a.status = 1 and a.mas_id = '" . $this->User['entityId'] . "' order by a.appointment_dt DESC"; // and a.appointment_dt >= '" . $curr_date_bfr_1hr . "'

        $selectAppntsRes = mysql_query($selectAppntsQry, $this->db->conn);

        if (mysql_num_rows($selectAppntsRes) <= 0)
            return $this->_getStatusMessage(30, $selectAppntsQry);

        $pending_appt = array();

        while ($appnt = mysql_fetch_assoc($selectAppntsRes)) {

            if ($appnt['profile_pic'] == '')
                $appnt['profile_pic'] = $this->default_profile_pic;

            $pending_appt[] = array('apntDt' => $appnt['appointment_dt'], 'pPic' => $appnt['profile_pic'], 'email' => $appnt['email'],
                'fname' => $appnt['first_name'], 'phone' => $appnt['phone'], 'apntTime' => date('H:i', strtotime($appnt['appointment_dt'])),
                'apntDate' => date('Y-m-d', strtotime($appnt['appointment_dt'])), 'apptLat' => (double) $appnt['appt_lat'], 'apptLong' => (double) $appnt['appt_long'],
                'addrLine1' => urldecode($appnt['address_line1']), 'addrLine2' => urldecode($appnt['address_line2']), 'notes' => $appnt['extra_notes'], 'bookType' => $appnt['booking_type']);
        }


        $errMsgArr = $this->_getStatusMessage(31, 2);

        return array('errNum' => $errMsgArr['errNum'], 'errFlag' => $errMsgArr['errFlag'], 'errMsg' => $errMsgArr['errMsg'], 'appointments' => $pending_appt); //,'test'=>$selectAppntsQry,'test1'=>$appointments);
    }

    function getMasterTripDetails($args) {

        if ($args['ent_date_time'] == '')
            return $this->_getStatusMessage(1, 'Date time');

        $this->curr_date_time = urldecode($args['ent_date_time']);

        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], '1');

        if (is_array($returned))
            return $returned;

        $explodeDateTime = explode(' ', $this->curr_date_time);
        $explodeDate = explode('-', $explodeDateTime[0]);

        $weekData = $this->week_start_end_by_date($this->curr_date_time);

        $query = "select distinct (select sum(mas_earning) from appointment where mas_id =mas.mas_id and payment_type = 2 ) as DriverCashEarning,"
                . "(select sum(mas_earning) from appointment where mas_id =mas.mas_id and payment_type = 1 ) as DriverCardEarning,"
                . "(select sum(mas_earning) from appointment where mas_id =mas.mas_id) as DriverTotalEarning,"
                . "(select sum(amount) from appointment where mas_id =mas.mas_id and payment_type = 2 and status = 9) as DriverCashColleted,"
                . "COALESCE((SELECT SUM(mas_earning) FROM appointment WHERE  mas_id = mas.mas_id   AND  STATUS = 9) - (SELECT SUM(amount) FROM appointment WHERE   mas_id = mas.mas_id  and payment_type = 2 and status = 9) - (SELECT COALESCE( SUM(pay_amount) ,0) FROM payroll WHERE mas_id = mas.mas_id ),0) AS pending,"
                . "COALESCE((SELECT SUM(mas_earning) FROM appointment WHERE payment_type = 2 AND mas_id = mas.mas_id  AND  STATUS = 9) - (SELECT SUM(app_owner_pl) FROM appointment WHERE payment_type = 1 AND mas_id =mas.mas_id  AND STATUS = 9),0) AS DriverToPay,"
                . "(select pay_date from payroll where mas_id= mas.mas_id order by payroll_id DESC limit 1) as pay_date,"
                . "(SELECT SUM(pay_amount) FROM payroll WHERE mas_id = mas.mas_id) as TOTALRECIVED,"
                . "(select pay_amount from payroll where mas_id= mas.mas_id order by payroll_id DESC limit  1) as pay_amount,"
                . "(select count(appointment_id) from appointment where mas_id = mas.mas_id and DATE(appointment_dt) = '" . $explodeDateTime[0] . "' and status = 9) as cmpltApts_toady,"
                . "(select count(appointment_id) from appointment where mas_id = mas.mas_id and DATE(appointment_dt) BETWEEN '" . $weekData['first_day_of_week'] . "' and '" . $weekData['last_day_of_week'] . "' and status = 9) as cmpltApts_week,"
                . "(select count(appointment_id) from appointment where mas_id = mas.mas_id and DATE_FORMAT(appointment_dt, '%Y-%m') = '" . $explodeDate[0] . '-' . $explodeDate[1] . "' and status = 9) as cmpltApts_month,"
                . "(select sum(mas_earning) from appointment where mas_id = mas.mas_id and DATE(appointment_dt) = '" . $explodeDateTime[0] . "' and status = 9) as toadys_earning,"
                . "(select count(appointment_id) from appointment where mas_id = mas.mas_id and DATE(appointment_dt) = '" . $explodeDateTime[0] . "' and status = 4) as canceled_toadys,"
                . "(select sum(mas_earning) from appointment where mas_id = mas.mas_id and status = 9 and DATE(appointment_dt) BETWEEN '" . $weekData['first_day_of_week'] . "' and '" . $weekData['last_day_of_week'] . "') as week_earnings,"
                . "(select count(appointment_id) from appointment where mas_id = mas.mas_id and status = 4 and DATE(appointment_dt) BETWEEN '" . $weekData['first_day_of_week'] . "' and '" . $weekData['last_day_of_week'] . "') as canceled_week,"
                . "(select sum(mas_earning) from appointment where mas_id = mas.mas_id and status = 9 and DATE_FORMAT(appointment_dt, '%Y-%m') = '" . $explodeDate[0] . '-' . $explodeDate[1] . "') as month_earnings,"
                . "(select count(appointment_id) from appointment where mas_id = mas.mas_id and status = 4 and DATE_FORMAT(appointment_dt, '%Y-%m') = '" . $explodeDate[0] . '-' . $explodeDate[1] . "') as canceld_month "
                . "from appointment mas where mas.mas_id = '" . $this->User['entityId'] . "'";
//       return array('query' => $query);

        $getAnalytics = mysql_query($query, $this->db->conn);
        $PaymentLocs = $tripdata = array();

        if (mysql_num_rows($getAnalytics) > 0) {
            $errMsgArr = $this->_getStatusMessage(21, 2);
        } else {
            $errMsgArr = $this->_getStatusMessage(3, 2);
        }

        $getRowData = mysql_fetch_assoc($getAnalytics);


        $PaymentLocs['DriverCashEarning'] = $getRowData['DriverCashEarning'];
        $PaymentLocs['DriverCardEarning'] = $getRowData['DriverCardEarning'];
        $PaymentLocs['DriverTotalEarning'] = $getRowData['DriverTotalEarning'];
        $PaymentLocs['DriverCashColleted'] = $getRowData['DriverCashColleted'];
        $PaymentLocs['pending'] = $getRowData['pending'];
        $PaymentLocs['DriverToPay'] = $getRowData['DriverToPay'];
        $PaymentLocs['LastPayDate'] = $getRowData['pay_date'];
        $PaymentLocs['LastPayAmount'] = $getRowData['pay_amount'];
        $PaymentLocs['TotalDue'] = ($PaymentLocs['pending'] + ($PaymentLocs['DriverToPay']));
        $PaymentLocs['TotalReceived'] = $getRowData['TOTALRECIVED'];



        $tripdata['cmpltApts_toady'] = $getRowData['cmpltApts_toady'];
        $tripdata['cmpltApts_week'] = $getRowData['cmpltApts_week'];
        $tripdata['cmpltApts_month'] = $getRowData['cmpltApts_month'];

        $tripdata['toadys_earning'] = $getRowData['toadys_earning'];
        $tripdata['canceled_toadys'] = $getRowData['canceled_toadys'];


        $tripdata['week_earnings'] = $getRowData['week_earnings'];
        $tripdata['canceled_week'] = $getRowData['canceled_week'];

        $tripdata['month_earnings'] = $getRowData['month_earnings'];
        $tripdata['canceld_month'] = $getRowData['canceld_month'];




        $errMsgArr = $this->_getStatusMessage(21, 2);
        return array('errNum' => $errMsgArr['errNum'], 'errFlag' => $errMsgArr['errFlag'], 'errMsg' => $errMsgArr['errMsg'],
            'paymentLoc' => $this->makeZeroIfNull($PaymentLocs),
            'Trips' => $this->makeZeroIfNull($tripdata)
        );
    }

    function makeZeroIfNull($array) {
        $arraytosend = array();
        foreach ($array as $key => $res) {
            $arraytosend[$key] = ($res == '' ? 0 : $res);
        }
        return $arraytosend;
    }

    /*
     * Method name: getHistoryWith
     * Desc: Get appointment details
     * Input: Request data
     * Output:  success if got it else error according to the result
     */

    protected function getHistoryWith($args) {

        if ($args['ent_pas_email'] == '')
            return $this->_getStatusMessage(1, 'Passenger email');
        else if ($args['ent_date_time'] == '')
            return $this->_getStatusMessage(1, 'Date time');

        $this->curr_date_time = urldecode($args['ent_date_time']);

        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], '1');

        if (is_array($returned))
            return $returned;

        $pageNum = (int) $args['ent_page'];

        if ($args['ent_page'] == '')
            $pageNum = 1;

        $lowerLimit = ($this->historyPageSize * $pageNum) - $this->historyPageSize;
        $upperLimit = $this->historyPageSize * $pageNum;

        $selectAppntsQry = "select a.remarks,a.appointment_dt from appointment a,slave p ";
        $selectAppntsQry .= "where a.slave_id = p.slave_id and a.mas_id = '" . $this->User['entityId'] . "' and p.email = '" . $args['ent_pas_email'] . "' ";
        $selectAppntsQry .= "limit $lowerLimit,$upperLimit";

        $selectAppntsRes = mysql_query($selectAppntsQry, $this->db->conn);

        if (mysql_num_rows($selectAppntsRes) <= 0)
            return $this->_getStatusMessage(32, 12);

        $data = array();

        while ($details = mysql_fetch_assoc($selectAppntsRes)) {
            $data[] = array('apptDt' => $details['appointment_dt'], 'remarks' => $details['remarks']);
        }

        $errMsgArr = $this->_getStatusMessage(33, 2);
        return array('errNum' => $errMsgArr['errNum'], 'errFlag' => $errMsgArr['errFlag'], 'errMsg' => $errMsgArr['errMsg'], 'history' => $data);
    }

    /*
     * Method name: fareCalculator
     * Desc: calculates fare for the given pick up to drop off
     * Input: Request data
     * Output: success if got it else error according to the result
     */

    protected function fareCalculator($args) {


        if ($args['ent_type_id'] == '')
            return $this->_getStatusMessage(1, 'Vehicle type');
        else if ($args['ent_from_lat'] == '' || $args['ent_from_long'] == '')
            return $this->_getStatusMessage(1, 'Pickup location');
        else if ($args['ent_to_lat'] == '' || $args['ent_to_long'] == '')
            return $this->_getStatusMessage(1, 'Drop location');
        else if ($args['ent_date_time'] == '')
            return $this->_getStatusMessage(1, 'Date time');

        $this->curr_date_time = urldecode($args['ent_date_time']);

        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], '2');

        if (is_array($returned))
            return $returned;

//        $arr = array();

        $getTypeDataQry = "select * from workplace_types where type_id = '" . $args['ent_type_id'] . "'";
        $getTypeDataRes = mysql_query($getTypeDataQry, $this->db->conn);

        $typeData = mysql_fetch_assoc($getTypeDataRes);
        if (!is_array($typeData)) {
            return $this->_getStatusMessage(1, 'Vehicle type');
        }



        $getDirectionFormMatrix = $this->get_DirectionFormMatrix($args['ent_curr_lat'], $args['ent_curr_long'], $args['ent_from_lat'], $args['ent_from_long']);

        $cur_to_pick_distance_text = $getDirectionFormMatrix['distance'];


        $getDirectionFormMatrix = $this->get_DirectionFormMatrix($args['ent_from_lat'], $args['ent_from_long'], $args['ent_to_lat'], $args['ent_to_long']);

//        this above array will return below thing 
//        return array('data' => $getDirectionFormMatrix,
//            'distance' => round($getDirectionFormMatrix['distance']/$this->distanceMetersByUnits,2), 
//            'duration' => round($getDirectionFormMatrix['duration'] / 360)); // in minuts

        $distance_in_mtr = $getDirectionFormMatrix['distance'];
        $duration_in_sec = ($getDirectionFormMatrix['duration'] / 60);




        $surg_price = '';

        $zonefactor = $this->mongo->selectCollection('zones')->findOne(
                array("polygons" =>
                    array('$geoIntersects' =>
                        array('$geometry' =>
                            array("type" => "Point", "coordinates" => array((double) $args['ent_from_long'], (double) $args['ent_from_lat']))
                        )
                    )
                )
        );

        if (is_array($zonefactor))
            $surg_price = (int) $zonefactor['surge_price'];

        if ($surg_price != '') {
            $calculatedAmount = $typeData['min_fare'] * $surg_price;
            $fare1 = number_format(($typeData['basefare'] + (float) (($distance_in_mtr / $this->distanceMetersByUnits) * $typeData['price_per_km'] * $surg_price) + (float) (($duration_in_sec / 60) * $typeData['price_per_min'] * $surg_price) * $surg_price), 2, '.', '');
        } else {
            $fare1 = number_format($typeData['basefare'] + (float) (($distance_in_mtr / $this->distanceMetersByUnits) * $typeData['price_per_km']) + (float) (($duration_in_sec / 60) * $typeData['price_per_min']), 2, '.', '');
        }

        $fare = ($calculatedAmount < $fare1) ? $fare1 : $calculatedAmount;

        // return array('fare' => $fare);

        $errMsgArr = $this->_getStatusMessage(21, 2);
        return array('errNum' => $errMsgArr['errNum'], 'errFlag' => $errMsgArr['errFlag'], 'errMsg' => $errMsgArr['errMsg'], 'dis' => round(($distance_in_mtr / $this->distanceMetersByUnits), 1) . ' ' . APP_DISTANCE_METRIC, 'fare' => $fare, 'curDis' => round(($cur_to_pick_distance_text / $this->distanceMetersByUnits), 1) . ' ' . APP_DISTANCE_METRIC, 'surg_price' => $surg_price); // 't' => $arr, 't1' => $arr
    }

    protected function get_DirectionFormMatrix($pickupLat, $pickuptLong, $dropLat, $DropLong) {
        $url = 'https://maps.googleapis.com/maps/api/distancematrix/json?origins=' . $pickupLat . ',' . $pickuptLong . '&destinations=' . $dropLat . ',' . $DropLong;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        $result = curl_exec($ch);
        $arr = json_decode($result, true);

        return array('OrignalDatadata' => $arr,
            'distance' => $arr['rows'][0]['elements'][0]['distance']['value'],
            'duration' => $arr['rows'][0]['elements'][0]['distance']['value']);
    }

    protected function fareCalAdmin($args) {

        if ($args['ent_type_id'] == '')
            return $this->_getStatusMessage(1, 'Vehicle type');
        else if ($args['ent_from_lat'] == '' || $args['ent_from_long'] == '')
            return $this->_getStatusMessage(1, 'Pickup location');
        else if ($args['ent_to_lat'] == '' || $args['ent_to_long'] == '')
            return $this->_getStatusMessage(1, 'Drop location');
        else if ($args['ent_date_time'] == '')
            return $this->_getStatusMessage(1, 'Date time');

        $this->curr_date_time = urldecode($args['ent_date_time']);

//        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], '2');

        if (is_array($returned))
            return $returned;

//        $arr = array();

        $getTypeDataQry = "select * from workplace_types where type_id = '" . $args['ent_type_id'] . "'";
        $getTypeDataRes = mysql_query($getTypeDataQry, $this->db->conn);

        $cur_to_pick_arr = $this->_getDirectionsData(array('lat' => $args['ent_curr_lat'], 'long' => $args['ent_curr_long']), array('lat' => $args['ent_from_lat'], 'long' => $args['ent_from_long']));

        $cur_to_pick_distance_text = $cur_to_pick_arr['routes'][0]['legs'][0]['distance']['value'];

        $arr = $this->_getDirectionsData(array('lat' => $args['ent_from_lat'], 'long' => $args['ent_from_long']), array('lat' => $args['ent_to_lat'], 'long' => $args['ent_to_long']));

        $distance_in_mtr = $arr['routes'][0]['legs'][0]['distance']['value'];
        $duration_in_sec = $arr['routes'][0]['legs'][0]['duration']['value'];

        $typeData = mysql_fetch_assoc($getTypeDataRes);

        $fare1 = number_format($typeData['basefare'] + (float) (($distance_in_mtr / $this->distanceMetersByUnits) * $typeData['price_per_km']) + (float) (($duration_in_sec / 60) * $typeData['price_per_min']), 2, '.', '');

//        $distance_in_mts = $arr['routes'][0]['legs'][0]['distance']['value'];
//        $dis_in_km = (float) ($distance_in_mts / $this->distanceMetersByUnits);

        $calculatedAmount = $typeData['min_fare']; //(float) $dis_in_km * $typeData['price_per_km'];

        $fare = ($calculatedAmount < $fare1) ? $fare1 : $calculatedAmount;

        $errMsgArr = $this->_getStatusMessage(21, 2);
        return array('errNum' => $errMsgArr['errNum'], 'errFlag' => $errMsgArr['errFlag'], 'errMsg' => $errMsgArr['errMsg'], 'dis' => round(($distance_in_mtr / $this->distanceMetersByUnits), 1) . ' ' . APP_DISTANCE_METRIC, 'fare' => $fare, 'curDis' => round(($cur_to_pick_distance_text / $this->distanceMetersByUnits), 1) . ' ' . APP_DISTANCE_METRIC, 't' => $arr, 't1' => $arr);
    }

    /*
     * Method name: liveBooking
     * Desc: Book appointment live in a given slot
     * Input: Request data
     * Output: success if got it else error according to the result
     */

    protected function dispatchJob($args) {

        if ($args['ent_mas_id'] == '')
            return $this->_getStatusMessage(1, 'Master id');
        else if ($args['ent_appointment_id'] == '')
            return $this->_getStatusMessage(1, 'Booking id');

        $checkAppointmentQry = "select a.*,p.* from appointment a,slave p where a.slave_id = p.slave_id and a.appointment_id = '" . $args['ent_appointment_id'] . "'";

        $checkAppointmentRes = mysql_query($checkAppointmentQry, $this->db->conn);

        if (mysql_num_rows($checkAppointmentRes) <= 0)
            return $this->_getStatusMessage(71, 1);

        $apptId = $args['ent_appointment_id'];

        $apptDetails = mysql_fetch_assoc($checkAppointmentRes);

        $location = $this->mongo->selectCollection('location');

        $location->ensureIndex(array('location' => '2d'));

        $doc = $location->findOne(array('user' => (int) $args['ent_mas_id']));

        if (count($doc) <= 0)
            return $this->_getStatusMessage(64, 64);

        $checkAppointmentQry = "select * from appointment where appointment_dt = '" . $apptDetails['appointment_dt'] . "' and mas_id = '" . $args['ent_mas_id'] . "' and status = 2";
        if (mysql_num_rows(mysql_query($checkAppointmentQry, $this->db->conn)) > 0) {
            $location->update(array('user' => (int) $args['ent_mas_id']), array('$set' => array('inBooking' => 1)));
            return $this->_getStatusMessage(71, '1');
        }

        $updateQry = "update appointment set mas_id = '" . $args['ent_mas_id'] . "',expire_ts = '" . (time() + $this->expireTimeForDriver) . "' where appointment_id = '" . $apptId . "'";
        mysql_query($updateQry, $this->db->conn);

        if (mysql_affected_rows() < 0)
            return $this->_getStatusMessage(3, $updateQry);

        $pushNum = array();

        $master = array("id" => $doc["user"], 'rating' => $doc['rating'], 'fname' => $doc['name'], 'image' => $doc['image'], 'email' => $doc['email'], 'lat' => $doc['location']['latitude'], 'lon' => $doc['location']['longitude'], 'carId' => $doc['carId'], 'chn' => $doc['chn'], 'listner' => $doc['listner'], 'type_id' => $doc['type']);

        if ($doc['inBooking'] == 2)
            return $this->_getStatusMessage(71, $pushNum);

//        return $this->_getStatusMessage(72, '$pushNum');

        if ((int) $doc['status'] != 3) {
//do nothing
        } else {

            $location->update(array('user' => (int) $master['id']), array('$set' => array('inBooking' => 2)));

            if ($apptId <= 0)
                return $this->_getStatusMessage(3, 12);

            if ($args['ent_addr_line2'] == '') {
                $message = "New Job from " . $apptDetails['first_name'] . " for " . date('jS M \a\t g:i A', strtotime($apptDetails['appointment_dt']));
            } else {
                $exploded = explode(" ", $args['ent_addr_line2']);
                $message = "New Job in " . $exploded[0] . $exploded[1] . " from " . $apptDetails['first_name'] . " for " . date('jS M \a\t g:i A', strtotime($apptDetails['appointment_dt']));
            }

            $this->ios_cert_path = IOS_DRIVER_PEM_PATH;
            $this->ios_cert_pwd = IOS_DRIVER_PEM_PASS;
            $this->androidApiKey = ANDROID_DRIVER_PUSH_KEY;

            $explodeAddr2 = explode(',', $apptDetails['addr_line1']);

            $aplPushContent = array('alert' => $message, 'nt' => 51, 'sname' => $apptDetails['first_name'], 'dt' => $apptDetails['appointment_dt'], 'e' => $apptDetails['email'], 'sound' => 'taxina.wav', 'bid' => $apptId, 'ltg' => $apptDetails['appt_lat'] . ',' . $apptDetails['appt_long'], 'adr2' => ($apptDetails['addr_line2'] == '' ? $explodeAddr2[0] . ' ' . $explodeAddr2[1] : $apptDetails['addr_line2']), 'chn' => 'qp_' . $apptDetails['user_device']);
            $andrPushContent = array("payload" => $message, 'action' => 51, 'sname' => $apptDetails['first_name'], 'dt' => $apptDetails['appointment_dt'], 'e' => $apptDetails['email'], 'bid' => $apptId, 'ltg' => $apptDetails['appt_lat'] . ',' . $apptDetails['appt_long'], 'adr2' => ($apptDetails['addr_line2'] == '' ? $explodeAddr2[0] . ' ' . $explodeAddr2[1] : $apptDetails['addr_line2']), 'chn' => 'qp_' . $apptDetails['user_device']);

            $pubnubContent = array('a' => 11, 'dt' => $apptDetails['appointment_dt'], 'e' => $apptDetails['email'], 'bid' => $apptId, 'nt' => 51, 'ltg' => $apptDetails['appt_lat'] . ',' . $apptDetails['appt_long'], 'adr2' => ($apptDetails['addr_line2'] == '' ? $explodeAddr2[0] . ' ' . $explodeAddr2[1] : $apptDetails['addr_line2']), 'chn' => 'qp_' . $apptDetails['user_device']);

            if (!is_null($master['listner']))
                $pushNum['pubnub'] = $this->pubnub->publish(array(
                    'channel' => $master['listner'],
                    'message' => $pubnubContent
                ));

            $pushNum['push'] = $this->_sendPush('0001', array($master['id']), $message, '7', $apptDetails['first_name'], $apptDetails['appointment_id'], '1', $aplPushContent, $andrPushContent);

            $time = $this->expireTimeForDriver * 2;

            for ($j = 1; $j < $time; $j++) {
                if ($j < $time)
                    usleep(500000);

                $statusCheckQry = "select status from appointment where appointment_id = '" . $apptId . "'";
                $statusArr = mysql_fetch_assoc(mysql_query($statusCheckQry, $this->db->conn));

                if ($statusArr['status'] == '4') {
                    $location->update(array('user' => (int) $master['id']), array('$set' => array('inBooking' => 1)));
                    return $this->_getStatusMessage(74, $statusCheckQry);
                }

                if ($j == ($time - 1) && $statusArr['status'] == '1')
                    mysql_query("update appointment set status = '1',mas_id = 0 where appointment_id = '" . $apptId . "'", $this->db->conn);

                if ($statusArr['status'] == '6' || $statusArr['status'] == '2' || $statusArr['status'] == '7') {
                    $this->ios_cert_path = IOS_PASSENGER_PEM_PATH;
                    $this->ios_cert_pwd = IOS_PASSENGER_PEM_PASS;
                    $this->androidApiKey = ANDROID_PASSENGER_PUSH_KEY;

                    if ($apptDetails['coupon_code'] != '') {

                        $getDataQry = "select coupon_type from coupons where coupon_code = '" . $apptDetails['coupon_code'] . "'";
                        $couponData = mysql_fetch_assoc(mysql_query($getDataQry, $this->db->conn));
                        if ($couponData['coupon_type'] == '3') {
                            $updateCouponStatusQry = "update coupons set status = 1 where coupon_code = '" . $apptDetails['coupon_code'] . "' and user_id = '" . $apptDetails['slave_id'] . "'";
                            mysql_query($updateCouponStatusQry, $this->db->conn);
                        } else if ($couponData['coupon_type'] == '2') {
                            $insertUsageQry = "insert into coupon_usage values ('" . $apptDetails['coupon_code'] . "','" . $apptId . "','" . $apptDetails['slave_id'] . "','" . $couponData['id'] . "')";
                            mysql_query($insertUsageQry, $this->db->conn);
                        }
                    }

                    $message = 'Driver named ' . $master['fname'] . ' will pick you up at ' . date('h:i a, d M', strtotime($apptDetails['appointment_dt'])) . '.';

                      $explodeAddr2 = explode(',', $args['ent_addr_line1']);

            $aplPushContent = array('alert' => $message, 'nt' => (($args['ent_later_dt'] == '') ? '7' : '51'), 'sname' => $this->User['firstName'], 'dt' => (($args['ent_later_dt'] == '') ? $this->curr_date_time : $args['ent_later_dt']), 'e' => $this->User['email'], 'sound' => 'default', 'bid' => $apptId, 'ltg' => $args['ent_lat'] . ',' . $args['ent_long'], 'adr2' => ($args['ent_addr_line2'] == '' ? $explodeAddr2[0] . ' ' . $explodeAddr2[1] : $args['ent_addr_line2']), 'chn' => 'qp_' . $args['ent_dev_id']);
            $andrPushContent = array("payload" => $message, 'action' => (($args['ent_later_dt'] == '') ? '7' : '51'), 'sname' => $this->User['firstName'], 'dt' => (($args['ent_later_dt'] == '') ? $this->curr_date_time : $args['ent_later_dt']), 'e' => $this->User['email'], 'bid' => $apptId, 'ltg' => $args['ent_lat'] . ',' . $args['ent_long'], 'adr2' => ($args['ent_addr_line2'] == '' ? $explodeAddr2[0] . ' ' . $explodeAddr2[1] : $args['ent_addr_line2']), 'chn' => 'qp_' . $args['ent_dev_id']);

                    $push['push'] = $this->_sendPush('0', array($apptDetails['slave_id']), $message, '5', $apptDetails['email'], $this->curr_date_time, '2', $aplPushContent, $andrPushContent);

                    $location->update(array('user' => (int) $master['id']), array('$set' => array('inBooking' => 1)));
                    return $this->_getStatusMessage(39, $pushNum);
                }

                if ($statusArr['status'] == '3') {
                    mysql_query("update appointment set status = '1',mas_id = 0 where appointment_id = '" . $apptId . "'", $this->db->conn);
                    $location->update(array('user' => (int) $master['id']), array('$set' => array('inBooking' => 1)));
                    return $this->_getStatusMessage(71, $pushNum);
                }
            }
        }
        mysql_query("update appointment set status = '1',mas_id = 0 where appointment_id = '" . $apptId . "'", $this->db->conn);
        $location->update(array('user' => (int) $master['id']), array('$set' => array('inBooking' => 1)));
        return $this->_getStatusMessage(71, $pushNum);
    }

    /*
     * Method name: liveBooking
     * Desc: Book appointment live in a given slot
     * Input: Request data
     * Output: success if got it else error according to the result
     */

    protected function liveBooking($args) {

//        $getlivebooking = $this->mongo->selectCollection('getlivebooking');
//
//        $getlivebooking->insert($args);
        
        
        if ($args['ent_wrk_type'] == '')
            return $this->_getStatusMessage(1, 'Vehicle type');
        else if ($args['ent_addr_line1'] == '')
            return $this->_getStatusMessage(1, 'Pickup address');
        else if ($args['ent_lat'] == '' || $args['ent_long'] == '')
            return $this->_getStatusMessage(1, 'Pickup location');
        else if ($args['ent_date_time'] == '')
            return $this->_getStatusMessage(1, 'Date time');
        else if ($args['ent_payment_type'] == '')
            return $this->_getStatusMessage(1, 'Payment type');
        
        if($args['ent_surge'] == '' || $args['ent_surge'] == 0)
          $args['ent_surge']  = 1;
            
        $args['ent_appnt_dt'] = $args['ent_date_time'];

        $this->curr_date_time = urldecode($args['ent_date_time']);

        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], '2');

        if (is_array($returned))
            return $returned;

        $args['ent_appnt_dt'] = urldecode($args['ent_appnt_dt']);

        $getAmountQry = "select min_fare from workplace_types where type_id = '" . $args['ent_wrk_type'] . "'";
        $typeAmount = mysql_fetch_assoc(mysql_query($getAmountQry, $this->db->conn));

        if (!is_array($typeAmount))
            return $this->_getStatusMessage(38, 38);

        $checkSlaveBookingsQry = "select * from appointment where appointment_dt = '" . (($args['ent_later_dt'] == '') ? $this->curr_date_time : $args['ent_later_dt']) . "' and slave_id = '" . $this->User['entityId'] . "' and status != 10";
        if (mysql_num_rows(mysql_query($checkSlaveBookingsQry, $this->db->conn)) > 0) {
            return $this->_getStatusMessage(122, 2);
        }

        
        
         $couponsColl = $this->mongo->selectCollection('coupons');

        if ($args['ent_coupon'] != '') {

            $couponDet = $couponsColl->findOne(array('coupon_code' => $args['ent_coupon'], 'status' => 0));

            if (!is_array($couponDet))
                return $this->_getStatusMessage(100, 21);

            if ($couponDet['start_date'] > strtotime($this->curr_date_time) || $couponDet['expiry_date'] < strtotime($this->curr_date_time))
                return $this->_getStatusMessage(100, $couponDet);
        }
        
        
        if ($args['ent_later_dt'] != '') {

            $insertAppointmentQry = "insert into appointment(mas_id,slave_id,created_dt,last_modified_dt,status,appointment_dt,address_line1,address_line2,appt_lat,appt_long,drop_addr1,drop_addr2,drop_lat,drop_long,extra_notes,amount,zipcode,user_device,appt_type,payment_type,type_id,additional_info,surge) 
            values('0','" . $this->User['entityId'] . "','" . $this->curr_date_time . "','" . $this->curr_date_time . "','1',
                '" . $args['ent_later_dt'] . "','" . $args['ent_addr_line1'] . "','" . $args['ent_addr_line2'] . "','" . $args['ent_lat'] . "',
                '" . $args['ent_long'] . "','" . $args['ent_drop_addr_line1'] . "','" . $args['ent_drop_addr_line2'] . "','" . $args['ent_drop_lat'] . "',
                '" . $args['ent_drop_long'] . "','" . $args['ent_extra_notes'] . "','" . $typeAmount['min_fare'] . "','" . $args['ent_zipcode'] . "','" . $args['ent_dev_id'] . "','2','" . $args['ent_payment_type'] . "','" . $args['ent_wrk_type'] . "','" . $args['ent_additional_info'] . "','".$args['ent_surge']."')";

            mysql_query($insertAppointmentQry, $this->db->conn);

            $pubnubContent1 = array('a' => 12, 'bid' => mysql_insert_id());

            $pushNum['pubnub'] = $this->pubnub->publish(array(
                'channel' => 'dispatcher',
                'message' => $pubnubContent1
            ));

            return $this->_getStatusMessage(78, 78);
        }

        if ($args['ent_dri_email'] == '')
            return $this->_getStatusMessage(1, 'Driver email');

        $location = $this->mongo->selectCollection('location');

        $location->ensureIndex(array('location' => '2d'));

        $doc = $location->findOne(array('email' => $args['ent_dri_email']));

        if (count($doc) <= 0)
            return $this->_getStatusMessage(64, 64);

        if ((int) $doc['user'] == 0)
            return $this->_getStatusMessage(64, 64);

        $updateStatus = $this->_updateSlvApptStatus($this->User['entityId'], "1");

        $vehicletypes = $this->mongo->selectCollection('vehicleTypes');
        $vehicletypesRes = $vehicletypes->findOne(array('type' => (int) $args['ent_wrk_type']));


        $pushNum = array();

        $master = array("id" => $doc["user"], 'rating' => $doc['rating'], 'fname' => $doc['name'], 'image' => $doc['image'], 'email' => $doc['email'], 'lat' => $doc['location']['latitude'], 'lon' => $doc['location']['longitude'], 'carId' => $doc['carId'], 'chn' => $doc['chn'], 'listner' => $doc['listner'], 'type_id' => $doc['type'], 'carimage' => $vehicletypesRes['type_on_image'], 'carmapimage' => $vehicletypesRes['type_map_image']);

        if ($doc['inBooking'] == 2)
            return $this->_getStatusMessage(71, $pushNum);
        
        if($doc['type'] != $args['ent_wrk_type']) 
            return $this->_getStatusMessage(129, $pushNum);
        
        if ((int) $doc['status'] != 3) {
//do nothing
        } else {

            $checkAppointmentQry = "select * from appointment where appointment_dt = '" . (($args['ent_later_dt'] == '') ? $this->curr_date_time : $args['ent_later_dt']) . "' and mas_id = '" . $master['id'] . "' and status IN (1,2)";
            if (mysql_num_rows(mysql_query($checkAppointmentQry, $this->db->conn)) > 0) {
                $location->update(array('user' => (int) $master['id']), array('$set' => array('inBooking' => 1)));
                return $this->_getStatusMessage(71, $pushNum);
            }

            
            $location->update(array('user' => (int) $master['id']), array('$set' => array('inBooking' => 2)));

            $insertAppointmentQry = "insert into appointment(mas_id,slave_id,created_dt,last_modified_dt,status,appointment_dt,address_line1,address_line2,appt_lat,appt_long,drop_addr1,drop_addr2,drop_lat,drop_long,extra_notes,amount,zipcode,user_device,appt_type,car_id,payment_type,type_id,coupon_code,expire_ts,additional_info,surge) 
            values('" . $master['id'] . "','" . $this->User['entityId'] . "','" . $this->curr_date_time . "','" . $this->curr_date_time . "','1',
                '" . (($args['ent_later_dt'] == '') ? $this->curr_date_time : $args['ent_later_dt']) . "','" . $args['ent_addr_line1'] . "','" . $args['ent_addr_line2'] . "','" . $args['ent_lat'] . "',
                '" . $args['ent_long'] . "','" . $args['ent_drop_addr_line1'] . "','" . $args['ent_drop_addr_line2'] . "','" . $args['ent_drop_lat'] . "',
                '" . $args['ent_drop_long'] . "','" . $args['ent_extra_notes'] . "','" . ((float)$typeAmount['min_fare'] * $args['ent_surge']) . "','" . $args['ent_zipcode'] . "','" . $args['ent_dev_id'] . "','" . (($args['ent_later_dt'] == '') ? '1' : '2') . "','" . $master['carId'] . "','" . $args['ent_payment_type'] . "','" . $master['type_id'] . "','" . $args['ent_coupon'] . "','" . (time() + $this->expireTimeForDriver) . "','" . $args['ent_additional_info'] . "','".$args['ent_surge']."')";

            mysql_query($insertAppointmentQry, $this->db->conn);

            $apptId = mysql_insert_id();

           if ($aptId > 0) {
                if ($args['ent_coupon'] != '' && $couponDet['coupon_type'] == '3') {

                    $data = array('booking_id' => $aptId, 'slave_id' => (string) $this->User['entityId'], 'email' => $this->User['email'], 'status' => 0);

                    $couponsColl->update(array('_id' => $couponDet['_id']), array('$push' => array('bookings' => $data)));
                }
            }

            if ($args['ent_later_dt'] == '' && $args['ent_addr_line2'] == '') {
                $message = "You got a new job request from " . $this->User['firstName'];
            } else if ($args['ent_later_dt'] == '' && $args['ent_addr_line2'] != '') {
                $exploded = explode(" ", $args['ent_addr_line2']);
                $message = "You got a new job request in " . $exploded[0] . $exploded[1] . " from " . $this->User['firstName'];
            } else if ($args['ent_later_dt'] != '' && $args['ent_addr_line2'] == '') {
                $message = "New Job from " . $this->User['firstName'] . " for " . date('jS M \a\t g:i A', strtotime($args['ent_later_dt']));
            } else if ($args['ent_later_dt'] != '' && $args['ent_addr_line2'] != '') {
                $exploded = explode(" ", $args['ent_addr_line2']);
                $message = "New Job in " . $exploded[0] . $exploded[1] . " from " . $this->User['firstName'] . " for " . date('jS M \a\t g:i A', strtotime($args['ent_later_dt']));
            }

            $this->ios_cert_path = IOS_DRIVER_PEM_PATH;
            $this->ios_cert_pwd = IOS_DRIVER_PEM_PASS;
            $this->androidApiKey = ANDROID_DRIVER_PUSH_KEY;

            $explodeAddr2 = explode(',', $args['ent_addr_line1']);

            $aplPushContent = array('alert' => $message, 'nt' => (($args['ent_later_dt'] == '') ? '7' : '51'), 'sname' => $this->User['firstName'], 'dt' => (($args['ent_later_dt'] == '') ? $this->curr_date_time : $args['ent_later_dt']), 'e' => $this->User['email'], 'sound' => 'default', 'bid' => $apptId, 'ltg' => $args['ent_lat'] . ',' . $args['ent_long'], 'adr2' => ($args['ent_addr_line2'] == '' ? $explodeAddr2[0] . ' ' . $explodeAddr2[1] : $args['ent_addr_line2']), 'chn' => 'qp_' . $args['ent_dev_id']);
            $andrPushContent = array("payload" => $message, 'action' => (($args['ent_later_dt'] == '') ? '7' : '51'), 'sname' => $this->User['firstName'], 'dt' => (($args['ent_later_dt'] == '') ? $this->curr_date_time : $args['ent_later_dt']), 'e' => $this->User['email'], 'bid' => $apptId, 'ltg' => $args['ent_lat'] . ',' . $args['ent_long'], 'adr2' => ($args['ent_addr_line2'] == '' ? $explodeAddr2[0] . ' ' . $explodeAddr2[1] : $args['ent_addr_line2']), 'chn' => 'qp_' . $args['ent_dev_id']);

            $pubnubContent = array('a' => 11, 'dt' => (($args['ent_later_dt'] == '') ? $this->curr_date_time : $args['ent_later_dt']), 'e' => $this->User['email'], 'bid' => $apptId, 'nt' => (($args['ent_later_dt'] == '') ? '' : '51'), 'ltg' => $args['ent_lat'] . ',' . $args['ent_long'], 'adr2' => ($args['ent_addr_line2'] == '' ? $explodeAddr2[0] . ' ' . $explodeAddr2[1] : $args['ent_addr_line2']), 'chn' => 'qp_' . $args['ent_dev_id']);

            if (!is_null($master['listner']))
                $pushNum['pubnub'] = $this->pubnub->publish(array(
                    'channel' => $master['listner'],
                    'message' => $pubnubContent
                ));

            $pushNum['push'] = $this->_sendPush($this->User['entityId'], array($master['id']), $message, '7', $this->User['firstName'], $this->curr_date_time, '1', $aplPushContent, $andrPushContent);

            $notifications = $this->mongo->selectCollection('notifications');

            $notifications->insert(array('args' => $args, 'pubnub' => $pubnubContent, 'test' => $pushNum));

            $time = $this->expireTimeForDriver * 2;
            
            

            for ($j = 1; $j < $time; $j++) {
                if ($j < $time)
                    usleep(500000);
                $getStatus = $this->_getSlvApptStatus($this->User['entityId']);



                if ($getStatus['booking_status'] == '3') {

                    mysql_query("update appointment set status = '4', cancel_status = '1', cancel_dt = '" . $this->curr_date_time . "' where appointment_id = '" . $apptId . "'", $this->db->conn);

                    $location->update(array('user' => (int) $master['id']), array('$set' => array('inBooking' => 1)));
                    $update = $this->_updateSlvApptStatus($this->User['entityId'], "0");
                    return $this->_getStatusMessage(74, $update);
                }

                $statusCheckQry = "select a.status,(select Vehicle_Image from workplace where workplace_id = d.workplace_id) as carPic from appointment a, master d where appointment_id = '" . $apptId . "'  AND d.mas_id = a.mas_id";
                $statusArr = mysql_fetch_assoc(mysql_query($statusCheckQry, $this->db->conn));

                if ($j == ($time - 1) && $statusArr['status'] == '1')
                    mysql_query("update appointment set status = '10' where appointment_id = '" . $apptId . "'", $this->db->conn);


                if ($statusArr['status'] == '6' || $statusArr['status'] == '2' || $statusArr['status'] == '7') {

                    if ($args['ent_coupon'] != '') {

                        if ($couponDet['coupon_type'] == '3') {
                            $couponsColl->update(array('coupon_code' => $args['ent_coupon'], 'status' => 0), array('$set' => array('status' => 1)));
                            $couponsColl->update(array('coupon_code' => $args['ent_coupon'], 'bookings.booking_id' => (int) $apptId), array('$set' => array('bookings.$.status' => 2)));
                        } else {
                            $couponsColl->update(array('coupon_code' => $args['ent_coupon'], 'status' => 0), array('$push' => array('bookings' => array('booking_id' => $apptId, 'slave_id' => (string) $this->User['entityId'], 'status' => 1))));
                        }
                    }

                    $location->update(array('user' => (int) $master['id']), array('$set' => array('status' => ($args['ent_later_dt'] == '') ? 5 : 3, 'inBooking' => 1)));

                    $getVehicleDataQry = "select wrk.workplace_id, wrk.License_Plate_No, (select v.vehiclemodel from vehiclemodel v, workplace w where w.Vehicle_Model = v.id and w.workplace_id = wrk.workplace_id) as vehicle_model from workplace wrk, master m where m.workplace_id = wrk.workplace_id and m.mas_id = '" . $master['id'] . "'";

                    $getVehicleDataRes = mysql_query($getVehicleDataQry, $this->db->conn);

                    $vehicleData = mysql_fetch_assoc($getVehicleDataRes);

                    $errMsgArr = $this->_getStatusMessage(($args['ent_later_dt'] == '') ? 39 : 78, $pushNum);

                    return array('errNum' => $errMsgArr['errNum'], 'errFlag' => $errMsgArr['errFlag'], 'errMsg' => $errMsgArr['errMsg'], 'email' => $master['email'], 'apptDt' => $this->curr_date_time, 'dt' => str_replace(' ', '', str_replace('-', '', str_replace(':', '', $this->curr_date_time))), 'model' => $vehicleData['vehicle_model'], 'plateNo' => $vehicleData['License_Plate_No'], 'rating' => round($master['rating'], 1), 'chn' => $master['chn'], 'bid' => $apptId, "drivername" => $master['fname'], 'driverPic' => $master['image'], 'carMapImage' => $vehicletypesRes['type_map_image'], 'carImage' => $statusArr['carPic']);
                }

                if ($statusArr['status'] == '3') {
                    $location->update(array('user' => (int) $master['id']), array('$set' => array('inBooking' => 1)));
                    return $this->_getStatusMessage(71, $pushNum);
                }
            }
        }
        $location->update(array('user' => (int) $master['id']), array('$set' => array('inBooking' => 1)));
        return $this->_getStatusMessage(71, $pushNum);
    }

    /*
     * Method name: getAppointmentDetails
     * Desc: Get appointment details of a given slot
     * Input: Request data
     * Output:  success if got it else error according to the result
     */

    protected function getAppointmentDetails($args) {

//
//        $notifications = $this->mongo->selectCollection('notifications');
//
//        $notifications->insert(array('res' => $args));


        if ($args['ent_appnt_dt'] == '')
            return $this->_getStatusMessage(1, 'Booking date time');
        else if ($args['ent_email'] == '')
            return $this->_getStatusMessage(1, 'Email');
        else if ($args['ent_date_time'] == '')
            return $this->_getStatusMessage(1, 'Date time');

        $this->curr_date_time = urldecode($args['ent_date_time']);

        if ($args['ent_user_type'] == '')
            $args['ent_user_type'] = '2';

        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], $args['ent_user_type']);



        if (is_array($returned))
            return $returned;

        $args['ent_appnt_dt'] = urldecode($args['ent_appnt_dt']);

        $docDet = $this->_getEntityDet($args['ent_email'], ($args['ent_user_type'] == '1') ? '2' : '1');

        $location = $this->mongo->selectCollection('location');

        if (!is_array($docDet))
            return $this->_getStatusMessage(37, 37);

        if ($args['ent_user_type'] == '2') {
            $selectAppntsQry = "select a.surge,a.cancel_status,a.cancel_amt,a.cc_fee,a.coupon_code,a.discount,a.tip_amount,a.tip_percent,a.waiting_mts,a.start_dt,a.meter_fee,a.toll_fee,a.airport_fee,a.parking_fee,a.apprxAmt, a.distance_in_mts, a.payment_type, a.payment_status, a.status, a.amount, a.address_line1, a.address_line2, a.drop_addr1, a.drop_addr2, a.duration, a.appt_lat, a.appt_long, a.drop_lat, a.arrive_dt, a.complete_dt, a.drop_long, a.appointment_id, a.appt_type, a.mas_id, d.profile_pic, d.mobile, d.first_name, d.last_name, d.email, ";
            $selectAppntsQry .= "(select avg(star_rating) from master_ratings where mas_id = d.mas_id) as rating,(select License_Plate_No from workplace where workplace_id = a.car_id) as licencePlate,(select v.vehiclemodel from vehiclemodel v, workplace w where w.Vehicle_Model = v.id and w.workplace_id = a.car_id) as vehicle_model, ";
            $selectAppntsQry .= "(select wt.basefare from workplace_types wt, workplace w, master d where w.type_id = wt.type_id and w.workplace_id = d.workplace_id and d.mas_id = a.mas_id) as base_fare,";
            $selectAppntsQry .= "(select wt.price_per_min from workplace_types wt, workplace w, master d where w.type_id = wt.type_id and w.workplace_id = d.workplace_id and d.mas_id = a.mas_id) as timeFee,";
            $selectAppntsQry .= "(select wt.min_fare from workplace_types wt, workplace w, master d where w.type_id = wt.type_id and w.workplace_id = d.workplace_id and d.mas_id = a.mas_id) as min_fare,";
            $selectAppntsQry .= "(select wt.price_per_km from workplace_types wt, workplace w, master d where w.type_id = wt.type_id and w.workplace_id = d.workplace_id and d.mas_id = a.mas_id) as distanceFee,";
            $selectAppntsQry .= "(select Vehicle_Image from workplace where workplace_id = d.workplace_id) as carPic,(select MapIcon from workplace_types where type_id = a.type_id) as mapicon, ";
            $selectAppntsQry .= "(select report_msg from reports where appointment_id = a.appointment_id limit 0, 1) as report_msg ";
            $selectAppntsQry .= " from appointment a, master d ";
            $selectAppntsQry .= " where a.mas_id = d.mas_id and a.appointment_dt = '" . $args['ent_appnt_dt'] . "' and d.email = '" . $args['ent_email'] . "' and a.status != 10 order by a.appointment_id DESC"; // and a.status IN (2,5,6,7)

            list($date, $time) = explode(' ', $args['ent_appnt_dt']);
            list($year, $month, $day) = explode('-', $date);
            list($hour, $minute, $second) = explode(':', $time);

            $dateNumber = $year . $month . $day . $hour . $minute . $second;
        } else {
            $selectAppntsQry = "select a.surge,a.additional_info,a.expire_ts,a.cancel_status,a.cancel_amt,a.coupon_code,a.discount,a.tip_amount,a.tip_percent, a.waiting_mts,a.start_dt,a.meter_fee,a.toll_fee,a.airport_fee,a.parking_fee,a.apprxAmt, a.distance_in_mts, a.payment_type, a.payment_status, a.status, a.amount, a.address_line1, a.address_line2, a.drop_addr1, a.drop_addr2, a.duration, a.appt_lat, a.appt_long, a.drop_lat, a.drop_long, a.arrive_dt, a.complete_dt, a.appointment_id, a.appt_type, a.user_device, a.mas_id, s.profile_pic, s.phone as mobile, s.first_name, s.last_name, s.email, ";
            $selectAppntsQry .= "(select report_msg from reports where appointment_id = a.appointment_id limit 0, 1) as report_msg ";
            $selectAppntsQry .= " from appointment a, slave s where a.slave_id = s.slave_id and a.slave_id = '" . $docDet['slave_id'] . "' and a.appointment_dt = '" . $args['ent_appnt_dt'] . "' and a.mas_id = '" . $this->User['entityId'] . "' and a.status != 10 order by a.appointment_id DESC "; // and a.status NOT IN (3,8)
        }
        $selectAppntsRes = mysql_query($selectAppntsQry, $this->db->conn);

        if (mysql_num_rows($selectAppntsRes) <= 0)
            return $this->_getStatusMessage(62, $selectAppntsQry);

        $apptData = mysql_fetch_assoc($selectAppntsRes);

        if ($apptData['status'] == '10')
            return $this->_getStatusMessage(72, 72);

        if ($apptData['cancel_status'] == '3') {
            $fare = $apptData['cancel_amt'];
            $amount = $apptData['cancel_amt'];
        } else {
            $amount = $apptData['amount'] + $apptData['tip_amount'];
            $total = $apptData['airport_fee'] + $apptData['parking_fee'] + $apptData['toll_fee'] + $apptData['meter_fee'];
            $fareCheck = $total; // - $apptData['discount'];
            if ($apptData['status'] != '9')
                $fareCheck = $total - $apptData['discount'];
            $fare = ($fareCheck < 0) ? 0 : $fareCheck;
        }

        $avgSpeedKmHour = ($apptData['distance_in_mts'] / ($apptData['duration'] * 60)) * 3.6;
        $dis_in_miles = (float) ($apptData['distance_in_mts'] / $this->distanceMetersByUnits);

        $errMsgArr = $this->_getStatusMessage(21, 2);


        $masterData = $location->findOne(array('user' => (int) $apptData['mas_id']));

        if ($args['ent_user_type'] == '2') {

            if ($apptData['status'] == '4' && $apptData['payment_status'] == '' && $apptData['cancel_status'] == '3')
                $payStatus = 0;
            else if ($apptData['status'] == '9' && ($apptData['payment_status'] == '' || $apptData['payment_status'] == '2'))
                $payStatus = 0;
            else
                $payStatus = 1;
            
            

            $apptData['base_fare'] = (double) ($apptData['base_fare'] * $apptData['surge']);
            $apptData['timeFee'] = (double)(($apptData['duration'] * $apptData['timeFee']) * $apptData['surge']  );
            $apptData['distanceFee'] = (double)(($apptData['app_distance'] * $apptData['distanceFee']) * $apptData['surge']);
            $subtotal = ($apptData['base_fare'] + $apptData['timeFee'] + $apptData['distanceFee'] + $apptData['airport_fee'] + $apptData['parking_fee'] + $apptData['toll_fee'])-$apptData['discount'];

            return array('errNum' => $errMsgArr['errNum'], 'errFlag' => $errMsgArr['errFlag'], 'errMsg' => $errMsgArr['errMsg'],
                'cancel_status' => $apptData['cancel_status'],
                'cancelAmt' => $apptData['cancel_amt'],
                'code' => $apptData['coupon_code'], 'discount' => $apptData['discount'],
                'tip' => $apptData['tip_amount'],
                'baseFee' => $apptData['base_fare'],
                'min_fare' => (double) ($apptData['min_fare'] * $apptData['surge']),
                'mobile' => $apptData['mobile'],
                'distanceFee' => $apptData['distanceFee'],
                'timeFee' => $apptData['timeFee'],
                'subTotal' => $subtotal,
                'share' => $this->share . $apptData['appointment_id'],
                'tipPercent' => $apptData['tip_percent'],
                'waitTime' => (int) $apptData['waiting_mts'],
                'statCode' => $apptData['status'],
                'meterFee' => $apptData['meter_fee'],
                'tollFee' => $apptData['toll_fee'],
                'airportFee' => $apptData['airport_fee'],
                'parkingFee' => $apptData['parking_fee'],
                'fName' => $apptData['first_name'],
                'lName' => $apptData['last_name'],
                'addr1' => urldecode($apptData['address_line1']),
                'carImage' => $apptData['carPic'],
                'model' => $apptData['vehicle_model'],
                'plateNo' => $apptData['licencePlate'],
                'carMapImage' => $apptData['mapicon'],
                'chn' => $masterData['chn'],
                'dropAddr1' => urldecode($apptData['drop_addr1']),
                'amount' => number_format($amount, 2, '.', ''),
                'pPic' => ($apptData['profile_pic'] == '') ? $this->default_profile_pic : $apptData['profile_pic'],
                'dis' => round($dis_in_miles, 2),
                'dur' => $apptData['duration'],
                'apptDt' => $args['ent_appnt_dt'],
                'pickupDt' => $apptData['start_dt'],
                'dropDt' => $apptData['complete_dt'],
                'email' => $apptData['email'],
                'dt' => $dateNumber,
                'bid' => $apptData['appointment_id'],
                'apptType' => $apptData['appt_type'],
                'payStatus' => $payStatus, 'reportMsg' => $apptData['report_msg'], 'payType' => $apptData['payment_type'], 'r' => round($apptData['rating'], 1));
        } else {

            //$arrCP = $this->_getDirectionsData(array('lat' => $masterData['location']['latitude'], 'long' => $masterData['location']['longitude']), array('lat' => $apptData['appt_lat'], 'long' => $apptData['appt_long']));

            $curr_to_pick_dis_in_km = 0; //round((float) $arrCP['routes'][0]['legs'][0]['distance']['value'] / $this->distanceMetersByUnits, 2);

            $data = array('errNum' => $errMsgArr['errNum'], 'errFlag' => $errMsgArr['errFlag'], 'errMsg' => $errMsgArr['errMsg'], 'data' => array(
                    'expireSec' => ($apptData['expire_ts'] - time() > 0 ? $apptData['expire_ts'] - time() : 0), 'r' => round($apptData['rating'], 1),
                   'surge' => $apptData['surge'],'code' => $apptData['coupon_code'], 'cancel_status' => $apptData['cancel_status'], 'cancelAmt' => $apptData['cancel_amt'],
                    'discount' => $apptData['discount'], 'tip' => $apptData['tip_amount'], 'tipPercent' => $apptData['tip_percent'],
                    'waitTime' => (int) $apptData['waiting_mts'], 'statCode' => $apptData['status'], 'meterFee' => $apptData['meter_fee'],
                    'tollFee' => $apptData['toll_fee'], 'airportFee' => $apptData['airport_fee'], 'parkingFee' => $apptData['parking_fee'],
                    'fName' => $apptData['first_name'], 'lName' => $apptData['last_name'], 'mobile' => $apptData['mobile'],
                    'addr1' => urldecode($apptData['address_line1']), 'addr2' => urldecode($apptData['address_line2']), 'additional_info' => $apptData['additional_info'],
                    'dropAddr1' => urldecode($apptData['drop_addr1']), 'dropAddr2' => urldecode($apptData['drop_addr2']),
                    'amount' => number_format($amount, 2, '.', ''), 'pPic' => ($apptData['profile_pic'] == '') ? $this->default_profile_pic : $apptData['profile_pic'],
                    'apptDis' => $curr_to_pick_dis_in_km, 'dis' => round($dis_in_miles, 2), 'dur' => $apptData['duration'], 'fare' => $fare,
                    'pickLat' => $apptData['appt_lat'], 'pickLong' => $apptData['appt_long'], 'dropLat' => $apptData['drop_lat'],
                    'dropLong' => $apptData['drop_long'], 'apptDt' => $args['ent_appnt_dt'], 'pickupDt' => $apptData['start_dt'],
                    'dropDt' => $apptData['complete_dt'], 'email' => $apptData['email'], 'apptType' => $apptData['appt_type'],
                    'bid' => $apptData['appointment_id'], 'pasChn' => 'qp_' . $apptData['user_device'], 'payStatus' => ($apptData['payment_status'] == '') ? 0 : $apptData['payment_status'],
                    'reportMsg' => $apptData['report_msg'], 'payType' => $apptData['payment_type'], 'avgSpeed' => $avgSpeedKmHour,
                    'apprAmount' => $apptData['apprxAmt'], 'masStatus' => $masterData['status']));


            $checkdata = $this->mongo->selectCollection('checkdata');
            $checkdata->insert(array('args' => $args, 'data' => $data));
            return $data;
        }
    }

    /*
     * Method name: updateSlaveReview
     * Desc: Update appointment review of an appointment
     * Input: Request data
     * Output:  success if got it else error according to the result
     */

    protected function updateSlaveReview($args) {

        if ($args['ent_appnt_dt'] == '')
            return $this->_getStatusMessage(1, 'Booking date time');
        else if ($args['ent_dri_email'] == '')
            return $this->_getStatusMessage(1, 'Driver email');
        else if ($args['ent_date_time'] == '')
            return $this->_getStatusMessage(1, 'Date time');
        else if ($args['ent_rating_num'] == '')
            return $this->_getStatusMessage(1, 'Rating');

        $this->curr_date_time = urldecode($args['ent_date_time']);

        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], '2');

        if (is_array($returned))
            return $returned;

        $args['ent_appnt_dt'] = urldecode($args['ent_appnt_dt']);

        $masDet = $this->_getEntityDet($args['ent_dri_email'], '1');

        if (!is_array($masDet))
            return $this->_getStatusMessage(37, 37);

        $selectApptQry = "select appointment_id from appointment where slave_id = '" . $this->User['entityId'] . "' and mas_id = '" . $masDet['mas_id'] . "' and appointment_dt = '" . $args['ent_appnt_dt'] . "'";
        $selectApptRes = mysql_query($selectApptQry, $this->db->conn);

        if (mysql_num_rows($selectApptRes) <= 0)
            return $this->_getStatusMessage(62, 62);

        $appt = mysql_fetch_assoc($selectApptRes);

        $insertReviewQry = "insert into master_ratings(mas_id, slave_id, review_dt, star_rating, review, appointment_id) values('" . $masDet['mas_id'] . "', '" . $this->User['entityId'] . "', '" . $this->curr_date_time . "', '" . $args['ent_rating_num'] . "', '" . $args['ent_review_msg'] . "', '" . $appt['appointment_id'] . "')";
        mysql_query($insertReviewQry, $this->db->conn);

        $selectAvgRatQry = "select avg(star_rating) as avg from master_ratings where mas_id = '" . $masDet['mas_id'] . "' and status = 1";
        $selectAvgRatRes = mysql_query($selectAvgRatQry, $this->db->conn);

        $avgRow = mysql_fetch_assoc($selectAvgRatRes);

        if ($args['ent_fav'] == '0') {
            $favourite = $this->mongo->selectCollection('favourite');
            $insertData = array('passenger' => (int) $this->User['entityId'], 'driver' => (int) $masDet['mas_id'], 'pasEmail' => $this->User['email']);
            if (!is_array($favourite->findOne($insertData)))
                $favourite->insert($insertData);
        }

        $location = $this->mongo->selectCollection('location');

        $location->update(array('user' => (int) $masDet['mas_id']), array('$set' => array('rating' => (float) $avgRow['avg'])));

//        if (mysql_affected_rows($insertReviewRes) > 0)
        return $this->_getStatusMessage(63, 12);
//        else
//            return $this->_getStatusMessage(3, $insertReviewQry);
    }

    /*
     * Method name: getSlaveAppointments
     * Desc: Get Passenger appointments
     * Input: Request data
     * Output:  success if got it else error according to the result
     */

//    protected function getSlaveAppointments($args) {
//
//        if ($args['ent_appnt_dt'] == '')
//            return $this->_getStatusMessage(1, 'Booking date time');
//        else if ($args['ent_date_time'] == '')
//            return $this->_getStatusMessage(1, 'Date time');
//
//        $this->curr_date_time = urldecode($args['ent_date_time']);
//
//        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], '2');
//
//        if (is_array($returned))
//            return $returned;
//
//        $args['ent_appnt_dt'] = urldecode($args['ent_appnt_dt']);
//
//        $dates = explode('-', $args['ent_appnt_dt']);
//
//        $args['ent_appnt_dt'] = $args['ent_appnt_dt'] . '-01';
//        $endDate = date('Y-m-d', strtotime('+1 month', strtotime($args['ent_appnt_dt'])));
//        $selectStr = " YEAR(a.appointment_dt) = '" . $dates[0] . "' and MONTH(a.appointment_dt) = '" . $dates[1] . "'";
//
//        $selectAppntsQry = "select d.profile_pic, d.first_name, d.mobile, d.email,a.cancel_status,a.cancel_amt, a.appt_lat, a.appt_long, a.appointment_dt, a.amount,a.tip_amount, a.extra_notes, a.address_line1, a.payment_status, a.address_line2, a.drop_addr1, a.drop_addr2, a.status, a.distance_in_mts, a.appt_type, (select count(appointment_id) from appointment where status = 1 and slave_id = '" . $this->User['entityId'] . "') as pen_count from appointment a, master d ";
//        $selectAppntsQry .= " where d.mas_id = a.mas_id and a.slave_id = '" . $this->User['entityId'] . "' and " . $selectStr . " and ((a.status in (4,5,9) and a.appt_type = 1) or ((a.status NOT in (3,10) and a.appt_type = 2)) ) order by a.appointment_id DESC"; // and a.appointment_dt >= '" . $curr_date_bfr_1hr . "'//a.status NOT in (3,4,7) 
//
//        $selectAppntsRes = mysql_query($selectAppntsQry, $this->db->conn);
//
//        if (mysql_num_rows($selectAppntsRes) <= 0)
//            return $this->_getStatusMessage(65, $selectAppntsQry);
//
//        $appointments = $daysArr = $sortedApnts = array();
//
//        $pendingCount = 0;
//
//        while ($appnt = mysql_fetch_assoc($selectAppntsRes)) {
//
//            if ($appnt['profile_pic'] == '')
//                $appnt['profile_pic'] = $this->default_profile_pic;
//
//            $pendingCount = $appnt['pen_count'];
//
//            $aptdate = date('Y-m-d', strtotime($appnt['appointment_dt']));
//
//            if ($appnt['status'] == '1')
//                $status = 'Booking requested';
//            else if ($appnt['status'] == '2')
//                $status = 'Driver accepted.';
//            else if ($appnt['status'] == '3')
//                $status = 'Driver rejected.';
//            else if ($appnt['status'] == '4')
//                $status = 'You cancelled.';
//            else if ($appnt['status'] == '5')
//                $status = 'Driver cancelled.';
//            else if ($appnt['status'] == '6')
//                $status = 'Driver is on the way.';
//            else if ($appnt['status'] == '7')
//                $status = 'Driver arrived.';
//            else if ($appnt['status'] == '8')
//                $status = 'Booking started.';
//            else if ($appnt['status'] == '9' && $appnt['payment_status'] == '')
//                $status = 'Completed, Payment not done.';
//            else if ($appnt['status'] == '9' && ($appnt['payment_status'] == '1' || $appnt['payment_status'] == '3'))
//                $status = 'Completed, Payment done.';
//            else if ($appnt['status'] == '9' && $appnt['payment_status'] == '2')
//                $status = 'Completed, Payment done.';
//            else if ($appnt['status'] == '10')
//                $status = 'Booking expired.';
//            else
//                $status = 'Status unavailable.';
//
//            $amount = ($appnt['cancel_status'] == '3' ? $appnt['cancel_amt'] : ((in_array($appnt['payment_status'], array(1, 3))) ? round($appnt['amount'], 2) : 0));
//
//            $appointments[$aptdate][] = array('apntDt' => $appnt['appointment_dt'], 'pPic' => $appnt['profile_pic'], 'email' => $appnt['email'], 'status' => $status, 'apptType' => $appnt['appt_type'],
//                'fname' => $appnt['first_name'], 'phone' => $appnt['mobile'], 'apntTime' => date('h:i a', strtotime($appnt['appointment_dt'])), 'cancel_status' => $appnt['cancel_status'], 'cancelAmt' => $appnt['cancel_amt'],
//                'apntDate' => date('Y-m-d', strtotime($appnt['appointment_dt'])), 'apptLat' => (double) $appnt['appt_lat'], 'apptLong' => (double) $appnt['appt_long'], 'payStatus' => ($appnt['payment_status'] == '' || $appnt['payment_status'] == '2') ? 0 : $appnt['payment_status'],
//                'addrLine1' => urldecode($appnt['address_line1']), 'addrLine2' => urldecode($appnt['address_line2']), 'dropLine1' => urldecode($appnt['drop_addr1']), 'dropLine2' => urldecode($appnt['drop_addr2']), 'notes' => $appnt['extra_notes'], 'bookType' => $args['booking_type'], 'amount' => ($amount + $appnt['tip_amount'])  , 'statCode' => $appnt['status'], 'distance' => round(($appnt['distance_in_mts'] / $this->distanceMetersByUnits), 2));
//        }
//
//        $i = 1;
//        $refIndexes = array();
//        $date = $args['ent_appnt_dt'];
//
//        $lastDate = $endDate;
//
//        while ($date < $endDate) {
////        while ($endDate > $date) {
////            $empty_arr = array();
//
//            if (is_array($appointments[$lastDate])) {
//                $sortedApnts[] = array('date' => $lastDate, 'appt' => $appointments[$lastDate]);
//                $refIndexes[] = $i;
//            }
////            else {
////                $sortedApnts[$i] = $empty_arr;
////            }
//            $date = date('Y-m-d', strtotime('+1 day', strtotime($date)));
//            $lastDate = date('Y-m-d', strtotime('-1 day', strtotime($lastDate)));
//
//            $i++;
//        }
//
//        $errNum = 31;
//        if (count($sortedApnts) <= 0)
//            $errNum = 30;
//
//        $errMsgArr = $this->_getStatusMessage($errNum, 2);
//
//        return array('errNum' => $errMsgArr['errNum'], 'errFlag' => $errMsgArr['errFlag'], 'errMsg' => $errMsgArr['errMsg'], 'penCount' => $pendingCount, 'refIndex' => $refIndexes, 'appointments' => $sortedApnts); //,'test'=>$selectAppntsQry,'test1'=>$appointments);
//    }
    protected function getSlaveAppointments($args) {

        if ($args['ent_appnt_dt'] == '')
            return $this->_getStatusMessage(1, 'Booking date time');
        else if ($args['ent_date_time'] == '')
            return $this->_getStatusMessage(1, 'Date time');
        else if ($args['ent_page_index'] == '')
            return $this->_getStatusMessage(1, 'Page index');

        $this->curr_date_time = urldecode($args['ent_date_time']);

        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], '2');

        if (is_array($returned))
            return $returned;

        $pagelimit = ($args['ent_page_index'] * 10);

//        $counterAd ="SELECT COUNT(appointment_id) FROM appointment WHERE slave_id = '" . $this->User['entityId'] . "' AND ((STATUS IN (4,5,9) AND appt_type = 1) OR ((STATUS NOT IN (3,10) AND appt_type = 2)) )";
        $selectAppntsQry = "select d.profile_pic, d.first_name, d.mobile, d.email,a.cancel_status,a.cancel_amt, a.appt_lat, a.appt_long, a.appointment_dt, a.amount,a.tip_amount, a.extra_notes, a.address_line1, a.payment_status, a.address_line2, a.drop_addr1, a.drop_addr2, a.status, a.distance_in_mts, a.appt_type, (select count(appointment_id) from appointment where status = 1 and slave_id = '" . $this->User['entityId'] . "') as pen_count from appointment a, master d ";
        $selectAppntsQry .= " where d.mas_id = a.mas_id and a.slave_id = '" . $this->User['entityId'] . "'  and ((a.status in (4,5,9) and a.appt_type = 1) or ((a.status NOT in (3,10) and a.appt_type = 2)) ) order by a.appointment_id DESC LIMIT " .  $pagelimit  . ", 11"; // and a.appointment_dt >= '" . $curr_date_bfr_1hr . "'//a.status NOT in (3,4,7) 

        
//        return $this->_getStatusMessage(1, $selectAppntsQry);
        
        $selectAppntsRes = mysql_query($selectAppntsQry, $this->db->conn);

        if (mysql_num_rows($selectAppntsRes) <= 0)
            return $this->_getStatusMessage(65, $selectAppntsQry);

        $appointments = $daysArr = $sortedApnts = array();

        $pendingCount = 0;

        $counter = 0;
        while ($appnt = mysql_fetch_assoc($selectAppntsRes)) {

            if ($appnt['profile_pic'] == '')
                $appnt['profile_pic'] = $this->default_profile_pic;
            $counter++;

            $pendingCount = $appnt['pen_count'];

            $aptdate = date('Y-m-d', strtotime($appnt['appointment_dt']));

            if ($appnt['status'] == '1')
                $status = 'Booking requested';
            else if ($appnt['status'] == '2')
                $status = 'Driver accepted.';
            else if ($appnt['status'] == '3')
                $status = 'Driver rejected.';
            else if ($appnt['status'] == '4')
                $status = 'You cancelled.';
            else if ($appnt['status'] == '5')
                $status = 'Driver cancelled.';
            else if ($appnt['status'] == '6')
                $status = 'Driver is on the way.';
            else if ($appnt['status'] == '7')
                $status = 'Driver arrived.';
            else if ($appnt['status'] == '8')
                $status = 'Booking started.';
            else if ($appnt['status'] == '9' && $appnt['payment_status'] == '')
                $status = 'Completed, Payment not done.';
            else if ($appnt['status'] == '9' && ($appnt['payment_status'] == '1' || $appnt['payment_status'] == '3'))
                $status = 'Completed, Payment done.';
            else if ($appnt['status'] == '9' && $appnt['payment_status'] == '2')
                $status = 'Disputed, Payment done.';
            else if ($appnt['status'] == '10')
                $status = 'Booking expired.';
            else
                $status = 'Status unavailable.';
             $amount = $appnt['amount'];
//            $amount = ($appnt['cancel_status'] == '3' ? $appnt['cancel_amt'] : ((in_array($appnt['payment_status'], array(1, 3))) ? round($appnt['amount'], 2) : 0));
          if($counter <=10)
            $appointments[] = array('apntDt' => $appnt['appointment_dt'], 'pPic' => $appnt['profile_pic'], 'email' => $appnt['email'], 'status' => $status, 'apptType' => $appnt['appt_type'],
                'fname' => $appnt['first_name'], 'phone' => $appnt['mobile'], 'apntTime' => date('h:i a', strtotime($appnt['appointment_dt'])), 'cancel_status' => $appnt['cancel_status'], 'cancelAmt' => $appnt['cancel_amt'],
                'apntDate' => date('Y-m-d', strtotime($appnt['appointment_dt'])), 'apptLat' => (double) $appnt['appt_lat'], 'apptLong' => (double) $appnt['appt_long'], 'payStatus' => ($appnt['payment_status'] == '' || $appnt['payment_status'] == '2') ? 0 : $appnt['payment_status'],
                'addrLine1' => urldecode($appnt['address_line1']), 'addrLine2' => urldecode($appnt['address_line2']), 'dropLine1' => urldecode($appnt['drop_addr1']), 'dropLine2' => urldecode($appnt['drop_addr2']), 'notes' => $appnt['extra_notes'], 'bookType' => $args['booking_type'], 'amount' => ($amount + $appnt['tip_amount']), 'statCode' => $appnt['status'], 'distance' => round(($appnt['distance_in_mts'] / $this->distanceMetersByUnits), 2));
        }


        $errNum = 31;

        $errMsgArr = $this->_getStatusMessage($errNum, 2);

        return array('errNum' => $errMsgArr['errNum'],'lastcount' => ($counter > 10 ? 0 : 1 )   , 'errFlag' => $errMsgArr['errFlag'], 'errMsg' => $errMsgArr['errMsg'], 'appointments' => $appointments); //,'test'=>$selectAppntsQry,'test1'=>$appointments);
    }

    /*
     * Method name: respondToAppointment
     * Desc: Respond to appointment requeted
     * Input: Request data
     * Output:  success if got it else error according to the result
     */

    protected function respondToAppointment($args) {

        if ($args['ent_appnt_dt'] == '')
            return $this->_getStatusMessage(1, 'Booking date time');
        else if ($args['ent_response'] == '')
            return $this->_getStatusMessage(1, 'Response type');
        else if ($args['ent_pas_email'] == '')
            return $this->_getStatusMessage(1, 'User email');
        else if ($args['ent_book_type'] == '')
            return $this->_getStatusMessage(1, 'Booking type');
        else if ($args['ent_date_time'] == '')
            return $this->_getStatusMessage(1, 'Date time');

        $this->curr_date_time = urldecode($args['ent_date_time']);

        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], '1');

        if (is_array($returned))
            return $returned;

        $args['ent_appnt_dt'] = urldecode($args['ent_appnt_dt']);

        $patData = $this->_getEntityDet($args['ent_pas_email'], '2');

        $oneHourBefore = date('Y-m-d H:i:s', strtotime('-1 hour', strtotime($args['ent_appnt_dt'])));

        if ($args['ent_book_type'] == '1')
            $checkApptStr = " and appointment_dt between ('" . $oneHourBefore . "' and '" . $args['ent_appnt_dt'] . "')";
        else
            $checkApptStr = " and appointment_dt = '" . $args['ent_appnt_dt'] . "'";

        $getApptDetQry = "select status from appointments where mas_id = '" . $this->User['entityId'] . "' and status = '2'" . $checkApptStr;

        if (mysql_num_rows(mysql_query($getApptDetQry, $this->db->conn)) > 0)
            return $this->_getStatusMessage(60, 60);

        $getApptDetQry = "select status, appt_type from appointments where mas_id = '" . $this->User['entityId'] . "' and appointment_dt = '" . $args['ent_appnt_dt'] . "' and slave_id = '" . $patData['slave_id'] . "' order by appointment_id DESC";
        $apptDet = mysql_fetch_assoc(mysql_query($getApptDetQry, $this->db->conn));

        if ($apptDet['status'] == '4')
            return $this->_getStatusMessage(41, 3);

        if ($apptDet['status'] == '10')
            return $this->_getStatusMessage(72, 72);

        if ($apptDet['status'] > '1')
            return $this->_getStatusMessage(40, 40);

        $updateString = '';

//        if ($args['ent_book_type'] == '1')
//            $updateString = ", appointment_dt = '" . $this->curr_date_time . "'";

        $updateResponseQry = "update appointment set status = '" . $args['ent_response'] . "'" . $updateString . " where mas_id = '" . $this->User['entityId'] . "' and slave_id = '" . $patData['slave_id'] . "' and appointment_dt = '" . $args['ent_appnt_dt'] . "' and status = 1";
        mysql_query($updateResponseQry, $this->db->conn);

        if (mysql_affected_rows() <= 0)
            return $this->_getStatusMessage(3, $updateResponseQry);

//        if ($args['ent_response'] == '2') {
//            $notifType = 2;
//            $message = "Your appointment with " . $this->User['firstName'] . " is confirmed for " . date('m/d/Y h:i a', strtotime($args['ent_appnt_dt'])) . " on " . APP_NAME . "!";
//        } else {
//            $notifType = 10;
//            $message = "Your appointment with " . $this->User['firstName'] . " is rejected for " . date('m/d/Y h:i a', strtotime($args['ent_appnt_dt'])) . " on " . APP_NAME . "!";
//        }

        if ($args['ent_book_type'] == '1' && $args['ent_response'] == '2') {
//            $deleteAllSessionsQry = "update master set status = '5' where mas_id = '" . $this->User['entityId'] . "'";
//            mysql_query($deleteAllSessionsQry, $this->db->conn);
//            if (mysql_affected_rows() < 0)
//                return $this->_getStatusMessage(70, $deleteAllSessionsQry);

            $location = $this->mongo->selectCollection('location');

            $location->update(array('user' => (int) $this->User['entityId']), array('$set' => array('status' => 4)));
        } else if ($args['ent_book_type'] == '2' && $args['ent_response'] == '2') {
            
            
        }

//        $this->ios_cert_path = IOS_PASSENGER_PEM_PATH;
//        $this->ios_cert_pwd = IOS_PASSENGER_PEM_PASS;
//        $aplPushContent = array('alert' => $message, 'nt' => $notifType, 'sname' => $this->User['firstName'], 'dt' => $this->curr_date_time, 'sound' => 'default');
//        $andrPushContent = array("payload" => $message, 'action' => $notifType, 'sname' => $this->User['firstName'], 'dt' => $this->curr_date_time);
//        $pushNum = $this->_sendPush($this->User['entityId'], array($patData['slave_id']), $message, '2', $this->User['firstName'], $this->curr_date_time, '2', $aplPushContent, $andrPushContent);

        return $this->_getStatusMessage(40, 40);
    }

    /*
     * Method name: updateDropOff
     * Desc: Update slave profile data
     * Input: Request data
     * Output:  success array if got it else error according to the result
     */

    protected function updateDropOff($args) {

        if ($args['ent_booking_id'] == '')
            return $this->_getStatusMessage(1, 'Booking id');
        if ($args['ent_drop_addr1'] == '')
            return $this->_getStatusMessage(1, 'Drop address');
        else if ($args['ent_lat'] == '' || $args['ent_long'] == '')
            return $this->_getStatusMessage(1, 'Drop location');
        else if ($args['ent_date_time'] == '')
            return $this->_getStatusMessage(1, 'Date time');

        $this->curr_date_time = urldecode($args['ent_date_time']);

        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], '2');

        if (is_array($returned))
            return $returned;

        $getApptDetQry = "select a.appointment_dt, a.appt_type, a.status, a.mas_id, a.appointment_id, a.user_device from appointment a where a.appointment_id = '" . $args['ent_booking_id'] . "' and a.slave_id = '" . $this->User['entityId'] . "'";
        $getApptDetRes = mysql_query($getApptDetQry, $this->db->conn);

        if (mysql_affected_rows() <= 0)
            return $this->_getStatusMessage(32, 32);

        $apptDet = mysql_fetch_assoc($getApptDetRes);

        if (!is_array($apptDet))
            return $this->_getStatusMessage(32, 32);

        if ($apptDet['status'] == '3')
            return $this->_getStatusMessage(44, 44);

        if ($apptDet['status'] == '4')
            return $this->_getStatusMessage(41, 3);

        if ($apptDet['status'] == '5')
            return $this->_getStatusMessage(82, 3);

        if ($apptDet['status'] == '9')
            return $this->_getStatusMessage(75, 3);

        $updateQry = "update appointment set drop_addr1 = '" . $args['ent_drop_addr1'] . "',drop_addr2 = '" . $args['ent_drop_addr2'] . "',drop_lat = '" . $args['ent_lat'] . "',drop_long = '" . $args['ent_long'] . "',last_modified_dt = '" . $this->curr_date_time . "' where slave_id = '" . $this->User['entityId'] . "' and appointment_id = '" . $args['ent_booking_id'] . "'";
        mysql_query($updateQry, $this->db->conn);

        if (mysql_affected_rows() < 0)
            return $this->_getStatusMessage(3, 39);


//            $pubnubContent = array('a' => 22, 'bid');
//
//            if (!is_null($master['listner']))
//                $pushNum['pubnub'] = $this->pubnub->publish(array(
//                    'channel' => $master['listner'],
//                    'message' => $pubnubContent
//                ));


        $aplPushContent = array('alert' => 'Drop off is updated by customer', 'nt' => '22', 'sound' => 'default', 'adr' => $args['ent_drop_addr1'] . $args['ent_drop_addr2'], 'ltg' => $args['ent_lat'] . ',' . $args['ent_long']);
        $andrPushContent = array("payload" => 'Drop off is updated by customer', 'action' => '22', 'adr' => $args['ent_drop_addr1'] . $args['ent_drop_addr2'], 'ltg' => $args['ent_lat'] . ',' . $args['ent_long']);

        $this->androidApiKey = $this->masterApiKey;
        $this->ios_cert_path = $this->ios_roadyo_driver;
        $this->ios_cert_pwd = $this->ios_dri_pwd;

        $sendPush = $this->_sendPush($this->User['entityId'], array($apptDet['mas_id']), $args['ent_message'], '21', $this->User['firstName'], $this->curr_date_time, '1', $aplPushContent, $andrPushContent);

        return $this->_getStatusMessage(123, $sendPush);
    }

    private function _distance($lat1, $lon1, $lat2, $lon2, $unit) {

        $theta = $lon1 - $lon2;
        if ($theta == 0) {
            return 0;
        }
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }

    /*
     * Method name: updateApptDetails
     * Desc: Update appointment details
     * Input: Request data
     * Output:  success if got it else error according to the result
     */

    protected function updateApptDetails($args) {

//
       $booking_data_livetrack = $this->mongo->selectCollection('updateapptdetailsdata');

        


        if ($args['ent_appnt_id'] == '')
            return $this->_getStatusMessage(1, 'Booking id');
        else if ($args['ent_drop_addr_line1'] == '')
            return $this->_getStatusMessage(1, 'Drop address');
        else if ($args['ent_distance'] == '')
            return $this->_getStatusMessage(1, 'Distance');
        else if ($args['ent_date_time'] == '')
            return $this->_getStatusMessage(1, 'Date time');
//        else if ($args['ent_cityid'] == '')
//            return $this->_getStatusMessage(1, 'city id ');

        $this->curr_date_time = urldecode($args['ent_date_time']);

        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], '1');

        if (is_array($returned))
            return $returned;

        $getApptDetQry = "select a.surge,a.tip_percent,a.expire_ts,a.coupon_code,a.status, a.appt_lat, a.appt_long, a.payment_type, a.drop_lat, a.drop_long, a.address_line1, a.address_line2, a.drop_addr1, a.drop_addr2, a.created_dt, a.arrive_dt, a.start_dt, a.appointment_dt, a.amount, a.appointment_id, a.last_modified_dt, a.user_device, ";
        $getApptDetQry .= "(select wt.price_per_km from workplace_types wt, workplace w, master d where w.type_id = wt.type_id and w.workplace_id = d.workplace_id and d.mas_id = a.mas_id) as price_per_km, ";
        $getApptDetQry .= "(select wt.price_per_min from workplace_types wt, workplace w, master d where w.type_id = wt.type_id and w.workplace_id = d.workplace_id and d.mas_id = a.mas_id) as price_per_min, ";
        $getApptDetQry .= "(select wt.basefare from workplace_types wt, workplace w, master d where w.type_id = wt.type_id and w.workplace_id = d.workplace_id and d.mas_id = a.mas_id) as base_fare ";
        $getApptDetQry .= " from appointment a where a.appointment_id = '" . $args['ent_appnt_id'] . "'";

        $apptDet = mysql_fetch_assoc(mysql_query($getApptDetQry, $this->db->conn));

        if ($apptDet['status'] != '8')
            return $this->_getStatusMessage(91, $getApptDetQry);

        $booking_route = $this->mongo->selectCollection('booking_route');

        $booking_route->update(array('bid' => (int) $args['ent_appnt_id']), array('$set' => array('app_route' => $args['ent_app_jsonLatLong'])));


        $route = $booking_route->findOne(array('appointment_id' => (int) $args['ent_appnt_id']));

        $route['route'][] = array("longitude" => (double) $args['ent_drop_long'], "latitude" => (double) $args['ent_drop_lat']);

        $distance = 0;

        $lat2 = $lon2 = "";

        $unit = APP_DISTANCE_METRIC;

        foreach ($route['route'] as $latlong) {


            $lat2 = $latlong['latitude'];
            $lon2 = $latlong['longitude'];

            if ($lat2 != '' && $lon2 != '')
                $distance += $this->_distance($latlong['latitude'], $latlong['longitude'], $lat2, $lon2, $unit[0]);
        }

        $duration_in_mts_old = round(abs(gmmktime() - $apptDet['expire_ts']) / 60, 2);

        $duration_in_mts = ((int) $duration_in_mts_old == 0) ? 1 : $duration_in_mts_old;

        $getDirectionFormMatrix = $this->get_DirectionFormMatrix($apptDet['appt_lat'], $apptDet['appt_long'], $args['ent_drop_lat'], $args['ent_drop_long']);

        $googleDis = $getDirectionFormMatrix['distance'];

        $distance_in_mts  = $args['ent_distance']; //0

        if ((($distance_in_mts < (0.8 * $googleDis)) || ($googleDis < (0.8 * $distance_in_mts))) && $googleDis > 0) {

            $dis_in_km = (float) ($googleDis / $this->distanceMetersByUnits);
        } else {
            $dis_in_km = (float) ($distance_in_mts / $this->distanceMetersByUnits);
        }



//        $avgSpeed = $dis_in_km / ($distance_in_mts / 60);

        $amount = ($dis_in_km * $apptDet['price_per_km']) + ($duration_in_mts * $apptDet['price_per_min']);

      $mongodata['amountvar'] = array('amount' =>$amount,'disinkm' => $dis_in_km,'durationinmts' => $duration_in_mts,'normaldistance' => $distance_in_mts);


        $newFare = ($amount + $apptDet['base_fare']) * $apptDet['surge'];
        

        // end of surg price



        $finalAmount = ($newFare > $apptDet['amount']) ? $newFare : $apptDet['amount'];

       $mongodata['newFare'] = array('newfare' => $newFare,'aptamount' => $apptDet['amount'],'finalamount' => $finalAmount);

        $speed_in_mts = $dis_in_km / ($duration_in_mts / 60);

        $apptDet['invoice_id'] = 'RY' . str_pad($apptDet['appointment_id'], 6, '0', STR_PAD_LEFT);

        $apptDet['speed_in_mts'] = $speed_in_mts;

        $apptDet['appt_duration'] = $duration_in_mts;

        $apptDet['appt_distance'] = $dis_in_km;

        


        $finalAmount = round($finalAmount);

        

        $tip = round($finalAmount * ((float) $apptDet['tip_percent'] / 100), 2);        
        
         $mongodata['afterrounding'] = array('finalamount' => $finalAmount,'tip' => $tip);

         $discount = 0;


            $couponsColl = $this->mongo->selectCollection('coupons');

            if ($apptDet['coupon_code'] != '') {

                $couponDet = $couponsColl->findOne(array('coupon_code' => $apptDet['coupon_code']));

                if (is_array($couponDet)) {

                    if ($couponDet['discount_type'] == '2')
                        $discount = $couponDet['discount'];
                    else
                        $discount = $finalAmount * ($couponDet['discount'] / 100);

                    $discountedFare = $finalAmount - round($discount, 2);
                    $finalAmount = ($discountedFare > 0 ? $discountedFare : 0);
                }
            }
        
        $updateDetailsQry = "update appointment set tip_amount = '" . $tip . "',discount='".round($discount, 2)."',expire_ts = '" . ($apptDet['expire_ts'] - $duration_in_mts_old) . "',app_distance = '" . (int) $dis_in_km . "',google_distance = '" . (int) $googleDis . "',server_distance = '" . (int) $distance . "',apprxAmt = '" . $finalAmount . "', duration = '" . $duration_in_mts . "', distance_in_mts = '" . $args['ent_distance'] . "', drop_addr1 = '" . $args['ent_drop_addr_line1'] . "', drop_addr2 = '" . $args['ent_drop_addr_line2'] . "', drop_lat = '" . $args['ent_drop_lat'] . "', drop_long = '" . $args['ent_drop_long'] . "',complete_dt = '" . $this->curr_date_time . "' where appointment_id = '" . $apptDet['appointment_id'] . "'";
        mysql_query($updateDetailsQry, $this->db->conn);

        if (mysql_affected_rows() <= 0)
            return $this->_getStatusMessage(3, $updateDetailsQry);
        
        
        $booking_data_livetrack->insert($mongodata);
        $errMsgArr = $this->_getStatusMessage(88, 1);
        return array('errNum' => $errMsgArr['errNum'], 'errFlag' => $errMsgArr['errFlag'], 'errMsg' => $errMsgArr['errMsg'],'apprAmount' => $finalAmount, 'dis' => round(($dis_in_km * $this->distanceMetersByUnits), 2),'tip' => $tip,'discount' => round($discount, 2)); //, 'calculatedAmount' => $amount
    }

    /*
     * Method name: updateApptStatus
     * Desc: Update appointment status
     * Input: Request data
     * Output:  success if got it else error according to the result
     */

    protected function updateApptStatus($args) {
        
        
//       $booking_data_livetrack = $this->mongo->selectCollection('booking_data_livetrack');
////$booking_data_livetrack->insert($args);
//$args = $booking_data_livetrack->findOne(array('_id' => new MongoId('576d355d5853f2f01e4663bd')));

        if ($args['ent_appnt_dt'] == '')
            return $this->_getStatusMessage(1, 'Booking date time');
        else if ($args['ent_response'] == '')
            return $this->_getStatusMessage(1, 'Response type');
        else if ($args['ent_pas_email'] == '')
            return $this->_getStatusMessage(1, 'Passenger email');
        else if ($args['ent_cityid'] == '' && $args['ent_reponse'] == '9')
            return $this->_getStatusMessage(1, 'city id');
        else if ($args['ent_date_time'] == '')
            return $this->_getStatusMessage(1, 'Date time');
        else if (($args['ent_reponse'] == '9' && $args['ent_meter'] == '' && $args['ent_dist'] == '' && $args['ent_cityid'] == ''))
            return $this->_getStatusMessage(1, 'Meter fare or distance ');

        $this->curr_date_time = urldecode($args['ent_date_time']);

        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], '1');

        if (is_array($returned))
            return $returned;

        $args['ent_appnt_dt'] = urldecode($args['ent_appnt_dt']);

        $pasData = $this->_getEntityDet($args['ent_pas_email'], '2');

        $getApptDetQry = "select a.discount,a.tip_amount,a.surge,a.coupon_code,a.tip_percent,a.apprxAmt,a.appt_type, a.status, a.distance_in_mts,a.duration, a.appt_lat, a.appt_long, a.payment_type, a.drop_lat, a.drop_long, a.address_line1, a.address_line2, a.drop_addr1, a.drop_addr2, a.created_dt, a.arrive_dt, a.start_dt, a.appointment_dt, a.amount, a.appointment_id, a.last_modified_dt, a.user_device, ";
//        $getApptDetQry .= "(select wt.price_per_km from workplace_types wt, workplace w, master d where w.type_id = wt.type_id and w.workplace_id = d.workplace_id and d.mas_id = a.mas_id) as price_per_km, ";
//        $getApptDetQry .= "(select wt.price_per_min from workplace_types wt, workplace w, master d where w.type_id = wt.type_id and w.workplace_id = d.workplace_id and d.mas_id = a.mas_id) as price_per_min, ";
        $getApptDetQry .= "(select avg(star_rating) from master_ratings where mas_id = a.mas_id) as avg_rating from appointment a where a.mas_id = '" . $this->User['entityId'] . "' and a.appointment_dt = '" . $args['ent_appnt_dt'] . "' and a.slave_id = '" . $pasData['slave_id'] . "' order by a.appointment_id DESC";

        $apptDet = mysql_fetch_assoc(mysql_query($getApptDetQry, $this->db->conn));

        if ($apptDet['status'] == '4')
            return $this->_getStatusMessage(41, 3);

        if ($apptDet['status'] == '5')
            return $this->_getStatusMessage(82, 82);

        if ($apptDet['status'] == '10')
            return $this->_getStatusMessage(72, 72);

        $updateStr = '';

        if ($args['ent_response'] == '6') {
            $message = 'Driver on way';
            $noteType = '6';
            $errNum = 57;

//            $getWorkplaceDataQry = "select wt.type_name, w.Title, w.Vehicle_Reg_No from workplace w, workplace_types wt where wt.workplace_id = w.workplace_id and w.workplace_id = '".$apptDet['workplace_id']."'";
//            $workPlaceData = mysql_fetch_assoc(mysql_query($getWorkplaceDataQry, $this->db->conn));

            list($date, $time) = explode(' ', $apptDet['appointment_dt']);
            list($year, $month, $day) = explode('-', $date);
            list($hour, $minute, $second) = explode(':', $time);

            $dateNumber = $year . $month . $day . $hour . $minute . $second;

            $location = $this->mongo->selectCollection('location');
            $masterData = $location->findOne(array('user' => (int) $this->User['entityId']));

            $aplPushContent = array('alert' => $message, 't' => $apptDet['appt_type'], 'nt' => $noteType, 'd' => $apptDet['appointment_dt'], 'e' => $this->User['email'], 'sound' => 'default', 'ltg' => number_format($masterData['location']['latitude'], '8', '.', '') . ',' . number_format($masterData['location']['longitude'], '6', '.', '')); // 'dis' => ($distance == NULL) ? 0 : $distance, 'eta' => ($duration == NULL) ? 0 : $duration);//'alert' => $message, 'd' => $apptDet['appointment_dt'],//, 'r' => number_format($apptDet['avg_rating'], '2', '.', '')//, 'id' => $apptDet['appointment_id']
            $andrPushContent = array("payload" => $message, 't' => $apptDet['appt_type'], 'action' => $noteType, 'sname' => $this->User['firstName'] . ' ' . $this->User['last_name'], 'dt' => (string) $dateNumber, 'pic' => $this->User['pPic'], 'ph' => $this->User['mobile'], 'e' => $this->User['email'], 'ltg' => $masterData['location']['latitude'] . ',' . $masterData['location']['longitude'], 'd' => $apptDet['appointment_dt'], 'r' => $apptDet['avg_rating'], 'id' => $apptDet['appointment_id']); //    'dis' => $distance, 'eta' => $duration);
        } else if ($args['ent_response'] == '7') {
            $message = 'Driver arrived';
            $noteType = '7';
            $errNum = 58;
            $updateStr = ", arrive_dt = '" . $this->curr_date_time . "'";

            $aplPushContent = array('alert' => $message, 't' => $apptDet['appt_type'], 'nt' => $noteType, 'e' => $this->User['email'], 'd' => $apptDet['appointment_dt'], 'sound' => 'default'); //, 'n' => $this->User['firstName'] . ' ' . $this->User['last_name']//, 'pic' => $this->User['pPic'], 'ph' => $this->User['mobile']//, 'id' => $apptDet['appointment_id']
            $andrPushContent = array("payload" => $message, 't' => $apptDet['appt_type'], 'action' => $noteType, 'sname' => $this->User['firstName'] . ' ' . $this->User['last_name'], 'dt' => $args['ent_appnt_dt'], 'pic' => $this->User['pPic'], 'ph' => $this->User['mobile'], 'smail' => $this->User['email'], 'id' => $apptDet['appointment_id']);
        } else if ($args['ent_response'] == '8') {
            $message = 'Journey started';
            $noteType = '8';
            $errNum = 83;
            $duration_in_mts = round(abs(strtotime($this->curr_date_time) - strtotime($apptDet['arrive_dt'])) / 60);
            $updateStr = ",waiting_mts = '" . $duration_in_mts . "', start_dt = '" . $this->curr_date_time . "',expire_ts = '" . gmmktime() . "'";
            $aplPushContent = array('alert' => $message, 't' => $apptDet['appt_type'], 'nt' => $noteType, 'e' => $this->User['email'], 'd' => $apptDet['appointment_dt'], 'sound' => 'default'); //, 'n' => $this->User['firstName'] . ' ' . $this->User['last_name']//, 'pic' => $this->User['pPic'], 'ph' => $this->User['mobile']//, 'id' => $apptDet['appointment_id']
            $andrPushContent = array("payload" => $message, 't' => $apptDet['appt_type'], 'action' => $noteType, 'sname' => $this->User['firstName'] . ' ' . $this->User['last_name'], 'dt' => $args['ent_appnt_dt'], 'pic' => $this->User['pPic'], 'ph' => $this->User['mobile'], 'smail' => $this->User['email'], 'id' => $apptDet['appointment_id']);
        } else if ($args['ent_response'] == '9') {
            $message = 'Trip completed';
            $noteType = '9';
            $errNum = 59;


            $finalAmt = round((float)($args['ent_meter'] + $args['ent_toll'] +  $args['ent_airport'] + $args['ent_parking']), 2);
            
            $tip = $apptDet['tip_amount'];
            $discount = $apptDet['discount'];


            $cc_charge = 0;

            $finalBeforeRounding = $finalAmt + $cc_charge;

            $finalAmount = $finalBeforeRounding;

            $gatewayCommision = 0;

            if ($apptDet['payment_type'] == '1')
                $gatewayCommision = (float) ($finalAmount * (0.025)) + 1;


            $rediusAmount = $finalAmt;

            if ($rediusAmount < 0)
                $rediusAmount = 1;

            $RediousPrice = $this->mongo->selectCollection('RediousPrice');

            $appcommisioninpersentage = $RediousPrice->find(array('cityid' => (string)$args['ent_cityid'], 'from_' => array('$lte' => (int) $rediusAmount)))->sort(array('from_' => -1))->limit(1);
//        return array('data' => 'test');

            foreach ($appcommisioninpersentage as $key) {
                $Commisiondata[] = $key;
            }



            $defautcommision = PAYMENT_APP_COMMISSION;

            if (!empty($Commisiondata)) {
                $defautcommision = $Commisiondata[0]['price'];
            }




            $appCommision = (float) (((float) $finalAmt) * ($defautcommision / 100)); //$apptDet['amount'] - $transferAmt;


            $mas = (((float) $finalAmt - $appCommision) + $tip); //$transferAmt - $appCommision - $gatewayCommision;

            $profitOrLoss = $appCommision - $discount - $gatewayCommision;

            $transactionId = "";

            if ($apptDet['payment_type'] == '1') {
                
                 $chargeCustomerArr = array('stripe_id' => $pasData['stripe_id'], 'amount' => (int) ((float)$finalAmount * 100), 'currency' => PAYMENT_BASE_CURRENCY, 'description' => 'From ' . $pasData['email']);

                $customer = $this->stripe->apiStripe('chargeCard', $chargeCustomerArr);

                if ($customer['error']) {

                    $query = "update appointment set payment_type = 2 where appointment_id = '" . $apptDet['appointment_id'] . "'";
                    $resutlUp = mysql_query($query, $this->db->conn);

                    if (mysql_affected_rows() <= 0) {
                        return $this->_getStatusMessage(3, 3);
                    }

                    $this->ios_cert_path = IOS_PASSENGER_PEM_PATH;
                    $this->ios_cert_pwd = IOS_PASSENGER_PEM_PASS;
                    $this->androidApiKey = ANDROID_PASSENGER_PUSH_KEY;
                    $noteType = 101;
                    $message = 'CARD PAYMENT HAS DECLINED! PLEASE PAY VIA CASH!';

                    $aplPushContent = array('alert' => $message, 't' => $apptDet['appt_type'], 'nt' => $noteType, 'd' => $apptDet['appointment_dt'], 'e' => $this->User['email'], 'sound' => 'default', 'id' => $apptDet['appointment_id']); //, 'n' => $this->User['firstName'] . ' ' . $this->User['last_name']
                    $andrPushContent = array("payload" => $message, 't' => $apptDet['appt_type'], 'action' => $noteType, 'sname' => $this->User['firstName'] . ' ' . $this->User['last_name'], 'dt' => $args['ent_appnt_dt'], 'smail' => $this->User['email'], 'id' => $apptDet['appointment_id']);

                    $push['push'] = $this->_sendPush($this->User['entityId'], array($pasData['slave_id']), $message, $noteType, $this->User['email'], $this->curr_date_time, '2', $aplPushContent, $andrPushContent, $apptDet['user_device']);


                    return array('errNum' => 16, 'errFlag' => 1, 'errMsg' => 'CARD PAYMENT HAS DECLINED! PLEASE TAKE PAYMENT VIA CASH!', 'code' => $customer['result']['code']);
                }



                $transactionId = $customer['id'];
            }

            $updateStr .= ",app_owner_pl = '" . ($profitOrLoss > 0 ? $profitOrLoss : 0 ) . "',txn_id = '" . $transactionId . "', mas_earning = '" . round($mas, 2) . "', pg_commission = '" . round($gatewayCommision, 2) . "',app_commission = '" . round($appCommision, 2) . "',cc_fee = '" . $cc_charge . "',payment_status = 1,amount = '" . $finalAmount . "',meter_fee = '" . $args['ent_meter'] . "',toll_fee = '" . $args['ent_toll'] . "',airport_fee = '" . $args['ent_airport'] . "',parking_fee = '" . $args['ent_parking'] . "',remarks = '" . $args['ent_doc_remarks'] . "'";

            $aplPushContent = array('alert' => $message, 't' => $apptDet['appt_type'], 'nt' => $noteType, 'd' => $apptDet['appointment_dt'], 'e' => $this->User['email'], 'sound' => 'default', 'id' => $apptDet['appointment_id']); //, 'n' => $this->User['firstName'] . ' ' . $this->User['last_name']
            $andrPushContent = array("payload" => $message, 't' => $apptDet['appt_type'], 'action' => $noteType, 'sname' => $this->User['firstName'] . ' ' . $this->User['last_name'], 'dt' => $args['ent_appnt_dt'], 'smail' => $this->User['email'], 'id' => $apptDet['appointment_id']);
        } else {
            return $this->_getStatusMessage(56, 56);
        }

        $updateAppointmentStatusQry = "update appointment set status = '" . $args['ent_response'] . "', last_modified_dt = '" . $this->curr_date_time . "'" . $updateStr . " where mas_id = '" . $this->User['entityId'] . "' and slave_id = '" . $pasData['slave_id'] . "' and appointment_dt = '" . $args['ent_appnt_dt'] . "'";
        $updateAppointmentStatusRes = mysql_query($updateAppointmentStatusQry, $this->db->conn);

        if (mysql_affected_rows() > 0) {

            $this->ios_cert_path = IOS_PASSENGER_PEM_PATH;
            $this->ios_cert_pwd = IOS_PASSENGER_PEM_PASS;
            $this->androidApiKey = ANDROID_PASSENGER_PUSH_KEY;

            $location = $this->mongo->selectCollection('location');

            if ($args['ent_response'] == '9') {

                $updateReviewQry = "insert into passenger_rating(mas_id, slave_id, rating, status, rating_dt, appointment_id) values ('" . $this->User['entityId'] . "', '" . $pasData['slave_id'] . "', '" . $args['ent_rating'] . "', '1', '" . $this->curr_date_time . "', '" . $apptDet['appointment_id'] . "')";
                mysql_query($updateReviewQry, $this->db->conn);

                $mail = new sendAMail(APP_SERVER_HOST);

                $apptDet['invoice_id'] = 'AR' . str_pad($apptDet['appointment_id'], 6, '0', STR_PAD_LEFT);

                $apptDet['speed_in_mts'] = $apptDet['distance_in_mts'];

                $apptDet['appt_duration'] = $apptDet['duration'];
                $apptDet['amount'] = $finalAmount;
                $apptDet['meter'] = $args['ent_meter'];
                $apptDet['toll'] = $args['ent_toll'];
                $apptDet['airport'] = $args['ent_airport'];
                $apptDet['parking'] = $args['ent_parking'];
                $apptDet['cc_fee'] = $cc_charge;
                $apptDet['tip'] = $tip;

                $apptDet['discount'] = $discount;
                $apptDet['appt_distance'] = round(($apptDet['distance_in_mts'] / $this->distanceMetersByUnits), 4);

                $push['invoMail'] = $mail->sendInvoice($this->User, $pasData, $apptDet);
                
                $cond = array('status' => 3, 'apptStatus' => 0);
            } else {
                $cond = array('apptStatus' => (int) $args['ent_response'], 'status' => 5);
            }
            $location->update(array('user' => (int) $this->User['entityId']), array('$set' => $cond));
//            else if ($args['ent_response'] == '7' || $args['ent_response'] == '8') {
            $push['push'] = $this->_sendPush($this->User['entityId'], array($pasData['slave_id']), $message, $noteType, $this->User['email'], $this->curr_date_time, '2', $aplPushContent, $andrPushContent, $apptDet['user_device']);
//            }

            $pubnubContent = array('a' => 14, 'bid' => $apptDet['appointment_id'], 's' => $args['ent_response'], 'm' => $message);

            $out = array('push1' => $push, 'push' => $aplPushContent);
            $out['pubnub'] = $this->pubnub->publish(array(
                'channel' => 'dispatcher',
                'message' => $pubnubContent
            ));

            return $this->_getStatusMessage($errNum, 2);
        } else if ($updateAppointmentStatusRes) {
            return $this->_getStatusMessage($errNum, $errNum);
        } else {
            return $this->_getStatusMessage(3, $updateAppointmentStatusQry);
        }
    }

    /*
     * Method name: getMySlots
     * Desc: get master slots
     * Input: Request data
     * Output:  success if got it else error according to the result
     */

    protected function getMySlots($args) {

        if ($args['ent_date_time'] == '')
            return $this->_getStatusMessage(1, 'Date time');

        $this->curr_date_time = urldecode($args['ent_date_time']);

        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], '1');

        if (is_array($returned))
            return $returned;

        $getScheduleQry = "select sh.id, sh.day_number, sh.start, sh.mas_id, sh.duration_in_mts, sh.ref_count from master_schedule sh, master doc ";
        $getScheduleQry .= "where sh.mas_id = doc.mas_id and doc.mas_id = '" . $this->User['entityId'] . "' order by sh.start asc";

        $getScheduleRes = mysql_query($getScheduleQry, $this->db->conn);

        $appts = $daysAvlb = $avlbSlots = $avlbDates = array();

        while ($appointment = mysql_fetch_assoc($getScheduleRes)) {

            $appts[$appointment['day_number']]['day'] = $appointment['day_number'];
            $appts[$appointment['day_number']]['time'][] = array('from' => date('h:i a', strtotime($appointment['start'])), 'to' => date("h:i a", strtotime('+' . (int) $appointment['duration_in_mts'] . ' minutes', strtotime($appointment['start']))), 'flag' => $appointment['ref_count']);
        }

        $errMsgArr = $this->_getStatusMessage(21, 2);
        return array('errNum' => $errMsgArr['errNum'], 'errFlag' => $errMsgArr['errFlag'], 'errMsg' => $errMsgArr['errMsg'], 'slots' => $appts);
    }

    /*
     * Method name: getMySlots
     * Desc: get master slots
     * Input: Request data
     * Output:  success if got it else error according to the result
     */

    protected function updateTip($args) {

        if ($args['ent_booking_id'] == '')
            return $this->_getStatusMessage(1, 'Booking id');
        else if ($args['ent_tip'] == '')
            return $this->_getStatusMessage(1, 'Tip amount');
        else if ($args['ent_date_time'] == '')
            return $this->_getStatusMessage(1, 'Date time');

        $this->curr_date_time = urldecode($args['ent_date_time']);

        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], '2');

        if (is_array($returned))
            return $returned;

//        $getDetailsQry = "select amount,meter_fee,toll_fee,parking_fee,airport_fee,coupon_code,discount from appointment where appointment_id = '" . $args['ent_booking_id'] . "'";
//
//        $getDetailsRes = mysql_query($getDetailsQry, $this->db->conn);
//
//        $apptData = mysql_fetch_assoc($getDetailsRes);
//        $total = $apptData['airport_fee'] + $apptData['parking_fee'] + $apptData['toll_fee'] + $apptData['meter_fee'];
//        $tip = $total * ((float) $args['ent_tip'] / 100);
//        $amount = $total - $apptData['discount'];
//        $fare = $amount + $tip;
//        $checkingDate = explode('-', $args['ent_slots_for']);

        $getScheduleQry = "update appointment set tip_percent = '" . $args['ent_tip'] . "' where appointment_id = '" . $args['ent_booking_id'] . "' and slave_id = '" . $this->User['entityId'] . "'";
        mysql_query($getScheduleQry, $this->db->conn); //$getScheduleRes = 
//        if (mysql_affected_rows() >= 0)
//            $errMsgArr = $this->_getStatusMessage(97, 2);
//        else

        return $this->_getStatusMessage(98, 2);
//        $errMsgArr = $this->_getStatusMessage(98, 2);
//        return array('errNum' => $errMsgArr['errNum'], 'errFlag' => $errMsgArr['errFlag'], 'errMsg' => $errMsgArr['errMsg'], 'tip' => $tip);
    }

    /*
     * Method name: getMasterProfile
     * Desc: get master profile
     * Input: Request data
     * Output:  success if got it else error according to the result
     */

    protected function getMasterProfile($args) {

        if ($args['ent_date_time'] == '')
            return $this->_getStatusMessage(1, 'Date time');

        $this->curr_date_time = urldecode($args['ent_date_time']);

        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], '1');

        if (is_array($returned))
            return $returned;

        $explodeDateTime = explode(' ', $this->curr_date_time);
        $explodeDate = explode('-', $explodeDateTime[0]);

        $weekData = $this->week_start_end_by_date($this->curr_date_time);

        $selectMasterProfileQry = "select doc.first_name, doc.workplace_id, doc.last_name, doc.email, doc.license_num,doc.license_exp, doc.board_certification_expiry_dt, doc.mobile, doc.status, doc.profile_pic, avg(rat.star_rating) as avgRate, count(rat.review_dt) as totRats, (select count(appointment_id) from appointment where mas_id = doc.mas_id and status = 9) as cmpltApts, (select sum(mas_earning) from appointment where mas_id = doc.mas_id and DATE(appointment_dt) = '" . $explodeDateTime[0] . "' and status = 9) as today_earnings, (select amount from appointment where mas_id = doc.mas_id and status = 9 order by appointment_id DESC limit 0, 1) as last_billed_amount, (select sum(mas_earning) from appointment where mas_id = doc.mas_id and status = 9 and DATE(appointment_dt) BETWEEN '" . $weekData['first_day_of_week'] . "' and '" . $weekData['last_day_of_week'] . "') as week_earnings, (select sum(mas_earning) from appointment where mas_id = doc.mas_id and status = 9 and DATE_FORMAT(appointment_dt, '%Y-%m') = '" . $explodeDate[0] . '-' . $explodeDate[1] . "') as month_earnings, (select companyname from company_info where company_id = doc.company_id ) as compname,(select sum(mas_earning) from appointment where mas_id = doc.mas_id and status = 9) as total_earnings from master doc, master_ratings rat where doc.mas_id = rat.mas_id and doc.mas_id = '" . $this->User['entityId'] . "'";
        $selectMasterProfileRes = mysql_query($selectMasterProfileQry, $this->db->conn);

        if (mysql_num_fields($selectMasterProfileRes) <= 0)
            return $this->_getStatusMessage(3, $selectMasterProfileQry);

        $docData = mysql_fetch_assoc($selectMasterProfileRes);

        $getVehicleDataQry = "select wrk.workplace_id, wrk.Vehicle_Insurance_No, wrk.Vehicle_Insurance_Dt, wrk.License_Plate_No, (select wt.max_size from workplace_types wt, workplace w where w.type_id = wt.type_id and w.workplace_id = wrk.workplace_id) as capacity, (select v.vehiclemodel from vehiclemodel v, workplace w where w.Vehicle_Model = v.id and w.workplace_id = wrk.workplace_id) as vehicle_model, (select wt.type_name from workplace_types wt, workplace w where w.type_id = wt.type_id and w.workplace_id = wrk.workplace_id) as vehicle_type, wrk.Vehicle_Color from workplace wrk where wrk.workplace_id = '" . $docData['workplace_id'] . "'";

        $getVehicleDataRes = mysql_query($getVehicleDataQry, $this->db->conn);

        $vehicleData = mysql_fetch_assoc($getVehicleDataRes);

        $errMsgArr = $this->_getStatusMessage(21, 2);
        return array('errNum' => $errMsgArr['errNum'], 'errFlag' => $errMsgArr['errFlag'], 'errMsg' => $errMsgArr['errMsg'], 'data' => array('fName' => $docData['first_name'],
                'lName' => $docData['last_name'], 'email' => $docData['email'], 'type' => $docData['type_name'], 'mobile' => $docData['mobile'], 'status' => $docData['status'],
                'pPic' => $docData['profile_pic'], 'expertise' => $docData['expertise'], 'vehicleType' => $vehicleData['vehicle_type'], 'companyname' => $docData['compname'],
                'licNo' => $docData['license_num'], 'licExp' => ($docData['license_exp'] == '') ? '' : date('F d, Y', strtotime($docData['license_exp'])),
                'vehMake' => $vehicleData['vehicle_model'] . ' ' . $vehicleData['Vehicle_Color'], 'licPlateNum' => $vehicleData['License_Plate_No'], 'seatCapacity' => $vehicleData['capacity'], 'vehicleInsuranceNum' => $vehicleData['Vehicle_Insurance_No'], 'vehicleInsuranceExp' => $vehicleData['Vehicle_Insurance_Dt'],
                'avgRate' => round($docData['avgRate'], 1), 'totRats' => $docData['totRats'], 'cmpltApts' => $docData['cmpltApts'], 'todayAmt' => round($docData['today_earnings'], 2), 'lastBilledAmt' => round($docData['last_billed_amount'], 2), 'weekAmt' => round($docData['week_earnings'], 2), 'monthAmt' => round($docData['month_earnings'], 2), 'totalAmt' => round($docData['total_earnings'], 2)
        ));
    }

    /*
     * Method name: cancelAppointment
     * Desc: Passenger can Cancel an appointment requested
     * Input: Request data
     * Output:  success if got it else error according to the result
     */

    protected function cancelAppointment($args) {

        if ($args['ent_appnt_dt'] == '')
            return $this->_getStatusMessage(1, 'Booking date time');
        else if ($args['ent_dri_email'] == '')
            return $this->_getStatusMessage(1, 'Driver email');
        else if ($args['ent_date_time'] == '')
            return $this->_getStatusMessage(1, 'Date time');

        $this->curr_date_time = urldecode($args['ent_date_time']);

        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], '2');

        if (is_array($returned))
            return $returned;

        $args['ent_appnt_dt'] = urldecode($args['ent_appnt_dt']);

        $getApptDetQry = "select a.appointment_dt, a.appt_type, a.status, a.mas_id, a.appointment_id, a.user_device from appointment a, master d where a.mas_id = d.mas_id and d.email = '" . $args['ent_dri_email'] . "' and a.appointment_dt = '" . $args['ent_appnt_dt'] . "'";
        $getApptDetRes = mysql_query($getApptDetQry, $this->db->conn);

        if (mysql_affected_rows() <= 0)
            return $this->_getStatusMessage(32, 32);

        $apptDet = mysql_fetch_assoc($getApptDetRes);

        if (!is_array($apptDet))
            return $this->_getStatusMessage(32, 32);

        if ($apptDet['status'] == '3')
            return $this->_getStatusMessage(44, 44);

        if ($apptDet['status'] == '4')
            return $this->_getStatusMessage(41, 3);

        if ($apptDet['status'] == '5')
            return $this->_getStatusMessage(82, 3);

        if ($apptDet['status'] == '9')
            return $this->_getStatusMessage(75, 3);

//        $docData = $this->_getEntityDet($args['ent_dri_email'], '1');

        $after_5min = date('Y-m-d H:i:s', (strtotime($apptDet['appointment_dt']) + $this->cancellationTimeInSec));

        if ($this->curr_date_time >= $after_5min) {
            $cancelStatus = "cancel_status = '3', cancel_amt = '5', ";

            $finalAmount = 5;

            $chargeCustomerArr = array('stripe_id' => $this->User['stripe_id'], 'amount' => (int) ((float) $finalAmount * 100), 'currency' => 'USD', 'description' => 'From ' . $this->User['email']);

            $customer = $this->stripe->apiStripe('chargeCard', $chargeCustomerArr);

//            if ($customer['error'])
//                return array('errNum' => 16, 'errFlag' => 1, 'errMsg' => $customer['error']['message'], 'test' => 1);
        } else {
            $cancelStatus = "cancel_status = '2', ";
        }

        $cancelApntQry = "update appointment set status = 4, " . $cancelStatus . " last_modified_dt = '" . $this->curr_date_time . "', cancel_dt = '" . $this->curr_date_time . "' where appointment_id = '" . $apptDet['appointment_id'] . "'"; // slave_id = '" . $this->User['entityId'] . "' and mas_id = '" . $apptDet['mas_id'] . "' and appointment_dt = '" . $args['ent_appnt_dt'] . "'";
        mysql_query($cancelApntQry, $this->db->conn);

        if (mysql_affected_rows() <= 0)
            return $this->_getStatusMessage(3, $cancelApntQry);

        $location = $this->mongo->selectCollection('location');

        $master = $location->findOne(array('user' => (int) $apptDet['mas_id']));

        $pubnubContent = array('a' => 10, 'dt' => $apptDet['appointment_dt'], 'e' => $this->User['email'], 'bid' => $apptDet['appointment_id'], 't' => $apptDet['appt_type'],);

        if (!is_null($master['listner']))
            $pushNum['pubnub'] = $this->pubnub->publish(array(
                'channel' => $master['listner'],
                'message' => $pubnubContent
            ));

        $message = "Passenger cancelled the Booking on " . APP_NAME . "!";

        $this->ios_cert_path = IOS_DRIVER_PEM_PATH;
        $this->ios_cert_pwd = IOS_DRIVER_PEM_PASS;
        $this->androidApiKey = ANDROID_DRIVER_PUSH_KEY;
        $aplPushContent = array('alert' => $message, 'nt' => '10', 'd' => $apptDet['appointment_dt'], 'e' => $this->User['email'], 'sound' => 'default', 'id' => $apptDet['appointment_id'], 'r' => $args['ent_cancel_type'], 't' => $apptDet['appt_type']);
        $andrPushContent = array('payload' => $message, 'action' => '10', 'sname' => $this->User['firstName'], 'dt' => $apptDet['appointment_dt'], 'e' => $this->User['email'], 'bid' => $apptDet['appointment_id'], 'r' => $args['ent_cancel_type'], 't' => $apptDet['appt_type']);
        $pushNum['push'] = $this->_sendPush($this->User['entityId'], array($apptDet['mas_id']), $message, '10', $this->User['firstName'], $this->curr_date_time, '1', $aplPushContent, $andrPushContent);

        $deleteAllSessionsQry = "update master set status = '3' where mas_id = '" . $apptDet['mas_id'] . "'";
        mysql_query($deleteAllSessionsQry, $this->db->conn);

        $location->update(array('user' => (int) $apptDet['mas_id']), array('$set' => array('status' => 3, 'apptStatus' => 0)));

         $pubnubContent = array('a' => 14, 'bid' => $apptDet['appointment_id'], 's' => $args['ent_response'], 'm' => 'passenger has cancelled');

            $out = array('push1' => $push, 'push' => $aplPushContent);
            $out['pubnub'] = $this->pubnub->publish(array(
                'channel' => 'dispatcher',
                'message' => $pubnubContent
            ));
        
        
        
        
        if ($this->curr_date_time >= $after_5min)
            return $this->_getStatusMessage(43, $cancelApntQry . $after_5min);
        else
            return $this->_getStatusMessage(42, $cancelApntQry . $after_5min);
    }

    /*
     * Method name: abortJourney
     * Desc: Driver can Cancel an appointment in any time
     * Input: Request data
     * Output:  success if got it else error according to the result
     */

    protected function abortJourney($args) {

        if ($args['ent_appnt_dt'] == '')
            return $this->_getStatusMessage(1, 'Booking date time');
        else if ($args['ent_pas_email'] == '')
            return $this->_getStatusMessage(1, 'Passenger email');
        else if ($args['ent_date_time'] == '')
            return $this->_getStatusMessage(1, 'Date time');
        else if ($args['ent_cancel_type'] == '')
            return $this->_getStatusMessage(1, 'Cancel type');

        $this->curr_date_time = urldecode($args['ent_date_time']);

        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], '1');

        if (is_array($returned))
            return $returned;

        $args['ent_appnt_dt'] = urldecode($args['ent_appnt_dt']);

        $getApptDetQry = "select a.appointment_dt,a.appointment_id,a.appt_type,a.status,a.slave_id,a.user_device from appointment a where a.mas_id = '" . $this->User['entityId'] . "' and a.appointment_dt = '" . $args['ent_appnt_dt'] . "'";
        $getApptDetRes = mysql_query($getApptDetQry, $this->db->conn);

        if (mysql_affected_rows() <= 0)
            return $this->_getStatusMessage(32, 32);

        $apptDet = mysql_fetch_assoc($getApptDetRes);

        if (!is_array($apptDet))
            return $this->_getStatusMessage(32, 32);

        if ($apptDet['status'] == '3')
            return $this->_getStatusMessage(44, 44);

        if ($apptDet['status'] == '4')
            return $this->_getStatusMessage(41, 3);

        if ($apptDet['status'] == '9')
            return $this->_getStatusMessage(75, 75);

//        $pasData = $this->_getEntityDet($args['ent_pas_email'], '2');

        $cancelApntQry = "update appointment set status = 5,cancel_status = '" . $args['ent_cancel_type'] . "',last_modified_dt = '" . $this->curr_date_time . "',cancel_dt = '" . $this->curr_date_time . "' where slave_id = '" . $apptDet['slave_id'] . "' and mas_id = '" . $this->User['entityId'] . "' and appointment_dt = '" . $args['ent_appnt_dt'] . "'";
        mysql_query($cancelApntQry, $this->db->conn);

        if (mysql_affected_rows() <= 0)
            return $this->_getStatusMessage(3, $cancelApntQry);

        $message = "Driver cancelled the Booking on " . APP_NAME . "!";

        $this->ios_cert_path = IOS_PASSENGER_PEM_PATH;
        $this->ios_cert_pwd = IOS_PASSENGER_PEM_PASS;
        $this->androidApiKey = ANDROID_PASSENGER_PUSH_KEY;

        $aplPushContent = array('alert' => $message, 'nt' => '10', 't' => $apptDet['appt_type'], 'sound' => 'default', 'id' => $apptDet['appointment_id'], 'r' => (int) $args['ent_cancel_type']); //, 'd' => $apptDet['appointment_dt'], 'e' => $this->User['email']
        $andrPushContent = array('payload' => $message, 'action' => '10', 't' => $apptDet['appt_type'], 'sname' => $this->User['firstName'], 'dt' => $apptDet['appointment_dt'], 'e' => $this->User['email'], 'bid' => $apptDet['appointment_id']);
        $pushNum['push'] = $this->_sendPush($this->User['entityId'], array($apptDet['slave_id']), $message, '10', $this->User['firstName'], $this->curr_date_time, '2', $aplPushContent, $andrPushContent, $apptDet['user_device']);

        
         $pubnubContent = array('a' => 14, 'bid' => $apptDet['appointment_id'], 'm' => 'driver has cancelled');

            $out = array('push1' => $push, 'push' => $aplPushContent);
            $out['pubnub'] = $this->pubnub->publish(array(
                'channel' => 'dispatcher',
                'message' => $pubnubContent
            ));
        
        
        $location = $this->mongo->selectCollection('location');

  
        
        $location->update(array('user' => (int) $this->User['entityId']), array('$set' => array('status' => 3)));

        return $this->_getStatusMessage(42, $cancelApntQry . $pushNum);
    }

    /*
     * Method name: cancelAppointmentRequest
     * Desc: Passenger can Cancel an appointment requested
     * Input: Request data
     * Output:  success if cancelled else error according to the result
     */

    protected function cancelAppointmentRequest($args) {

        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], '2');

        if (is_array($returned))
            return $returned;

        $checkBookingsQry = "select appointment_id from appointment where slave_id = '" . $this->User['entityId'] . "' and status IN (6,7,8)";
        $checkBookingsRes = mysql_query($checkBookingsQry, $this->db->conn);

        if (mysql_num_rows($checkBookingsRes) > 0)
            return $this->_getStatusMessage(93, 93);

        if ($this->_updateSlvApptStatus($this->User['entityId'], '3') == 0)
            return $this->_getStatusMessage(74, 74);
        else
            return $this->_getStatusMessage(3, 1);
    }

    /*
     * Method name: payForBooking
     * Desc: Passenger can pay for the journey
     * Input: Request data
     * Output:  success array if got it else error according to the result
     */

    protected function payForBookingOld($args) {

        if ($args['ent_appnt_dt'] == '')
            return $this->_getStatusMessage(1, 'Booking date time');
        else if ($args['ent_date_time'] == '')
            return $this->_getStatusMessage(1, 'Date time');

        $this->curr_date_time = urldecode($args['ent_date_time']);

        $args['ent_appnt_dt'] = urldecode($args['ent_appnt_dt']);

        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], '2');

        if (is_array($returned))
            return $returned;

        $getApptDetQry = "select a.amount,a.tip_amount,a.coupon_code,a.discount,a.meter_fee as meter,a.toll_fee as toll,a.parking_fee as parking,a.airport_fee as airport,a.status,a.distance_in_mts,a.payment_type,a.appt_lat,a.appt_long,a.drop_lat,a.drop_long,a.address_line1,a.address_line2,a.created_dt,a.arrive_dt,a.appointment_dt,a.amount,a.appointment_id,a.last_modified_dt,(select email from master where mas_id = a.mas_id) as master_email from appointment a where a.appointment_dt = '" . $args['ent_appnt_dt'] . "' and a.slave_id = '" . $this->User['entityId'] . "' and a.status = 9";
        $apptDet = mysql_fetch_assoc(mysql_query($getApptDetQry, $this->db->conn));

        if ($apptDet['status'] == '4')
            return $this->_getStatusMessage(41, 3);

        if ($apptDet['status'] == '5')
            return $this->_getStatusMessage(82, 3);

        $masData = $this->_getEntityDet($apptDet['master_email'], '1');

        $mail = new sendAMail(APP_SERVER_HOST);
        $transferString = '';

//        $message = "Payment completed for booking dated " . date('d-m-Y h:i a', strtotime($apptDet['appointment_dt'])) . " on " . APP_NAME . "!";

        if ($apptDet['payment_type'] == '1') {

//            if ($args['ent_transaction_id'] == '')
//                return $this->_getStatusMessage(1, 2);
//
//            if ($this->User['paypal'] == '') {
//                return $this->_getStatusMessage(104, 2);
//            }
//
//            $paypal = new paypal();
//
//            $accessToken = $paypal->refresh_token($this->User['paypal']);
//
//            $pay = $paypal->process_payment(array('access_token' => $accessToken['access_token'], 'amount' => $apptDet['amount'], 'metadata_id' => $args['ent_transaction_id']));
//
//            $transferString = ",txn_id = '" . $pay['id'] . "'";

            $message = "You have received payment via card for booking id " . $apptDet['appointment_id'] . " on " . date('d-m-Y h:i a', strtotime($apptDet['appointment_dt'])) . ".";

            $chargeCustomerArr = array('stripe_id' => $this->User['stripe_id'], 'amount' => (int) ((float) $apptDet['amount'] * 100), 'currency' => 'USD', 'description' => 'From ' . $this->User['email']);

            $customer = $this->stripe->apiStripe('chargeCard', $chargeCustomerArr);

            $transferAmt = (int) (((float) $apptDet['amount'] * 100) * (90 / 100));

            if ($customer['error'])
                $charge = array('errNum' => 16, 'errFlag' => 1, 'errMsg' => $customer['error']['message'], 'test' => 1);
//            else
//                $transfer = $this->stripe->apiStripe('createTransfer', array('amount' => $transferAmt, 'currency' => 'usd', 'recipient' => $masData['stripe_id'], 'description' => 'From ' . $this->User['email'], 'statement_description' => 'For appointment dated: ' . $args['ent_appnt_dt']));

            $transferString = ",txn_id = '" . $transfer['id'] . "'";

            $payStatus = 1;
            $message = "You have received payment via card for booking id " . $apptDet['appointment_id'] . " on " . date('d-m-Y h:i a', strtotime($apptDet['appointment_dt'])) . ".";
        } else if ($apptDet['payment_type'] == '2') {
            $payStatus = 1;
            $message = "You have received payment via cash for booking id " . $apptDet['appointment_id'] . " on " . date('d-m-Y h:i a', strtotime($apptDet['appointment_dt'])) . ".";
        }

        $duration_in_mts = round(abs(strtotime($apptDet['complete_dt']) - strtotime($apptDet['start_dt'])) / 60, 2);

        $dis_in_km = (float) ($apptDet['distance_in_mts'] / $this->distanceMetersByUnits);

        $speed_in_mts = $dis_in_km / ($duration_in_mts / 60);

        $apptDet['tip'] = (float) $apptDet['tip_amount'];

        $apptDet['invoice_id'] = 'RY' . str_pad($apptDet['appointment_id'], 6, '0', STR_PAD_LEFT);

        $apptDet['speed_in_mts'] = $speed_in_mts;

        $apptDet['appt_duration'] = $duration_in_mts;

        $apptDet['appt_distance'] = round($dis_in_km, 4);

        $invoMail = $mail->sendInvoice($masData, $this->User, $apptDet);

        $updateInvoiceDetailsQry = "update appointment set payment_status = '" . $payStatus . "',inv_id = '" . $apptDet['invoice_id'] . "'" . $transferString . " where appointment_id = '" . $apptDet['appointment_id'] . "'"; //mas_id = '" . $masData['mas_id'] . "' and slave_id = '" . $this->User['entityId'] . "' and 
        mysql_query($updateInvoiceDetailsQry, $this->db->conn);

        $aplPushContent = array('alert' => $message, 'nt' => '11', 'n' => $this->User['firstName'] . ' ' . $this->User['last_name'], 'd' => $args['ent_appnt_dt'], 'e' => $this->User['email']);
        $andrPushContent = array("payload" => $message, 'action' => '11', 'sname' => $this->User['firstName'] . ' ' . $this->User['last_name'], 'dt' => $args['ent_appnt_dt'], 'smail' => $this->User['email']);

        $this->ios_cert_path = IOS_DRIVER_PEM_PATH;
        $this->ios_cert_pwd = IOS_DRIVER_PEM_PASS;
        $this->androidApiKey = ANDROID_DRIVER_PUSH_KEY;
        $push = $this->_sendPush($this->User['entityId'], array($masData['mas_id']), $message, '11', $this->User['email'], $this->curr_date_time, '1', $aplPushContent, $andrPushContent);
        $out = array('push' => $push, 'charge' => $pay, 'token' => $accessToken, 'invoice' => $invoMail, 'qry' => $updateInvoiceDetailsQry . $getApptDetQry);
        return $this->_getStatusMessage(84, $out);
    }

    /*
     * Method name: reportDispute
     * Desc: Get workplace types data
     * Input: Request data
     * Output:  success array if got it else error according to the result
     */

    protected function reportDispute($args) {

        if ($args['ent_date_time'] == '')
            return $this->_getStatusMessage(1, 'Date time');
        else if ($args['ent_report_msg'] == '')
            return $this->_getStatusMessage(1, 'Dispute message');

        $this->curr_date_time = urldecode($args['ent_date_time']);
        $args['ent_appnt_dt'] = urldecode($args['ent_appnt_dt']);

        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], '2');

        if (is_array($returned))
            return $returned;

        $getApptDetQry = "select a.status,a.appt_lat,a.appt_long,a.address_line1,a.address_line2,a.created_dt,a.arrive_dt,a.appointment_dt,a.amount,a.appointment_id,a.last_modified_dt,a.mas_id from appointment a where a.appointment_dt = '" . $args['ent_appnt_dt'] . "' and a.slave_id = '" . $this->User['entityId'] . "'";
        $apptDet = mysql_fetch_assoc(mysql_query($getApptDetQry, $this->db->conn));

        if ($apptDet['status'] == '4')
            return $this->_getStatusMessage(41, 3);

        $insertIntoReportQry = "insert into reports(mas_id,slave_id,appointment_id,report_msg,report_dt) values('" . $apptDet['mas_id'] . "','" . $this->User['entityId'] . "','" . $apptDet['appointment_id'] . "','" . $args['ent_report_msg'] . "','" . $this->curr_date_time . "')";
        mysql_query($insertIntoReportQry, $this->db->conn);

        if (mysql_insert_id() > 0) {
            $updateQryReq = "update appointment set payment_status = '2' where appointment_id = '" . $apptDet['appointment_id'] . "'";
            mysql_query($updateQryReq, $this->db->conn);

            $message = "Dispute reported for appointment dated " . date('d-m-Y h:i a', strtotime($apptDet['appointment_dt'])) . " on " . APP_NAME . "!";

            $aplPushContent = array('alert' => $message, 'nt' => '13', 'n' => $this->User['firstName'] . ' ' . $this->User['last_name'], 'd' => $args['ent_appnt_dt'], 'e' => $this->User['email'], 'bid' => $apptDet['appointment_id']);
            $andrPushContent = array("payload" => $message, 'action' => '13', 'sname' => $this->User['firstName'] . ' ' . $this->User['last_name'], 'dt' => $args['ent_appnt_dt'], 'smail' => $this->User['email'], 'bid' => $apptDet['appointment_id']);

            $this->ios_cert_path = $this->ios_uberx_driver;
            $this->ios_cert_pwd = $this->ios_mas_pwd;
            $this->androidApiKey = ANDROID_DRIVER_PUSH_KEY;
            $push = $this->_sendPush($this->User['entityId'], array($apptDet['mas_id']), $message, '13', $this->User['email'], $this->curr_date_time, '1', $aplPushContent, $andrPushContent);

            $errMsgArr = $this->_getStatusMessage(85, $push);
        } else {
            $errMsgArr = $this->_getStatusMessage(86, 76);
        }

        return array('errNum' => $errMsgArr['errNum'], 'errFlag' => $errMsgArr['errFlag'], 'errMsg' => $errMsgArr['errMsg'], 't' => $insertIntoReportQry);
    }

    /*
     * Method name: getProfile
     * Desc: Get slave profile data
     * Input: Request data
     * Output:  success array if got it else error according to the result
     */

    protected function getProfile($args) {

        if ($args['ent_date_time'] == '')
            return $this->_getStatusMessage(1, 'Date time');

        $this->curr_date_time = urldecode($args['ent_date_time']);

        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], '2');

        if (is_array($returned))
            return $returned;

        $selectProfileQry = "select * from slave where slave_id = '" . $this->User['entityId'] . "'";
        $profileData = mysql_fetch_assoc(mysql_query($selectProfileQry, $this->db->conn));

        if (!is_array($profileData))
            $errMsgArr = $this->_getStatusMessage(20, 20);

        $errMsgArr = $this->_getStatusMessage(33, 2);
        return array('errNum' => $errMsgArr['errNum'], 'errFlag' => $errMsgArr['errFlag'], 'errMsg' => $errMsgArr['errMsg'], 'fName' => $profileData['first_name'], 'lName' => $profileData['last_name'], 'email' => $profileData['email'], 'phone' => $profileData['phone'], 'pPic' => ($profileData['profile_pic'] == '' ? $this->default_profile_pic : $profileData['profile_pic']));
    }

    /*
     * Method name: getWorkplaces
     * Desc: Get workplace types data
     * Input: Request data
     * Output:  success array if got it else error according to the result
     */

    protected function getWorkplaces($args) {

        if ($args['ent_date_time'] == '')
            return $this->_getStatusMessage(1, 'Date time');

        $this->curr_date_time = urldecode($args['ent_date_time']);

        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], '2');

        if (is_array($returned))
            return $returned;

        $errMsgArr = $this->_getStatusMessage(33, 2);
        return array('errNum' => $errMsgArr['errNum'], 'errFlag' => $errMsgArr['errFlag'], 'errMsg' => $errMsgArr['errMsg'], 'types' => $this->getWorkplaceTypes());
    }

    /*
     * Method name: updateProfile
     * Desc: Update slave profile data
     * Input: Request data
     * Output:  success array if got it else error according to the result
     */

    protected function updateProfile($args) {

        if (($args['ent_first_name'] == '' && $args['ent_email'] == '' && $args['ent_last_name'] == '' && $args['ent_phone'] == '') || $args['ent_date_time'] == '')
            return $this->_getStatusMessage(1, 'Update');
        else if ($args['ent_date_time'] == '')
            return $this->_getStatusMessage(1, 'Date time');

        $this->curr_date_time = urldecode($args['ent_date_time']);

        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], '2');

        if (is_array($returned))
            return $returned;

        if ($args['ent_first_name'] != '')
            $update_name = " first_name = '" . $args['ent_first_name'] . "',";

        if ($args['ent_phone'] != '')
            $update_phone = " phone = '" . $args['ent_phone'] . "',";

        if ($args['ent_last_name'] != '')
            $update_lname = " last_name = '" . $args['ent_last_name'] . "',";

        if ($args['ent_email'] != '') {

            $checkEmailQry = "select slave_id from user where email = '" . $args['ent_email'] . "' and slave_id != '" . $this->User['entityId'] . "'";
            $checkEmailRes = mysql_query($checkEmailQry, $this->db->conn);

            if (mysql_num_rows($checkEmailRes) > 0)
                return $this->_getStatusMessage(2, 10);

            $update_email = " email = '" . $args['ent_email'] . "',";
        }

        $update_str = rtrim($update_name . $update_email . $update_phone . $update_lname, ',');

        $updateQry = "update slave set " . $update_str . ",last_active_dt = '" . $this->curr_date_time . "' where slave_id = '" . $this->User['entityId'] . "'";
        $updateRes = mysql_query($updateQry, $this->db->conn);

        if (mysql_affected_rows() > 0)
            return $this->_getStatusMessage(54, 39);
        else if ($updateRes)
            return $this->_getStatusMessage(54, 40);
        else
            return $this->_getStatusMessage(3, $updateQry);
    }

    /*
     * Method name: getMasterCarDetails
     * Desc: Get master car details that are active currently
     * Input: Request data
     * Output:  success array if got it else error according to the result
     */

    protected function getMasterCarDetails($args) {

        if ($args['ent_email'] == '')
            return $this->_getStatusMessage(1, 'Email');
        else if ($args['ent_date_time'] == '')
            return $this->_getStatusMessage(1, 'Date time');

        $this->curr_date_time = urldecode($args['ent_date_time']);

        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], '2');

        if (is_array($returned))
            return $returned;

        $masDet = $this->_getEntityDet($args['ent_email'], '1');

        if (!is_array($masDet))
            return $this->_getStatusMessage(37, 37);

        if ($masDet['workplace_id'] == 0)
            return $this->_getStatusMessage(37, 37);

        $getVehicleDataQry = "select wrk.workplace_id,wrk.License_Plate_No,(select v.vehiclemodel from vehiclemodel v,workplace w where w.Vehicle_Model = v.id  and w.workplace_id = wrk.workplace_id) as vehicle_model from workplace wrk,master m where m.workplace_id = wrk.workplace_id and m.mas_id = '" . $masDet['mas_id'] . "'";

        $getVehicleDataRes = mysql_query($getVehicleDataQry, $this->db->conn);

        $vehicleData = mysql_fetch_assoc($getVehicleDataRes);

        $errMsgArr = $this->_getStatusMessage(21, 50);
        return array('errNum' => $errMsgArr['errNum'], 'errFlag' => $errMsgArr['errFlag'], 'errMsg' => $errMsgArr['errMsg'], 'model' => $vehicleData['vehicle_model'], 'plateNo' => $vehicleData['License_Plate_No']);
    }

    /*
     * Method name: addCard
     * Desc: Add a card to the passenger profile
     * Input: Request data
     * Output:  success array if got it else error according to the result
     */

    protected function addCard($args) {

        if ($args['ent_token'] == '')
            return $this->_getStatusMessage(1, 'Card token');
        if ($args['ent_date_time'] == '')
            return $this->_getStatusMessage(1, 'Date time');

        $this->curr_date_time = urldecode($args['ent_date_time']);

        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], '2');

        if (is_array($returned))
            return $returned;

        if ($this->User['stripe_id'] == '') {

            $createCustomerArr = array('token' => $args['ent_token'], 'email' => $this->User['email']);

            $customer = $this->stripe->apiStripe('createCustomer', $createCustomerArr);

            if ($customer['error'])
                return array('errNum' => 16, 'errFlag' => 1, 'errMsg' => $customer['error']['message'], 'test' => 1);

            $updateQry = "update slave set stripe_id = '" . $customer['id'] . "',last_active_dt = '" . $this->curr_date_time . "' where slave_id = '" . $this->User['entityId'] . "'";
            mysql_query($updateQry, $this->db->conn);
            if (mysql_affected_rows() <= 0)
                return $this->_getStatusMessage(51, 50);

            $getCardArr = array('stripe_id' => $customer['id']);

            $card = $this->stripe->apiStripe('getCustomer', $getCardArr);

            if ($card['error'])
                return array('errNum' => 16, 'errFlag' => 1, 'errMsg' => $card['error']['message'], 'test' => 2);

            foreach ($card['sources']['data'] as $c) {
                $cardRes = array('id' => $c['id'], 'last4' => $c['last4'], 'type' => $c['brand'], 'exp_month' => $c['exp_month'], 'exp_year' => $c['exp_year']);
            }

            $errMsgArr = $this->_getStatusMessage(50, 50);
            return array('errNum' => $errMsgArr['errNum'], 'errFlag' => $errMsgArr['errFlag'], 'errMsg' => $errMsgArr['errMsg'], 'cards' => array($cardRes), 'def' => $card['default_source']); //, 'cards' => array('id' => $customer['data']['id'], 'last4' => $customer['data']['last4'], 'type' => $customer['data']['type'], 'exp_month' => $customer['data']['exp_month'], 'exp_year' => $customer['data']['exp_year']));
        }

        $addCardArr = array('stripe_id' => $this->User['stripe_id'], 'token' => $args['ent_token']);

        $card = $this->stripe->apiStripe('addCard', $addCardArr);

        if ($card['error'])
            return array('errNum' => 16, 'errFlag' => 1, 'errMsg' => $card['error']['message'], 'test' => 2);

        $getCard = $this->stripe->apiStripe('getCustomer', $addCardArr);

        foreach ($getCard['sources']['data'] as $card) {
            $cardsArr[] = array('id' => $card['id'], 'last4' => $card['last4'], 'type' => $card['brand'], 'exp_month' => $card['exp_month'], 'exp_year' => $card['exp_year']);
        }

        $errMsgArr = $this->_getStatusMessage(50, 50);
        return array('errNum' => $errMsgArr['errNum'], 'errFlag' => $errMsgArr['errFlag'], 'errMsg' => $errMsgArr['errMsg'], 'cards' => $cardsArr, 'def' => $getCard['default_source']);
    }

    /*
     * Method name: getCards
     * Desc: Add a card to the passenger profile
     * Input: Request data
     * Output:  success array if got it else error according to the result
     */

    protected function getCards($args) {

        if ($args['ent_date_time'] == '')
            return $this->_getStatusMessage(1, 'Date time');

        $this->curr_date_time = urldecode($args['ent_date_time']);

        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], '2');

        if (is_array($returned))
            return $returned;

        if ($this->User['stripe_id'] == '')
            return $this->_getStatusMessage(51, 51);

        $getCardArr = array('stripe_id' => $this->User['stripe_id']);

        $cardsArr = array();

        $card = $this->stripe->apiStripe('getCustomer', $getCardArr);

        if ($card['error'])
            return array('errNum' => 16, 'errFlag' => 1, 'errMsg' => $card['error']['message'], 'test' => 2);

        foreach ($card['sources']['data'] as $c) {
            $cardsArr[] = array('id' => $c['id'], 'last4' => $c['last4'], 'type' => $c['brand'], 'exp_month' => $c['exp_month'], 'exp_year' => $c['exp_year']);
        }

        if (count($cardsArr) > 0)
            $errNum = 52;
        else
            $errNum = 51;

        $errMsgArr = $this->_getStatusMessage($errNum, 52);
        return array('errNum' => $errMsgArr['errNum'], 'errFlag' => $errMsgArr['errFlag'], 'errMsg' => $errMsgArr['errMsg'], 'cards' => $cardsArr, 'def' => $card['default_source'], 'paypal' => ($this->User['paypal'] == '' ? 1 : 2));
    }

    /*
     * Method name: removeCard
     * Desc: Add a card to the passenger profile
     * Input: Request data
     * Output:  success array if got it else error according to the result
     */

    protected function removeCard($args) {

        if ($args['ent_cc_id'] == '')
            return $this->_getStatusMessage(1, 'Card id');
        else if ($args['ent_date_time'] == '')
            return $this->_getStatusMessage(1, 'Date time');

        $this->curr_date_time = urldecode($args['ent_date_time']);

        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], '2');

        if (is_array($returned))
            return $returned;

        if ($this->User['stripe_id'] == '')
            return $this->_getStatusMessage(51, 51);

        $remCardArr = array('stripe_id' => $this->User['stripe_id'], 'card_id' => $args['ent_cc_id']);

        $cardsArr = array();

        $card = $this->stripe->apiStripe('deleteCard', $remCardArr);

        if ($card->error)
            return array('errNum' => 16, 'errFlag' => 1, 'errMsg' => $card->error->message);

        foreach ($card->data as $card) {
            $cardsArr = array('id' => $card->data->id, 'last4' => $card->data->last4, 'type' => $card->data->brand, 'exp_month' => $card->data->exp_month, 'exp_year' => $card->data->exp_year);
        }

        $errMsgArr = $this->_getStatusMessage(52, 52);
        return array('errNum' => $errMsgArr['errNum'], 'errFlag' => $errMsgArr['errFlag'], 'errMsg' => $errMsgArr['errMsg'], 'cards' => $cardsArr);
    }

    /*
     * Method name: makeCardDefault
     * Desc: Make a card default in the passenger profile
     * Input: Request data
     * Output:  success array if got it else error according to the result
     */

    protected function makeCardDefault($args) {

        if ($args['ent_cc_id'] == '')
            return $this->_getStatusMessage(1, 'Card id');
        else if ($args['ent_date_time'] == '')
            return $this->_getStatusMessage(1, 'Date time');

        $this->curr_date_time = urldecode($args['ent_date_time']);

        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], '2');

        if (is_array($returned))
            return $returned;

        if ($this->User['stripe_id'] == '')
            return $this->_getStatusMessage(51, 51);

        $remCardArr = array('stripe_id' => $this->User['stripe_id'], 'card_id' => $args['ent_cc_id']);

        $cardsArr = array();

        $card = $this->stripe->apiStripe('updateCustomerDefCard', $remCardArr);

        if ($card->error)
            return array('errNum' => 16, 'errFlag' => 1, 'errMsg' => $card->error->message);

        foreach ($card->sources->data as $c) {
            $cardsArr[] = array('id' => $c->id, 'last4' => $c->last4, 'type' => $c->brand, 'exp_month' => $c->exp_month, 'exp_year' => $c->exp_year);
        }

        $errMsgArr = $this->_getStatusMessage(52, 52);
        return array('errNum' => $errMsgArr['errNum'], 'errFlag' => $errMsgArr['errFlag'], 'errMsg' => $errMsgArr['errMsg'], 'cards' => $cardsArr, 'def' => $card->default_source);
    }

    /*
     * Method name: validateEmailZip
     * Desc: Validates the email and zipcode
     * Input: Token
     * Output:  gives error array if unavailable
     */

    protected function validateEmailZip($args) {

        if ($args['ent_email'] == '')
            return $this->_getStatusMessage(1, 'Email');
        else if ($args['ent_user_type'] == '')
            return $this->_getStatusMessage(1, 'User type');


        if ($args['ent_user_type'] == '1')
            $verifyEmail = $this->_verifyEmail($args['ent_email'], 'mas_id', 'master'); //_verifyEmail($email,$field,$table);
        else
            $verifyEmail = $this->_verifyEmail($args['ent_email'], 'slave_id', 'slave'); //_verifyEmail($email,$field,$table);

        if (is_array($verifyEmail))
            $email = array('errFlag' => 1);
        else
            $email = array('errFlag' => 0);


//                $vmail = new verifyEmail();
//
//                if ($vmail->check($args['ent_email'])) {
//                    $email = $this->_getStatusMessage(34, $args['ent_email']);
//                } else if ($vmail->isValid($args['ent_email'])) {
//                    $email = $this->_getStatusMessage(24, $args['ent_email']); //_getStatusMessage($errNo, $test_num);
//                    //echo 'email valid, but not exist!';
//                } else {
//                    $email = $this->_getStatusMessage(25, $args['ent_email']); //_getStatusMessage($errNo, $test_num);
//                    //echo 'email not valid and not exist!';
//                }
//        $selectZipQry = "select zipcode from zipcodes where zipcode = '" . $args['zip_code'] . "'";
//        $selectZipRes = mysql_query($selectZipQry, $this->db->conn);
//        if (mysql_num_rows($selectZipRes) > 0)
        $zip = array('errFlag' => 0);
//        else
//            $zip = array('errFlag' => 1);


        if ($email['errFlag'] == 0 && $zip['errFlag'] == 0)
            return $this->_getStatusMessage(47, $verifyEmail);
        else if ($email['errFlag'] == 1 && $zip['errFlag'] == 1)
            return $this->_getStatusMessage(46, $verifyEmail);
        else if ($email['errFlag'] == 0 && $zip['errFlag'] == 1)
            return $this->_getStatusMessage(46, $verifyEmail);
        else if ($email['errFlag'] == 1 && $zip['errFlag'] == 0)
            return $this->_getStatusMessage(2, $verifyEmail);
    }

    /*
     * Method name: validateEmail
     * Desc: Validates the email
     * Input: Token
     * Output:  gives error array if unavailable
     */

    protected function validateEmail($args) {

        if ($args['ent_email'] == '')
            return $this->_getStatusMessage(1, 'Email');

        $vmail = new verifyEmail();

        if ($vmail->check($args['ent_email'])) {
            return $this->_getStatusMessage(34, $args['ent_email']);
        } else if ($vmail->isValid($args['ent_email'])) {
            return $this->_getStatusMessage(24, $args['ent_email']); //_getStatusMessage($errNo, $test_num);
//echo 'email valid, but not exist!';
        } else {
            return $this->_getStatusMessage(25, $args['ent_email']); //_getStatusMessage($errNo, $test_num);
//echo 'email not valid and not exist!';
        }
    }

    /*
     * Method name: resetPassword
     * Desc: User can reset the password from with in the app
     * Input: Token
     * Output:  gives error array if failed
     */

    protected function resetPassword($args) {

        if ($args['ent_date_time'] == '')
            return $this->_getStatusMessage(1, 'Date time');

        $this->curr_date_time = urldecode($args['ent_date_time']);

        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], '1');

        if (is_array($returned))
            return $returned;

        $deleteAllSessionsQry = "update user_sessions set loggedIn = 2 where oid = '" . $this->User['entityId'] . "' and user_type = '1'";
        mysql_query($deleteAllSessionsQry, $this->db->conn);

        if (mysql_affected_rows() <= 0)
            return $this->_getStatusMessage(6, $deleteAllSessionsQry);

        $randData = $this->_generateRandomString(20) . '_1';

        $mail = new sendAMail(APP_SERVER_HOST);
        $resetRes = $mail->forgotPassword($this->User, $randData);

        if ($resetRes['flag'] == 0) {
            $updateResetDataQry = "update master set resetData = '" . $randData . "', resetFlag = 1 where email = '" . $this->User['email'] . "'";
            mysql_query($updateResetDataQry, $this->db->conn);
//            $resetRes['update'] = $updateResetDataQry;
            return $this->_getStatusMessage(67, $resetRes);
        } else {
            return $this->_getStatusMessage(68, $resetRes);
        }
    }

    /*
     * Method name: updateMasterStatus
     * Desc: Update master status
     * Input: Token
     * Output:  gives error array if failed
     */

    protected function updateMasterStatus($args) {



        $arr = array('3', '4');

        if ($args['ent_date_time'] == '')
            return $this->_getStatusMessage(1, 'Date time');
        else if ($args['ent_status'] == '')
            return $this->_getStatusMessage(1, 'Status');
        else if (!in_array($args['ent_status'], $arr))
            return $this->_getStatusMessage(1, 'Status');

        $this->curr_date_time = urldecode($args['ent_date_time']);

        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], '1');

        if (is_array($returned))
            return $returned;

        $location = $this->mongo->selectCollection('location');
        $statusLog = $this->mongo->selectCollection('statusLog');

        $update['location'] = $location->update(array('user' => (int) $this->User['entityId']), array('$set' => array('status' => (int) $args['ent_status'])));
        $update['status_log'] = $statusLog->insert(array('master' => (int) $this->User['entityId'], 'status' => (int) $args['ent_status'], 'time' => time()));

        return $this->_getStatusMessage(69, $update);
    }

    /*
     * Method name: forgotPassword
     * Desc: send mail for forgot password
     * Input: Token
     * Output:  gives error array if failed
     */

    protected function forgotPassword($args) {

        if ($args['ent_email'] == '')
            return $this->_getStatusMessage(1, 'Email');
        else if ($args['ent_user_type'] == '')
            return $this->_getStatusMessage(1, 'User type');

        if ($args['ent_user_type'] == '1') {
            $table = 'master';
            $uid = 'mas_id';
        } else if ($args['ent_user_type'] == '2') {
            $table = 'slave';
            $uid = 'slave_id';
        } else {
            return $this->_getStatusMessage(1, 'User type');
        }

        $selectUserQry = "select status,email,password,$uid from $table where email = '" . $args['ent_email'] . "'";
        $selectUserRes = mysql_query($selectUserQry, $this->db->conn);

        if (mysql_num_rows($selectUserRes) <= 0)
            return $this->_getStatusMessage(66, $selectUserQry);

        $userData = mysql_fetch_assoc($selectUserRes);

        if ($userData['status'] == '1' || $userData['status'] == '2')
            return $this->_getStatusMessage(10, $selectUserQry);

        if ($userData['status'] == '4')
            return $this->_getStatusMessage(94, $selectUserQry);

        $randData = $this->_generateRandomString(20) . '_' . $args['ent_user_type'];

        $mail = new sendAMail(APP_SERVER_HOST);
        $resetRes = $mail->forgotPassword($userData, $randData);

        if ($resetRes['flag'] == 0) {
            $updateResetDataQry = "update $table set resetData = '" . $randData . "', resetFlag = 1 where email = '" . $args['ent_email'] . "'";
            mysql_query($updateResetDataQry, $this->db->conn);
//$resetRes['update'] = $updateResetDataQry;
            return $this->_getStatusMessage(67, $resetRes);
        } else {
            return $this->_getStatusMessage(68, $resetRes);
        }
    }

    /*
     * Method name: checkSession
     * Desc: Check session of any users
     * Input: Request data
     * Output:  Complete profile details if available, else error message
     */

    protected function checkSession($args) {

        if ($args['ent_user_type'] == '')
            return $this->_getStatusMessage(1, 'User type');
        if ($args['ent_date_time'] == '')
            return $this->_getStatusMessage(1, 'Date time');

        $this->curr_date_time = urldecode($args['ent_date_time']);

        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], $args['ent_user_type']);

        if (is_array($returned))
            return $returned;
        else
            return $this->_getStatusMessage(73, 15);
    }

    /*
     * Method name: checkCoupon
     * Desc: Check coupon exists or not, if exists check the expirty
     * Input: Request data
     * Output:  Success if available else error
     */

    protected function checkCoupon($args) {
        
//         $notifications = $this->mongo->selectCollection('notificationsOne');
//       $args =  $notifications->findOne(array('_id' => new MongoId('576ea54b5853f2ba5e4663db')));

        if ($args['ent_coupon'] == '')
            return $this->_getStatusMessage(1, 'Coupon');
        if ($args['ent_lat'] == '' || $args['ent_long'] == '')
            return $this->_getStatusMessage(1, 'Location');

        $this->curr_date_time = urldecode($args['ent_date_time']);

        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], '2');

        if (is_array($returned))
            return $returned;

        $couponsColl = $this->mongo->selectCollection('coupons');

        $cond = array('status' => 0, 'coupon_code' => $args['ent_coupon'], '$or' => array(array('coupon_type' => 3, 'user_id' => (string) $this->User['entityId'], 'status' => 0, 'expiry_date' => array('$gte' => time())), array('coupon_type' => 2, 'bookings.slave_id' => array('$ne' => (string) $this->User['entityId']), 'expiry_date' => array('$gte' => time()))));
//return $cond;
        $couponsCollOne = $couponsColl->findOne($cond);

        if (!is_array($couponsCollOne)){
             $cond_Slave = array('status' => 0, 'coupon_code' => $args['ent_coupon'],'coupon_type' => 2, 'bookings.slave_id' => (string) $this->User['entityId']);
            $couponsCollOne_ = $couponsColl->findOne($cond_Slave);
             if (is_array($couponsCollOne_))
             return $this->_getStatusMessage(128, 1);
                    
            return $this->_getStatusMessage(116, 1);
        }

        $resultArr = $this->mongo->selectCollection('$cmd')->findOne(array(
            'geoNear' => 'coupons',
            'near' => array(
                (double) $args['ent_long'], (double) $args['ent_lat']
            ), 'spherical' => true, 'maxDistance' => ($this->promoCodeRadius * 1000) / 6378137, 'distanceMultiplier' => 6378137,
            'query' => $cond));

        if (count($resultArr['results']) <= 0)
            return $this->_getStatusMessage(116, 2);

        $findOne = $resultArr['results'][0]['obj'];

        $findOne['current'] = time();

        if ($findOne['start_date'] > strtotime($this->curr_date_time) || $findOne['expiry_date'] < strtotime($this->curr_date_time))
            return $this->_getStatusMessage(116, $findOne);

//        if (!is_array($findOne))
//            return $this->_getStatusMessage(96, 82);
//        else if ($findOne['status'] == 1)
//            return $this->_getStatusMessage(97, 82);

        $errMsgArr = $this->_getStatusMessage(119, 52);
        return array('errNum' => $errMsgArr['errNum'], 'errFlag' => $errMsgArr['errFlag'], 'errMsg' => sprintf($errMsgArr['errMsg'], $findOne['discount'] . ($findOne['discount_type'] == 1 ? '%' : '')), 'discount' => $findOne['discount'], 'code' => $args['ent_coupon']);
    }

//    protected function checkCoupon($args) {
//
//        if ($args['ent_coupon'] == '')
//            return $this->_getStatusMessage(1, 'Coupon');
//        if ($args['ent_lat'] == '' || $args['ent_long'] == '')
//            return $this->_getStatusMessage(1, 'Location');
//
//        $this->curr_date_time = urldecode($args['ent_date_time']);
//
//        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], '2');
//
//        if (is_array($returned))
//            return $returned;
//
//        $checkCouponQry = "select * from coupons where coupon_code = '" . $args['ent_coupon'] . "' and ((coupon_type = 3 and user_id = '" . $this->User['entityId'] . "') or (coupon_type = 2 and city_id in (select ca.City_Id from city_available ca where (3956 * acos( cos( radians('" . $args['ent_lat'] . "') ) * cos( radians(ca.City_Lat) ) * cos( radians(ca.City_Long) - radians('" . $args['ent_long'] . "') ) + sin( radians('" . $args['ent_lat'] . "') ) * sin( radians(ca.City_Lat) ) ) ) <= " . $this->promoCodeRadius . "))) and status = 0 and expiry_date > '" . date('Y-m-d', time()) . "' and start_date <= '" . date('Y-m-d', time()) . "'";
//        $checkCouponRes = mysql_query($checkCouponQry, $this->db->conn);
//        if (mysql_num_rows($checkCouponRes) > 0) {
//
//            $getDataQry = "select appointment_id from coupon_usage where coupon_code = '" . $args['ent_coupon'] . "' and user_id = '" . $this->User['entityId'] . "'";
//            $couponData = mysql_query($getDataQry, $this->db->conn);
//            if (mysql_num_rows($couponData) > 0) {
//                return $this->_getStatusMessage(118, 15);
//            }
//
//            $couponDet = mysql_fetch_assoc($checkCouponRes);
//
//            $errMsgArr = $this->_getStatusMessage(119, 52);
//            return array('errNum' => $errMsgArr['errNum'], 'errFlag' => $errMsgArr['errFlag'], 'errMsg' => sprintf($errMsgArr['errMsg'], $couponDet['discount'], ($couponDet['discount_type'] == 1 ? '%' : '')));
//        } else {
//
//            return $this->_getStatusMessage(116, $checkCouponQry);
//        }
//    }

    /*
     * Method name: verifyCode
     * Desc: Check coupon exists or not, if exists check the expirty
     * Input: Request data
     * Output:  Success if available else error
     */

    protected function verifyCode($args) {

        if ($args['ent_coupon'] == '')
            return $this->_getStatusMessage(1, 'Coupon');
        else if ($args['ent_lat'] == '' || $args['ent_long'] == '')
            return $this->_getStatusMessage(1, 'Location');

        $this->User['lang'] = $args['ent_lang'];

        $cond = array('status' => 0, 'coupon_code' => $args['ent_coupon'], 'coupon_type' => 1, 'user_type' => 1);

        $couponsColl = $this->mongo->selectCollection('coupons');

        $couponsCollOne = $couponsColl->findOne($cond);

        if (!is_array($couponsCollOne))
            return $this->_getStatusMessage(100, $resultArr);

        $resultArr = $this->mongo->selectCollection('$cmd')->findOne(array(
            'geoNear' => 'coupons',
            'near' => array(
                (double) $args['ent_long'], (double) $args['ent_lat']
            ), 'spherical' => true, 'maxDistance' => ($this->promoCodeRadius * 1000) / 6378137, 'distanceMultiplier' => 6378137,
            'query' => $cond)
        );

        if (count($resultArr['results']) > 0) {
            return $this->_getStatusMessage(101, 1);
        } else {
            return $this->_getStatusMessage(100, 2);
        }
    }

    /*
     * Method name: getApptStatus
     * Desc: Get appointment status
     * Input: nothing
     * Output:  gives status if available else error msg
     */

    protected function getApptStatus($args) {

        
        $favouritesold = $this->mongo->selectCollection('favouriteOld');
        $favouritesold->insert($args);
        
        
        if ($args['ent_date_time'] == '')
            return $this->_getStatusMessage(1, 15);


        $this->curr_date_time = urldecode($args['ent_date_time']);

        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], $args['ent_user_type']);

        if (is_array($returned))
            return $returned;

        $location = $this->mongo->selectCollection('location');

        if ($args['ent_user_type'] == 1) {
            $masStatus = $location->findOne(array('user' => (int) $this->User['entityId']));
            $queyrDrivervehicleInfo = "select (select count(appointment_id) from appointment where appointment_dt like '%" . date('Y-m-d') . "%' and status = 9 and mas_id = '" . $this->User['entityId'] . "') as tripsToday,"
                    . "(select IFNULL(sum(mas_earning),0) from appointment where appointment_dt like '%" . date('Y-m-d') . "%' and status = 9 and mas_id = '" . $this->User['entityId'] . "') as earningsToday,"
                    . "(select IFNULL(mas_earning,0) from appointment where status = 9 and mas_id = '" . $this->User['entityId'] . "' order by appointment_id desc limit 1) as lastTripPrice,"
                    . "(select appointment_dt from appointment where  status = 9 and mas_id = '" . $this->User['entityId'] . "' order by appointment_id desc limit 1) as lastTripTime,"
                    . "(select type_name  from appointment a,workplace_types wt where  a.type_id = wt.type_id and a.status = 9 and a.mas_id = '" . $this->User['entityId'] . "' order by appointment_id desc limit 1) as carType,"
                    . "ROUND((ifnull((select sum(RechargeAmount) from DriverRecharge where  mas_id = '" . $this->User['entityId'] . "'),0) - ifnull((select sum(app_owner_pl) from appointment where status = 9  and mas_id = '" . $this->User['entityId'] . "'),0)),2) as wallet";

            $driverRechargeData = mysql_fetch_assoc(mysql_query($queyrDrivervehicleInfo, $this->db->conn));
        }


        if ($args['ent_appnt_dt'] != '') {

            if ($args['ent_user_type'] == '1') {

                $selectStatusQry = "select a.expire_ts,a.user_device,a.status,a.appointment_id,a.payment_status,a.payment_type,(select status from master where mas_id = '" . $this->User['entityId'] . "') as master_status from appointment a where a.mas_id = '" . $this->User['entityId'] . "' and a.appointment_dt = '" . $args['ent_appnt_dt'] . "' and a.status != '10'";
            } else {
                $selectStatusQry = "select a.tip_amount,a.tip_percent,a.user_device,a.payment_type,a.payment_status,a.appointment_dt,a.cancel_status,a.amount,a.address_line1,a.address_line2,a.drop_addr1,a.drop_addr2,a.duration,a.appt_lat,a.appt_long,a.drop_lat,a.arrive_dt,a.complete_dt,a.drop_long,a.appointment_id,a.status,a.mas_id,d.profile_pic,d.mobile,d.first_name,d.last_name,d.email,";
                $selectStatusQry .= "(select report_msg from reports where appointment_id = a.appointment_id limit 0,1) as report_msg,(select License_Plate_No from workplace where workplace_id = a.car_id) as licencePlate,(select v.vehiclemodel from vehiclemodel v, workplace w where w.Vehicle_Model = v.id and w.workplace_id = a.car_id) as vehicle_model, ";
                $selectStatusQry .= "(select wt.MapIcon from workplace_types wt,workplace w where w.type_id = wt.type_id and w.workplace_id = d.workplace_id) as mapicon,(select wt.vehicle_img from workplace_types wt,workplace w where w.type_id = wt.type_id and w.workplace_id = d.workplace_id) as vehicleimg,";
                $selectStatusQry .= "(select wt.price_per_km from workplace_types wt,workplace w where w.type_id = wt.type_id and w.workplace_id = d.workplace_id) as price_per_km,(select mas_id from master_ratings where appointment_id = a.appointment_id) as rateStatus from appointment a,master d ";
                $selectStatusQry .= " where a.mas_id = d.mas_id  and a.slave_id = '" . $this->User['entityId'] . "' and a.status != 10 and a.appointment_dt = '" . $args['ent_appnt_dt'] . "' order by appointment_id DESC";
            }
            $selectStatusRes = mysql_query($selectStatusQry, $this->db->conn);
            $statArr = mysql_fetch_assoc($selectStatusRes);

            if (is_array($statArr))
                $errMsgArr = $this->_getStatusMessage(21, 52);
            else
                $errMsgArr = $this->_getStatusMessage(49, 52);

            if ($args['ent_user_type'] == '2') {
                $masterData = $location->findOne(array('user' => (int) $statArr['mas_id']));
            }

            if ($args['ent_user_type'] == '2') {
                return array('errNum' => $errMsgArr['errNum'], 'errFlag' => $errMsgArr['errFlag'], 'errMsg' => $errMsgArr['errMsg'], 'payType' => $statArr['payment_type'], 'pasChn' => 'qp_' . $statArr['user_device'], 'plateNo' => $statArr['licencePlate'], 'model' => $statArr['vehicle_model'], 'chn' => $masterData['chn'], 'r' => round($masterData['rating'], 1), 'ltg' => $masterData['location']['latitude'] . ',' . $masterData['location']['longitude'], 'bid' => $statArr['appointment_id'], 'status' => $statArr['status'], 'bid' => $statArr['appointment_id'], 'fName' => $statArr['first_name'], 'lName' => $statArr['last_name'], 'mobile' => $statArr['mobile'], 'addr1' => urldecode($statArr['address_line1']), 'addr2' => urldecode($statArr['address_line2']), 'dropAddr1' => urldecode($statArr['drop_addr1']), 'dropAddr2' => urldecode($statArr['drop_addr2']), 'amount' => number_format($statArr['amount'], 2, '.', ''), 'pPic' => (($statArr['profile_pic'] === '') ? $this->default_profile_pic : $statArr['profile_pic']), 'dur' => $statArr['duration'], 'pickLat' => $statArr['appt_lat'], 'pickLong' => $statArr['appt_long'], 'dropLat' => $statArr['drop_lat'], 'dropLong' => $statArr['drop_long'], 'apptDt' => $statArr['appointment_dt'], 'pickupDt' => $statArr['arrive_dt'], 'dropDt' => $statArr['complete_dt'], 'email' => $statArr['email'], 'discount' => '0.00', 'rateStatus' => ($statArr['rateStatus'] === '') ? 1 : 2, 'payStatus' => ($statArr['payment_status'] == '') ? 0 : $statArr['payment_status'], 'reportMsg' => $statArr['report_msg'], 'share' => $this->share . $statArr['appointment_id'], 'carImage' => $statArr['vehicleimg'], 'carMapImage' => $statArr['mapicon'], 'tip' => $statArr['tip_amount'], 'tipPercent' => $statArr['tip_percent']); //,'t'=>$selectStatusQry);
            } else {
                return array('errNum' => $errMsgArr['errNum'], 'errFlag' => $errMsgArr['errFlag'], 'errMsg' => $errMsgArr['errMsg'], 'payType' => $statArr['payment_type'], 'pasChn' => 'qp_' . $statArr['user_device'], 'nt' => ($statArr['appt_type'] == '1' ? '' : '51'), 'plateNo' => $statArr['licencePlate'], 'model' => $statArr['vehicle_model'], 'bid' => $statArr['appointment_id'], 'chn' => $masterData['chn'], 'r' => round($masterData['rating'], 1), 'ltg' => $masterData['location']['latitude'] . ',' . $masterData['location']['longitude'], 'status' => $statArr['status'], 'bid' => $statArr['appointment_id'], 'rateStatus' => ($statArr['rateStatus'] == '') ? 1 : 2, 'payStatus' => ($statArr['payment_status'] == '') ? 0 : 1, 'pPic' => (($statArr['profile_pic'] === '') ? $this->default_profile_pic : $statArr['profile_pic']), 'mobile' => $statArr['mobile'], 'email' => $statArr['email'], 'fName' => $statArr['first_name'], 'apptDt' => $statArr['appointment_dt'], 'masStatus' => $statArr['master_status'], 'share' => $this->share . $statArr['appointment_id']); //,'t'=>$selectStatusQry);
            }
        }

        if ($args['ent_user_type'] == '2') {
            $selectAppntsQry = "select a.tip_amount,a.tip_percent,a.user_device,a.payment_type,a.payment_status,a.appointment_dt,a.cancel_status,a.amount,a.address_line1,a.address_line2,a.drop_addr1,a.drop_addr2,a.duration,a.appt_lat,a.appt_long,a.drop_lat,a.arrive_dt,a.complete_dt,a.drop_long,a.appointment_id,a.status,a.mas_id,d.profile_pic,d.mobile,d.first_name,d.last_name,d.email,";
//            $selectAppntsQry .= "(select mas_id from master_ratings where appointment_id = a.appointment_id) as reveiw_flag, ";
            $selectAppntsQry .= "(select report_msg from reports where appointment_id = a.appointment_id limit 0,1) as report_msg,(select License_Plate_No from workplace where workplace_id = a.car_id) as licencePlate,(select v.vehiclemodel from vehiclemodel v, workplace w where w.Vehicle_Model = v.id and w.workplace_id = a.car_id) as vehicle_model, ";
            $selectAppntsQry .= "(select wt.MapIcon from workplace_types wt,workplace w where w.type_id = wt.type_id and w.workplace_id = d.workplace_id) as mapicon,(select wt.vehicle_img from workplace_types wt,workplace w where w.type_id = wt.type_id and w.workplace_id = d.workplace_id) as vehicleimg,";
            $selectAppntsQry .= "(select wt.price_per_km from workplace_types wt,workplace w where w.type_id = wt.type_id and w.workplace_id = d.workplace_id) as price_per_km,(select mas_id from master_ratings where appointment_id = a.appointment_id limit 0,1) as rateStatus from appointment a,master d ";
            $selectAppntsQry .= " where a.mas_id = d.mas_id  and a.slave_id = '" . $this->User['entityId'] . "' and a.status IN (6,7,8,9)  order by appointment_id DESC limit 0,1";
        } else {
            $selectAppntsQry = "select a.expire_ts,a.appt_type,a.payment_type,a.user_device,a.payment_status,a.appointment_dt,a.amount,a.address_line1,a.address_line2,a.drop_addr1,a.drop_addr2,a.appointment_dt,a.duration,a.appt_lat,a.appt_long,a.appointment_id,a.drop_lat,a.drop_long,a.arrive_dt,a.complete_dt,a.status,s.profile_pic,s.phone as mobile,s.first_name,s.last_name,s.email,(select status from master where mas_id = '" . $this->User['entityId'] . "') as master_status ,";
            $selectAppntsQry .= "(select report_msg from reports where appointment_id = a.appointment_id limit 0,1) as report_msg,";
            $selectAppntsQry .= "(select wt.price_per_km from workplace_types wt,workplace w,master d where w.type_id = wt.type_id and w.workplace_id = d.workplace_id and d.mas_id = a.mas_id) as price_per_km ";
            $selectAppntsQry .= " from appointment a,slave s  where a.slave_id = s.slave_id and a.mas_id = '" . $this->User['entityId'] . "' and a.status IN (1,6,7,8) ";
        }

        $selectStatusRes = mysql_query($selectAppntsQry, $this->db->conn);

        $appts = array();

//        $location = $this->mongo->selectCollection('location');
//        $location->ensureIndex(array('user' => 1));

        while ($apptData = mysql_fetch_assoc($selectStatusRes)) {

            $masterData = array();

            if ($args['ent_user_type'] == '2') {
                $masterData = $location->findOne(array('user' => (int) $apptData['mas_id']));
            }

            if (($args['ent_user_type'] == '2' && $apptData['rateStatus'] == '') || $args['ent_user_type'] == '1')
                $appts[] = array('payType' => $apptData['payment_type'], 'nt' => ($apptData['appt_type'] == '1' ? '' : '51'), 'pasChn' => 'qp_' . $apptData['user_device'], 'plateNo' => $apptData['licencePlate'], 'model' => $apptData['vehicle_model'],'apptDt' => $apptData['appointment_dt'],'email'=>$masterData['email'], 'chn' => $masterData['chn'], 'r' => round($masterData['rating'], 1), 'ltg' => $masterData['location']['latitude'] . ',' . $masterData['location']['longitude'], 'bid' => $apptData['appointment_id'], 'status' => $apptData['status'], 'bid' => $apptData['appointment_id'], 'fName' => $apptData['first_name'], 'lName' => $apptData['last_name'], 'mobile' => $apptData['mobile'], 'addr1' => urldecode($apptData['address_line1']), 'addr2' => urldecode($apptData['address_line2']), 'dropAddr1' => urldecode($apptData['drop_addr1']), 'dropAddr2' => urldecode($apptData['drop_addr2']), 'amount' => number_format($apptData['amount'], 2, '.', ''), 'pPic' => (($apptData['profile_pic'] === '') ? $this->default_profile_pic : $apptData['profile_pic']), 'dur' => $apptData['duration'], 'pickLat' => $apptData['appt_lat'], 'pickLong' => $apptData['appt_long'], 'dropLat' => $apptData['drop_lat'], 'dropLong' => $apptData['drop_long'], 'apptDt' => $apptData['appointment_dt'], 'pickupDt' => $apptData['arrive_dt'], 'dropDt' => $apptData['complete_dt'], 'email' => $apptData['email'], 'discount' => '0.00', 'rateStatus' => ($apptData['rateStatus'] === '') ? 1 : 2, 'payStatus' => ($apptData['payment_status'] == '') ? 0 : $apptData['payment_status'], 'reportMsg' => $apptData['report_msg'], 'share' => $this->share . $apptData['appointment_id'], 'carImage' => $apptData['vehicleimg'], 'carMapImage' => $apptData['mapicon'], 'tip' => $apptData['tip_amount'], 'tipPercent' => $apptData['tip_percent'], 'expireSec' => ($apptData['expire_ts'] - time() > 0 ? $apptData['expire_ts'] - time() : 0),);
        }

        if (count($appts) > 0) {
            $errMsgArr = $this->_getStatusMessage(21, 52);
        } else {
            $errMsgArr = $this->_getStatusMessage(49, 52);
        }


        if ($args['ent_user_type'] == '1') {
            return array('errNum' => $errMsgArr['errNum'], 'errFlag' => $errMsgArr['errFlag'], 'errMsg' => $errMsgArr['errMsg'],
                'tripsToday' => $driverRechargeData['tripsToday'],
                'earningsToday' => round($driverRechargeData['earningsToday'], 2),
                'wallet' => round($driverRechargeData['wallet'], 2),
                'balanceLimit' => ALLOW_DRIVER_UPTO,
                'lastTripTime' => $driverRechargeData['lastTripTime'],
                'lastTripPrice' => round($driverRechargeData['lastTripPrice'], 2),
                'carType' => $driverRechargeData['carType'],
                'data' => $appts, 't' => $selectAppntsQry, 'masStatus' => $masStatus['status']);
        } else{
            $ClientmapKey = And_ClientmapKey;
            $ClientPlaceKey = And_ClientPlaceKey;
            if($this->User['deviceType'] == 1){
                $ClientmapKey = Ios_ClientmapKey;
                $ClientPlaceKey = Ios_ClientPlaceKey;
                }
               
            return array('errNum' => $errMsgArr['errNum'], 'errFlag' => $errMsgArr['errFlag'], 'errMsg' => $errMsgArr['errMsg'], 'data' => $appts, 't' => $selectAppntsQry, 'masStatus' => $masStatus['status'],
                'ClientPlaceKey' => $ClientPlaceKey,
                'ClientmapKey' => $ClientmapKey,'presenseChn' => presenseChn ,'stipeKey' => stipeKeyForApp,'pub' => PUBNUB_PUBLISH_KEY, 'sub' => PUBNUB_SUBSCRIBE_KEY,'serverChn' => APP_PUBNUB_CHANNEL);
        }
    }

//    protected function getApptStatus($args) {
//
//       
//        if ($args['ent_date_time'] == '')
//            return $this->_getStatusMessage(1, 15);
//
//        $this->curr_date_time = urldecode($args['ent_date_time']);
//
//        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], $args['ent_user_type']);
//
//        if (is_array($returned))
//            return $returned;
//
//        $location = $this->mongo->selectCollection('location');
//
//
//
//        $queyrDrivervehicleInfo = "select (select count(appointment_id) from appointment where appointment_dt like '%" . date('Y-m-d') . "%' and status = 9 and mas_id = '" . $this->User['entityId'] . "') as tripsToday,"
//                . "(select IFNULL(sum(mas_earning),0) from appointment where appointment_dt like '%" . date('Y-m-d') . "%' and status = 9 and mas_id = '" . $this->User['entityId'] . "') as earningsToday,"
//                . "ROUND((ifnull((select sum(RechargeAmount) from DriverRecharge where  mas_id = '" . $this->User['entityId'] . "'),0) - ifnull((select sum(app_owner_pl) from appointment where status = 9  and mas_id = mas_id = '" . $this->User['entityId'] . "'),0)),2) as wallet";
//
//        $driverRechargeData = mysql_fetch_assoc(mysql_query($queyrDrivervehicleInfo, $this->db->conn));
//
//        $masStatus = $location->findOne(array('user' => (int) $this->User['entityId']));
//
////
//        if ($driverRechargeData['wallet'] < ALLOW_DRIVER_UPTO) {
//            $location->update(array('user' => (int) $this->User['entityId']), array('$set' => array('EnougMoney' => 0)));
//            $errorno = $this->_getStatusMessage(404, $pushNum);
//
//            return array('errNum' => $errorno['errNum'], 'errFlag' => $errorno['errFlag'], 'errMsg' => $errorno['errMsg'] . "$" . ALLOW_DRIVER_UPTO . " in your wallet.");
//        } else {
//            if($masStatus['EnougMoney'] == 0)
//             $location->update(array('user' => (int) $this->User['entityId']), array('$set' => array('EnougMoney' => 1)));
//        }
//        
//
//
//        if ($args['ent_appnt_dt'] != '') {
//
//            if ($args['ent_user_type'] == '1') {
//
//                $selectStatusQry = "select a.user_device,a.status,a.appointment_id,a.payment_status,a.payment_type,(select status from master where mas_id = '" . $this->User['entityId'] . "') as master_status from appointment a where a.mas_id = '" . $this->User['entityId'] . "' and a.appointment_dt = '" . $args['ent_appnt_dt'] . "' and a.status != '10'";
//            } else {
//                $selectStatusQry = "select a.user_device,a.payment_type,a.payment_status,a.appointment_dt,a.cancel_status,a.amount,a.address_line1,a.address_line2,a.drop_addr1,a.drop_addr2,a.duration,a.appt_lat,a.appt_long,a.drop_lat,a.arrive_dt,a.complete_dt,a.drop_long,a.appointment_id,a.status,a.mas_id,d.profile_pic,d.mobile,d.first_name,d.last_name,d.email,";
//                $selectStatusQry .= "(select report_msg from reports where appointment_id = a.appointment_id limit 0,1) as report_msg,(select License_Plate_No from workplace where workplace_id = a.car_id) as licencePlate,(select v.vehiclemodel from vehiclemodel v, workplace w where w.Vehicle_Model = v.id and w.workplace_id = a.car_id) as vehicle_model, ";
//                $selectStatusQry .= "(select wt.price_per_km from workplace_types wt,workplace w where w.type_id = wt.type_id and w.workplace_id = d.workplace_id) as price_per_km,(select mas_id from master_ratings where appointment_id = a.appointment_id) as rateStatus from appointment a,master d ";
//                $selectStatusQry .= " where a.mas_id = d.mas_id  and a.slave_id = '" . $this->User['entityId'] . "' and a.status != 10 and a.appointment_dt = '" . $args['ent_appnt_dt'] . "' order by appointment_id DESC";
//            }
//            $selectStatusRes = mysql_query($selectStatusQry, $this->db->conn);
//            $statArr = mysql_fetch_assoc($selectStatusRes);
//
//            if (is_array($statArr))
//                $errMsgArr = $this->_getStatusMessage(21, 52);
//            else
//                $errMsgArr = $this->_getStatusMessage(49, 52);
//
//            if ($args['ent_user_type'] == '2') {
//                $masterData = $location->findOne(array('user' => (int) $statArr['mas_id']));
//            }
//
//            if ($args['ent_user_type'] == '2') {
//                return array('errNum' => $errMsgArr['errNum'], 'errFlag' => $errMsgArr['errFlag'], 'errMsg' => $errMsgArr['errMsg'], 'payType' => $statArr['payment_type'], 'pasChn' => 'qp_' . $statArr['user_device'], 'plateNo' => $statArr['licencePlate'], 'model' => $statArr['vehicle_model'], 'chn' => $masterData['chn'], 'r' => round($masterData['rating'], 1), 'ltg' => $masterData['location']['latitude'] . ',' . $masterData['location']['longitude'], 'bid' => $statArr['appointment_id'], 'status' => $statArr['status'], 'bid' => $statArr['appointment_id'], 'fName' => $statArr['first_name'], 'lName' => $statArr['last_name'], 'mobile' => $statArr['mobile'], 'addr1' => urldecode($statArr['address_line1']), 'addr2' => urldecode($statArr['address_line2']), 'dropAddr1' => urldecode($statArr['drop_addr1']), 'dropAddr2' => urldecode($statArr['drop_addr2']), 'amount' => number_format($statArr['amount'], 2, '.', ''), 'pPic' => (($statArr['profile_pic'] === '') ? $this->default_profile_pic : $statArr['profile_pic']), 'dur' => $statArr['duration'], 'pickLat' => $statArr['appt_lat'], 'pickLong' => $statArr['appt_long'], 'dropLat' => $statArr['drop_lat'], 'dropLong' => $statArr['drop_long'], 'apptDt' => $statArr['appointment_dt'], 'pickupDt' => $statArr['arrive_dt'], 'dropDt' => $statArr['complete_dt'], 'email' => $statArr['email'], 'discount' => '0.00', 'rateStatus' => ($statArr['rateStatus'] === '') ? 1 : 2, 'payStatus' => ($statArr['payment_status'] == '') ? 0 : $statArr['payment_status'], 'reportMsg' => $statArr['report_msg'], 'share' => $this->get_tiny_url($this->share . $this->encrypt_decrypt('encrypt', $statArr['appointment_id']))); //,'t'=>$selectStatusQry);
//            } else {
//                return array('errNum' => $errMsgArr['errNum'], 'errFlag' => $errMsgArr['errFlag'], 'errMsg' => $errMsgArr['errMsg'],
//                    'tripsToday' => $driverRechargeData['tripsToday'],
//                    'earningsToday' => $driverRechargeData['earningsToday'],
//                    'wallet' => $driverRechargeData['wallet'],
//                    'payType' => $statArr['payment_type'], 'pasChn' => 'qp_' . $statArr['user_device'], 'nt' => ($statArr['appt_type'] == '1' ? '' : '51'), 'plateNo' => $statArr['licencePlate'], 'model' => $statArr['vehicle_model'], 'bid' => $statArr['appointment_id'], 'chn' => $masterData['chn'], 'r' => round($masterData['rating'], 1), 'ltg' => $masterData['location']['latitude'] . ',' . $masterData['location']['longitude'], 'status' => $statArr['status'], 'bid' => $statArr['appointment_id'], 'rateStatus' => ($statArr['rateStatus'] == '') ? 1 : 2, 'payStatus' => ($statArr['payment_status'] == '') ? 0 : 1, 'pPic' => (($statArr['profile_pic'] === '') ? $this->default_profile_pic : $statArr['profile_pic']), 'mobile' => $statArr['mobile'], 'email' => $statArr['email'], 'fName' => $statArr['first_name'], 'apptDt' => $statArr['appointment_dt'], 'masStatus' => $statArr['master_status'], 'share' => $this->get_tiny_url($this->share . $this->encrypt_decrypt('encrypt', $statArr['appointment_id']))); //,'t'=>$selectStatusQry);
//            }
//        }
//
//
//
//
//        if ($args['ent_user_type'] == '2') {
//            $selectAppntsQry = "select a.user_device,a.payment_type,a.payment_status,a.appointment_dt,a.cancel_status,a.amount,a.address_line1,a.address_line2,a.drop_addr1,a.drop_addr2,a.duration,a.appt_lat,a.appt_long,a.drop_lat,a.arrive_dt,a.complete_dt,a.drop_long,a.appointment_id,a.status,a.mas_id,d.profile_pic,d.mobile,d.first_name,d.last_name,d.email,";
////            $selectAppntsQry .= "(select mas_id from master_ratings where appointment_id = a.appointment_id) as reveiw_flag, ";
//            $selectAppntsQry .= "(select report_msg from reports where appointment_id = a.appointment_id limit 0,1) as report_msg,(select License_Plate_No from workplace where workplace_id = a.car_id) as licencePlate,(select v.vehiclemodel from vehiclemodel v, workplace w where w.Vehicle_Model = v.id and w.workplace_id = a.car_id) as vehicle_model, ";
//            $selectAppntsQry .= "(select wt.price_per_km from workplace_types wt,workplace w where w.type_id = wt.type_id and w.workplace_id = d.workplace_id) as price_per_km,(select mas_id from master_ratings where appointment_id = a.appointment_id limit 0,1) as rateStatus from appointment a,master d ";
//            $selectAppntsQry .= " where a.mas_id = d.mas_id  and a.slave_id = '" . $this->User['entityId'] . "' and a.status IN (6,7,8,9) order by appointment_id DESC limit 0,1";
//        } else {
//            $selectAppntsQry = "select a.appt_type,a.payment_type,a.user_device,a.payment_status,a.appointment_dt,a.amount,a.address_line1,a.address_line2,a.drop_addr1,a.drop_addr2,a.appointment_dt,a.duration,a.appt_lat,a.appt_long,a.appointment_id,a.drop_lat,a.drop_long,a.arrive_dt,a.complete_dt,a.status,s.profile_pic,s.phone as mobile,s.first_name,s.last_name,s.email,(select status from master where mas_id = '" . $this->User['entityId'] . "') as master_status ,";
//            $selectAppntsQry .= "(select report_msg from reports where appointment_id = a.appointment_id limit 0,1) as report_msg,";
//            $selectAppntsQry .= "(select wt.price_per_km from workplace_types wt,workplace w,master d where w.type_id = wt.type_id and w.workplace_id = d.workplace_id and d.mas_id = a.mas_id) as price_per_km ";
//            $selectAppntsQry .= " from appointment a,slave s  where a.slave_id = s.slave_id and a.mas_id = '" . $this->User['entityId'] . "' and a.status IN (1,6,7,8) ";
//        }
//
//        $selectStatusRes = mysql_query($selectAppntsQry, $this->db->conn);
//
//        $appts = array();
//
////        $location = $this->mongo->selectCollection('location');
////        $location->ensureIndex(array('user' => 1));
//
//        while ($apptData = mysql_fetch_assoc($selectStatusRes)) {
//
//            $masterData = array();
//
//            if ($args['ent_user_type'] == '2') {
//                $masterData = $location->findOne(array('user' => (int) $apptData['mas_id']));
//            }
//
//            if (($args['ent_user_type'] == '2' && $apptData['rateStatus'] == '') || $args['ent_user_type'] == '1')
//                $appts[] = array('payType' => $apptData['payment_type'], 'nt' => ($apptData['appt_type'] == '1' ? '' : '51'), 'pasChn' => 'qp_' . $apptData['user_device'], 'plateNo' => $apptData['licencePlate'], 'model' => $apptData['vehicle_model'], 'chn' => $masterData['chn'], 'r' => round($masterData['rating'], 1), 'ltg' => $masterData['location']['latitude'] . ',' . $masterData['location']['longitude'], 'bid' => $apptData['appointment_id'], 'status' => $apptData['status'], 'bid' => $apptData['appointment_id'], 'fName' => $apptData['first_name'], 'lName' => $apptData['last_name'], 'mobile' => $apptData['mobile'], 'addr1' => urldecode($apptData['address_line1']), 'addr2' => urldecode($apptData['address_line2']), 'dropAddr1' => urldecode($apptData['drop_addr1']), 'dropAddr2' => urldecode($apptData['drop_addr2']), 'amount' => number_format($apptData['amount'], 2, '.', ''), 'pPic' => (($apptData['profile_pic'] === '') ? $this->default_profile_pic : $apptData['profile_pic']), 'dur' => $apptData['duration'], 'pickLat' => $apptData['appt_lat'], 'pickLong' => $apptData['appt_long'], 'dropLat' => $apptData['drop_lat'], 'dropLong' => $apptData['drop_long'], 'apptDt' => $apptData['appointment_dt'], 'pickupDt' => $apptData['arrive_dt'], 'dropDt' => $apptData['complete_dt'], 'email' => $apptData['email'], 'discount' => '0.00', 'rateStatus' => ($apptData['rateStatus'] === '') ? 1 : 2, 'payStatus' => ($apptData['payment_status'] == '') ? 0 : $apptData['payment_status'], 'reportMsg' => $apptData['report_msg'], 'share' => $this->get_tiny_url($this->share . $this->encrypt_decrypt('encrypt', $apptData['appointment_id'])));
//        }
//
//        if (count($appts) > 0) {
//            $errMsgArr = $this->_getStatusMessage(21, 52);
//        } else {
//            $errMsgArr = $this->_getStatusMessage(49, 52);
//        }
//
//
//
//
//
//
//        return array('errNum' => $errMsgArr['errNum'], 'errFlag' => $errMsgArr['errFlag'], 'errMsg' => $errMsgArr['errMsg'],
//            'tripsToday' => $driverRechargeData['tripsToday'],
//            'earningsToday' => $driverRechargeData['earningsToday'],
//            'wallet' => $driverRechargeData['wallet'],
//            'data' => $appts, 't' => $selectAppntsQry, 'masStatus' => $this->User['status']);
//    }

    /*
     * Method name: updateMasterRating
     * Desc: Update master rating for slave
     * Input: Rating
     * Output:  gives error array if failed
     */

    protected function updateMasterRating($args) {

        if ($args['ent_date_time'] == '')
            return $this->_getStatusMessage(1, 'Date time');
        else if ($args['ent_rating'] == '')
            return $this->_getStatusMessage(1, 'Rating');
        else if ($args['ent_appnt_dt'] == '')
            return $this->_getStatusMessage(1, 'Booking date time');

        $this->curr_date_time = urldecode($args['ent_date_time']);

        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], '1');

        if (is_array($returned))
            return $returned;

        $slvDet = $this->_getEntityDet($args['ent_slv_email'], '2');

        $selectApptQry = "select appointment_id from appointment where slave_id = '" . $slvDet['slave_id'] . "' and mas_id = '" . $this->User['entityId'] . "' and appointment_dt = '" . $args['ent_appnt_dt'] . "'";
        $selectApptRes = mysql_query($selectApptQry, $this->db->conn);

        if (mysql_num_rows($selectApptRes) <= 0)
            return $this->_getStatusMessage(62, 62);

        $appt = mysql_fetch_assoc($selectApptRes);

        $updateReviewQry = "insert into passenger_rating(mas_id,slave_id,rating,status,rating_dt,appointment_id) values ('" . $this->User['entityId'] . "','" . $slvDet['slave_id'] . "','" . $args['ent_rating'] . "','1','" . $this->curr_date_time . "','" . $appt['appointment_id'] . "')";
        mysql_query($updateReviewQry, $this->db->conn);

        if (mysql_affected_rows() < 0)
            return $this->_getStatusMessage(70, $updateReviewQry);

        return $this->_getStatusMessage(69, $updateReviewQry);
    }

    /*
     * Method name: updateMasterRating
     * Desc: Update master rating for slave
     * Input: Rating
     * Output:  gives error array if failed
     */

    protected function getMasterStatus($args) {
        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], '1');

        if (is_array($returned))
            return $returned;

        $location = $this->mongo->selectCollection('location');

        $findOne = $location->findOne(array('user' => (int) $this->User['entityId']));

        $errMsgArr = $this->_getStatusMessage(114, 52);
        return array('errNum' => $errMsgArr['errNum'], 'errFlag' => $errMsgArr['errFlag'], 'errMsg' => $errMsgArr['errMsg'], 'status' => $findOne['status']);
    }

    /*
     * Method name: getFavourites
     * Desc: Get all favourite drivers
     * Input: Request data
     * Output:  Complete details if available, else error message
     */

    protected function getFavourites($args) {

        if ($args['ent_date_time'] == '')
            return $this->_getStatusMessage(1, 'Date time');
        else if ($args['ent_latitude'] == '' || $args['ent_longitude'] == '')
            return $this->_getStatusMessage(1, 'Location');

        $this->curr_date_time = urldecode($args['ent_date_time']);

        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], '2');

        if (is_array($returned))
            return $returned;

        $favourites = $this->mongo->selectCollection('favourite');

        $getCursor = $favourites->find(array('passenger' => (int) $this->User['entityId']));

        $driversArr = array();

        foreach ($getCursor as $fav) {
            $driversArr[] = $fav['driver'];
        }

        if (count($driversArr) <= 0)
            return $this->_getStatusMessage(95, 15);

        $resultArr = $this->mongo->selectCollection('$cmd')->findOne(array(
            'geoNear' => 'location',
            'near' => array(
                (double) $args['ent_longitude'], (double) $args['ent_latitude']
            ), 'spherical' => true, 'maxDistance' => 1, 'distanceMultiplier' => 6378137,
            'query' => array('user' => array('$in' => $driversArr)))
        );

        $md_arr = array();
//                    
        foreach ($resultArr['results'] as $res) {
            $doc = $res['obj'];
            $md_arr[] = array("name" => $doc["name"], 'lname' => $doc['lname'], "image" => $doc['image'], "rating" => (float) $doc['rating'],
                'email' => $doc['email'], 'lat' => $doc['location']['latitude'], 'lon' => $doc['location']['longitude'], 'dis' => number_format((float) $res['dis'] / $this->distanceMetersByUnits, 2, '.', ''));
        }

        if (count($md_arr) > 0)
            $errMsgArr = $this->_getStatusMessage(94, 52);
        else
            $errMsgArr = $this->_getStatusMessage(95, 52);

        return array('errNum' => $errMsgArr['errNum'], 'errFlag' => $errMsgArr['errFlag'], 'errMsg' => $errMsgArr['errMsg'], 'masters' => $md_arr);
    }

    /*
     * Method name: removeFavourites
     * Desc: Remove driver from favorites
     * Input: Request data
     * Output:  success if removed, else error message
     */

    protected function removeFavourites($args) {

        if ($args['ent_date_time'] == '')
            return $this->_getStatusMessage(1, 'Date time');
        else if ($args['ent_mas_email'] == '')
            return $this->_getStatusMessage(1, 'Master email');

        $this->curr_date_time = urldecode($args['ent_date_time']);

        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], '2');

        if (is_array($returned))
            return $returned;

        $location = $this->mongo->selectCollection('location');

        $masDet = $location->findOne(array('email' => $args['ent_mas_email']));

        $favorite = $this->mongo->selectCollection('favourite');
        $favorite->remove(array('driver' => (int) $masDet['user']));

        return $this->_getStatusMessage(96, 52);
    }

    /*
     * Method name: logout
     * Desc: Edit profile of any users
     * Input: Request data
     * Output:  Complete profile details if available, else error message
     */

    protected function logout($args) {

        if ($args['ent_user_type'] == '')
            return $this->_getStatusMessage(1, 'User type');
        else if ($args['ent_date_time'] == '')
            return $this->_getStatusMessage(1, 'Date time');

        $this->curr_date_time = urldecode($args['ent_date_time']);

        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], $args['ent_user_type'], '1');

        if (is_array($returned))
            return $returned;

        $logoutQry = "update user_sessions set loggedIn = '2' where oid = '" . $this->User['entityId'] . "' and sid = '" . $this->User['sid'] . "' and user_type = '" . $args['ent_user_type'] . "'";
        $logoutRes = mysql_query($logoutQry, $this->db->conn);

        if (mysql_affected_rows() > 0 || $logoutRes) {

            if ($args['ent_user_type'] == '1') {
                $updateWorkplaceIdQry = "update master set workplace_id = '' where mas_id = '" . $this->User['entityId'] . "'";
                mysql_query($updateWorkplaceIdQry, $this->db->conn);

                $updateWorkplaceQry = "update workplace set Status = 2,last_logout_lat = '" . $args['ent_lat'] . "',last_logout_long = '" . $args['ent_long'] . "' where workplace_id = '" . $this->User['workplaceId'] . "'";
                mysql_query($updateWorkplaceQry, $this->db->conn);

                $location = $this->mongo->selectCollection('location');

                $location->update(array('user' => (int) $this->User['entityId']), array('$set' => array('status' => 4, 'type' => 0, 'carId' => 0, 'chn' => '', 'listner' => '')));
            }
            return $this->_getStatusMessage(29, 55);
        } else {
            return $this->_getStatusMessage(3, $logoutQry);
        }
    }

    /*
     * Method name: support 
     * Desc: Edit profile of any users
     * Input: Request data
     * Output:  Complete profile details if available, else error message
     */

    protected function support($args) {
        $supportArray = array(
            array(
                "tag" => "Request a ride",
                "link" => "support/HowToUseUber/RequestingARide/",
                "childs" => array(
//                    array(
//                        "tag" => "Reservation",
//                        "link" => "support/HowToUseUber/RequestingARide/can_i_make_reserv.html",
//                    ),
//                    array(
//                        "tag" => "Request for morethan one",
//                        "link" => "support/HowToUseUber/RequestingARide/can_i_req_more_than_one.html",
//                    ),
                    array(
                        "tag" => "How to request ride",
                        "link" => "support/HowToUseUber/RequestingARide/howTorequest_aRide.html",
                    ),
                    array(
                        "tag" => "Contact driver",
                        "link" => "support/HowToUseUber/RequestingARide/contact_driver.html",
                    ),
                    array(
                        "tag" => "Cancel request",
                        "link" => "support/HowToUseUber/RequestingARide/cancelling_my_req.html",
                    ),
                    array(
                        "tag" => "Fare calculator",
                        "link" => "support/HowToUseUber/RequestingARide/getting_fare_estimate.html",
                    ),
                    array(
                        "tag" => "Share eta",
                        "link" => "support/HowToUseUber/RequestingARide/share_eta.html",
                    ),
                    array(
                        "tag" => "How to use",
                        "link" => "support/HowToUseUber/RequestingARide/how_to_use.html",
                    )
                ),
            ),
            array(
                "tag" => "Perfecting your pickup",
                "link" => "support/HowToUseUber/PerfectingYourPickup/",
                "childs" => array(
                    array(
                        "tag" => "Identifying driver",
                        "link" => "support/HowToUseUber/PerfectingYourPickup/identifying_your_driver.html",
                    ),
                    array(
                        "tag" => "Bringing along a pet",
                        "link" => "support/HowToUseUber/PerfectingYourPickup/bringing_along_a_pet.html",
                    ),
                    array(
                        "tag" => "Request ride for some one else",
                        "link" => "support/HowToUseUber/PerfectingYourPickup/requesting_a_ride_for_someone_else.html",
                    ),
                    array(
                        "tag" => "Cancel i request a specific driver",
                        "link" => "support/HowToUseUber/PerfectingYourPickup/can_i_req_a_specific_driver.html",
                    ),
                    array(
                        "tag" => "Cancel request",
                        "link" => "support/HowToUseUber/PerfectingYourPickup/cancelling_my_req.html",
                    ),
//                    array(
//                        "tag" => "Request at airport",
//                        "link" => "support/HowToUseUber/PerfectingYourPickup/requesting_at_the_airport.html",
//                    ),
//                    array(
//                        "tag" => "Why did my eta change",
//                        "link" => "support/HowToUseUber/PerfectingYourPickup/why_did_my_eta_change.html",
//                    )
                )
            ),
            array(
                "tag" => "Paying for your trip",
                "link" => "support/HowToUseUber/PayingForYourTrip/",
                "childs" => array(
                    array(
                        "tag" => "Choosing a payment type",
                        "link" => "support/HowToUseUber/PayingForYourTrip/choosing_a_payment_type.html",
                    ),
                    array(
                        "tag" => "Do i need to pay driver",
                        "link" => "support/HowToUseUber/PayingForYourTrip/do_i_need_to_my_driver.html",
                    ),
                    array(
                        "tag" => "Enabling disabling credit",
                        "link" => "support/HowToUseUber/PayingForYourTrip/enabling_or_disabling_credits.html",
                    ),
                    array(
                        "tag" => "Splitting fare",
                        "link" => "support/HowToUseUber/PayingForYourTrip/splitting_your_fare.html",
                    )
                ),
            ),
            array(
                "tag" => "Understanding your fare",
                "link" => "support/HowToUseUber/UnderstandingYourFare/",
                "childs" => array(
//                    array(
//                        "tag" => "I was charged a cleaning fee",
//                        "link" => "support/HowToUseUber/UnderstandingYourFare/i_was_charged_a_cleaning_fee.html",
//                    ),
//                    array(
//                        "tag" => "I was charged a saferide fee",
//                        "link" => "support/HowToUseUber/UnderstandingYourFare/i_was_charged_a_saferide_fee.html",
//                    ),
                    array(
                        "tag" => "I was charged a toll",
                        "link" => "support/HowToUseUber/UnderstandingYourFare/i_was_charged_a_toll.html",
                    )
                )
            ),
            array(
                "tag" => "Which cities app is available",
                "link" => "support/HowToUseUber/what_cities_is_uber_available.html",
                "childs" => array(
                )
            ),
            array(
                "tag" => "How old must be to use the app",
                "link" => "support/HowToUseUber/how_must_be_old_to_use_uber.html",
                "childs" => array(
                )
            ),
            array(
                "tag" => "Code of conduct",
                "link" => "support/HowToUseUber/CodeOfConduct/",
                "childs" => array(
                    array(
                        "tag" => "Introduction",
                        "link" => "support/HowToUseUber/CodeOfConduct/introduction.html",
                    ),
                    array(
                        "tag" => "Professionalism perfect",
                        "link" => "support/HowToUseUber/CodeOfConduct/professionalism_perfect.html",
                    ),
                    array(
                        "tag" => "Safety",
                        "link" => "support/HowToUseUber/CodeOfConduct/safety.html",
                    ),
                    array(
                        "tag" => "Emergencies",
                        "link" => "support/HowToUseUber/CodeOfConduct/emergencies.html",
                    )
                ),
            ),
            array(
                "tag" => "Understanding the app",
                "link" => "support/HowToUseUber/UsingTheApp/",
                "childs" => array(
                    array(
                        "tag" => "Downloading the app",
                        "link" => "support/HowToUseUber/UsingTheApp/downldng_the_app.html",
                    ),
                    array(
                        "tag" => "Understanding app permissions",
                        "link" => "support/HowToUseUber/UsingTheApp/understanding_app_permissions.html",
                    ),
                    array(
                        "tag" => "Using iOS unaccessibility features",
                        "link" => "support/HowToUseUber/UsingTheApp/uisng_ios_accessibilty_features.html",
                    ),
                    array(
                        "tag" => "User app on apple watch",
                        "link" => "support/HowToUseUber/UsingTheApp/using_uber_on_apple_watch.html",
                    ),
                    array(
                        "tag" => "Receiving 'Network error'",
                        "link" => "support/HowToUseUber/UsingTheApp/receiving_network_error.html",
                    ),
                    array(
                        "tag" => "Receiving issue with the app",
                        "link" => "support/HowToUseUber/UsingTheApp/report_an_issue_with_the_app.html",
                    ),
                    array(
                        "tag" => "Using app on iOS 6",
                        "link" => "support/HowToUseUber/UsingTheApp/using_uber_on_ios6.html",
                    )
                )
            ),
        );
        $errMsgArr = $this->_getStatusMessage(33, 2);
        return array('errNum' => $errMsgArr['errNum'], 'errFlag' => $errMsgArr['errFlag'], 'errMsg' => $errMsgArr['errMsg'], 'support' => $supportArray);
    }

    /*     * *********************************
      /*pendingrequest
     */

    protected function getPendingRequests($args) {

        if ($args['ent_date_time'] == '')
            return $this->_getStatusMessage(1, 'Date time');

        $this->curr_date_time = urldecode($args['ent_date_time']);

        $dateExploded = explode(' ', $this->curr_date_time);

        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], '1');

        if (is_array($returned))
            return $returned;

        $selectAppntsQry = "select p.profile_pic,p.first_name,p.phone,p.email,p.phone,a.appt_lat,a.appt_long,a.appointment_dt,a.appointment_id,a.extra_notes,a.address_line1,a.address_line2,a.drop_addr1,a.drop_addr2,a.drop_lat,a.drop_long,a.complete_dt,a.arrive_dt,a.status,a.payment_status,a.amount,a.distance_in_mts,a.appt_type from appointment a, slave p ";
        $selectAppntsQry .= " where p.slave_id = a.slave_id and a.mas_id = '" . $this->User['entityId'] . "' and a.status IN (1,2,6,7,8)  order by a.appointment_dt ASC"; // and a.appointment_dt >= '" . $curr_date_bfr_1hr . "'        a.status NOT in (1,3,4,7) and



        /*
          $selectAppntsQry = "select p.profile_pic,p.first_name,p.phone,p.email,p.phone,a.appt_lat,a.appt_long,a.appointment_dt,a.appointment_id,a.extra_notes,a.address_line1,a.address_line2,a.drop_addr1,a.drop_addr2,a.drop_lat,a.drop_long,a.complete_dt,a.arrive_dt,a.status,a.payment_status,a.amount,a.distance_in_mts,a.appt_type from appointment a, slave p ";
          $selectAppntsQry .= " where p.slave_id = a.slave_id and (" . $pendingString . " (a.mas_id = '" . $this->User['entityId'] . "' and a.status IN (2,6,7,8))) and DATE(a.appointment_dt) = '" . $dateExploded[0] . "' order by a.appointment_dt ASC"; // and a.appointment_dt >= '" . $curr_date_bfr_1hr . "'        a.status NOT in (1,3,4,7) and
         */

        $selectAppntsRes = mysql_query($selectAppntsQry, $this->db->conn);

        if (mysql_num_rows($selectAppntsRes) <= 0)
            return $this->_getStatusMessage(30, $selectAppntsQry);

        $appointments = $daysArr = array();


        while ($appnt = mysql_fetch_assoc($selectAppntsRes)) {

            if ($appnt['profile_pic'] == '')
                $appnt['profile_pic'] = $this->default_profile_pic;

            $durationSec = abs(strtotime($appnt['complete_dt']) - strtotime($appnt['start_dt']));

            $durationMin = round($durationSec / 60);

            if ($appnt['status'] == '1')
                $status = 'Booking requested';
            else if ($appnt['status'] == '2')
                $status = 'Driver accepted.';
//            else if ($appnt['status'] == '3')
//                $status = 'Driver rejected.';
//            else if ($appnt['status'] == '4')
//                $status = 'You cancelled.';
//            else if ($appnt['status'] == '5')
//                $status = 'Driver cancelled.';
//            else
            else if ($appnt['status'] == '6')
                $status = 'Driver is on the way.';
            else if ($appnt['status'] == '7')
                $status = 'Driver arrived.';
            else if ($appnt['status'] == '8')
                $status = 'Booking started.';
            else if ($appnt['status'] == '9')
                $status = 'Booking completed.';
//            else if ($appnt['status'] == '10')
//                $status = 'Booking expired.';
            else
                $status = 'Status unavailable.';

            $appointments[] = array('bid' => $appnt['appointment_id'], 'pPic' => $appnt['profile_pic'], 'email' => $appnt['email'], 'statCode' => $appnt['status'], 'status' => $status,
                'fname' => $appnt['first_name'], 'apntTime' => date('h:i a', strtotime($appnt['appointment_dt'])), 'apntDt' => $appnt['appointment_dt'], 'mobile' => $appnt['phone'],
                'addrLine1' => urldecode($appnt['address_line1']), 'payStatus' => ($appnt['payment_status'] == '') ? 0 : $appnt['payment_status'], 'apptLat' => $appnt['appt_lat'], 'apptLong' => $appnt['appt_long'],
                'dropLine1' => urldecode($appnt['drop_addr1']), 'duration' => $durationMin, 'distance' => round($appnt['distance_in_mts'] / $this->distanceMetersByUnits, 2), 'amount' => $appnt['amount']);
        }

        $errMsgArr = $this->_getStatusMessage(31, 2);
        return array('errNum' => $errMsgArr['errNum'], 'errFlag' => $errMsgArr['errFlag'], 'errMsg' => $errMsgArr['errMsg'], 'appointments' => $appointments); //,'test'=>$selectAppntsQry,'test1'=>$appointments);
    }

    /*     * *********************************



      /*
     * Method name: getWorkplaceTypes
     * Desc: Get workplace data
     * Input: nothing
     * Output:  gives workplace details if available else error msg
     */

    protected function getWorkplaceTypes($cityName = NULL, $lat = NULL, $long = NULL) {

        if ($lat != NULL && $long != NULL) {
            $typesData = array();
            $cond = array(
                'geoNear' => 'vehicleTypes',
                'near' => array(
                    (double) $long, (double) $lat
                ), 'spherical' => true, 'maxDistance' => 50000 / 6378137, 'distanceMultiplier' => 6378137);

            $resultArr1 = $this->mongo->selectCollection('$cmd')->findOne($cond);

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
                return $typesDataNew;
        }

        if ($cityName == NULL)
            $selectWkTypesQry = "select wt.type_id,wt.type_name,wt.max_size,wt.basefare,wt.min_fare,wt.price_per_min,wt.price_per_km,wt.type_desc from workplace_types wt";
        else
            $selectWkTypesQry = "select wt.type_id,wt.type_name,wt.max_size,wt.basefare,wt.min_fare,wt.price_per_min,wt.price_per_km,wt.type_desc from workplace_types wt,city c where wt.city_id = c.City_Id and c.City_Name = '" . $cityName . "'";

        $selectWkTypesRes = mysql_query($selectWkTypesQry, $this->db->conn);

        $reviewsArr = array();

        while ($review = mysql_fetch_assoc($selectWkTypesRes)) {
            $reviewsArr[] = $review;
        }

        return $reviewsArr;
    }

    protected function getTypes($args) {

        $getCompQry = "select company_id,companyname from company_info where Status = 3";
        $getCompRes = mysql_query($getCompQry, $this->db->conn);

        while ($type = mysql_fetch_assoc($getCompRes)) {
            $compList[] = $type;
        }
        $compList[] = array('company_id' => 0, 'companyname' => 'Other');
        $errMsgArr = $this->_getStatusMessage(21, 52);
        return array('errNum' => $errMsgArr['errNum'], 'errFlag' => $errMsgArr['errFlag'], 'errMsg' => $errMsgArr['errMsg'], 'types' => $compList);
    }

    /*
     * Method name: checkCity
     * Desc: Check the cars available in this area or not.
     * Input: Request data
     * Output:  image name if uploaded and status message according to the result
     */

    protected function checkCity($args) {

        if ($args['ent_city'] == '')
            return $this->_getStatusMessage(1, 'City');

        if (count($this->getWorkplaceTypes($args['ent_city'])) <= 0)
            return $this->_getStatusMessage(80, 80);
        else
            return $this->_getStatusMessage(81, 51);
    }

    /*
     * Method name: updateSession
     * Desc: Updates user session
     * Input: Request data
     * Output:  Complete profile details if available, else error message
     */

    protected function updateSession($args) {

        if ($args['ent_user_type'] == '')
            return $this->_getStatusMessage(1, 'User type');
        else if ($args['ent_date_time'] == '')
            return $this->_getStatusMessage(1, 'Date time');

        $this->curr_date_time = $args['ent_date_time'];

        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], $args['ent_user_type']);
//print_r($returned);

        if (!is_array($returned)) {
            return $this->_getStatusMessage(73, $this->testing);
        } else if ((is_array($returned) && $returned['errNum'] == 6)) {// || 
            $token_obj = new ManageToken($this->db);

            $updateArr = $token_obj->updateSessToken($this->User['entityId'], $args['ent_dev_id'], '0', $args['ent_user_type'], $args['ent_date_time']);

            $errMsgArr = $this->_getStatusMessage(89, 71);
            return array('errNum' => $errMsgArr['errNum'], 'errFlag' => $errMsgArr['errFlag'], 'errMsg' => $errMsgArr['errMsg'], 'token' => $updateArr['Token'], 'expiryLocal' => $updateArr['Expiry_local'], 'expiryGMT' => $updateArr['Expiry_GMT'], 'flag' => $updateArr['Flag'], 'status' => ($this->User['status'] == '') ? $returned['status'] : $this->User['status']); //, 't' => $updateArr);
        } else {
            return $this->_getStatusMessage(90, 72);
        }
    }

    /*
     * Method name: truncateDB
     * Desc: Uploads media to the server folder named "pics"
     * Input: Request data
     * Output:  image name if uploaded and status message according to the result
     */

    protected function _truncateDB($args) {

        $num = 0;

        $qry2 = "truncate table master";
        mysql_query($qry2, $this->db->conn);
        $num += mysql_affected_rows();

        $qry12 = "truncate table slave";
        mysql_query($qry12, $this->db->conn);
        $num += mysql_affected_rows();

        $qry32 = "truncate table coupons";
        mysql_query($qry32, $this->db->conn);
        $num += mysql_affected_rows();

        $qry33 = "truncate table coupon_usage";
        mysql_query($qry33, $this->db->conn);
        $num += mysql_affected_rows();

        $qry21 = "truncate table user_sessions";
        mysql_query($qry21, $this->db->conn);
        $num += mysql_affected_rows();

        $qry22 = "truncate table master_ratings";
        mysql_query($qry22, $this->db->conn);
        $num += mysql_affected_rows();

        $qry23 = "truncate table appointment";
        mysql_query($qry23, $this->db->conn);
        $num += mysql_affected_rows();

        $qry25 = "truncate table images";
        mysql_query($qry25, $this->db->conn);
        $num += mysql_affected_rows();

        $qry24 = "truncate table workplace";
        mysql_query($qry24, $this->db->conn);
        $num += mysql_affected_rows();

        $qry26 = "truncate table appointments_later";
        mysql_query($qry26, $this->db->conn);
        $num += mysql_affected_rows();

        $qry27 = "truncate table company_info";
        mysql_query($qry27, $this->db->conn);
        $num += mysql_affected_rows();

        $qry28 = "truncate table vehicledoc";
        mysql_query($qry28, $this->db->conn);
        $num += mysql_affected_rows();

        $qry29 = "truncate table docdetail";
        mysql_query($qry29, $this->db->conn);
        $num += mysql_affected_rows();

        $qry30 = "truncate table workplace_types";
        mysql_query($qry30, $this->db->conn);
        $num += mysql_affected_rows();


        $location = $this->mongo->selectCollection('location');
        $response = $location->drop();

        $pat = $this->mongo->selectCollection('pat');
        $response1 = $pat->drop();

        $notifications = $this->mongo->selectCollection('notifications');
        $response2 = $notifications->drop();

        $vTypes = $this->mongo->selectCollection('vehicleTypes');
        $response3 = $vTypes->drop();

        $location->ensureIndex(array("location" => "2d"));

        $cursor = $location->find();

        $data = array();

        foreach ($cursor as $doc) {
            $data[] = $doc;
        }

        $cursor1 = $pat->find();

        foreach ($cursor1 as $doc) {
            $data[] = $doc;
        }

        $dir = 'pics/';
        $leave_files = array('aa_default_profile_pic.gif');

        $image = 0;

        foreach (glob("$dir/*") as $file) {
            if (is_dir($file)) {
                foreach (glob("$dir/$file/*") as $file1) {
                    if (!in_array(basename($file1), $leave_files)) {
                        unlink($file);
                        $image++;
                    }
                }
            } else if (!in_array(basename($file), $leave_files)) {
                unlink($file);
                $image++;
            }
        }
        $dir = 'pics/mdpi/';
        $leave_files = array('aa_default_profile_pic.gif');

        $image = 0;

        foreach (glob("$dir/*") as $file) {
            if (is_dir($file)) {
                foreach (glob("$dir/$file/*") as $file1) {
                    if (!in_array(basename($file1), $leave_files)) {
                        unlink($file);
                        $image++;
                    }
                }
            } else if (!in_array(basename($file), $leave_files)) {
                unlink($file);
                $image++;
            }
        }
        $dir = 'pics/hdpi/';
        $leave_files = array('aa_default_profile_pic.gif');

        $image = 0;

        foreach (glob("$dir/*") as $file) {
            if (is_dir($file)) {
                foreach (glob("$dir/$file/*") as $file1) {
                    if (!in_array(basename($file1), $leave_files)) {
                        unlink($file);
                        $image++;
                    }
                }
            } else if (!in_array(basename($file), $leave_files)) {
                unlink($file);
                $image++;
            }
        }
        $dir = 'pics/xhdpi/';
        $leave_files = array('aa_default_profile_pic.gif');

        $image = 0;

        foreach (glob("$dir/*") as $file) {
            if (is_dir($file)) {
                foreach (glob("$dir/$file/*") as $file1) {
                    if (!in_array(basename($file1), $leave_files)) {
                        unlink($file);
                        $image++;
                    }
                }
            } else if (!in_array(basename($file), $leave_files)) {
                unlink($file);
                $image++;
            }
        }
        $dir = 'pics/xxhdpi/';
        $leave_files = array('aa_default_profile_pic.gif');

        $image = 0;

        foreach (glob("$dir/*") as $file) {
            if (is_dir($file)) {
                foreach (glob("$dir/$file/*") as $file1) {
                    if (!in_array(basename($file1), $leave_files)) {
                        unlink($file);
                        $image++;
                    }
                }
            } else if (!in_array(basename($file), $leave_files)) {
                unlink($file);
                $image++;
            }
        }


        $dir1 = 'invoice/';

        foreach (glob("$dir1/*") as $file) {
            if (is_dir($file)) {
                foreach (glob("$dir1/$file/*") as $file1) {
                    unlink($file);
                    $image++;
                }
            } else if (!in_array(basename($file), $leave_files)) {
                unlink($file);
                $image++;
            }
        }

        $dir2 = 'admin/pics/';

        foreach (glob("$dir2/*") as $file) {
            if (is_dir($file)) {
                foreach (glob("$dir2/$file/*") as $file1) {
                    unlink($file);
                    $image++;
                }
            } else if (!in_array(basename($file), $leave_files)) {
                unlink($file);
                $image++;
            }
        }

        $dir3 = 'admin/upload_images/';

        foreach (glob("$dir3/*") as $file) {
            if (is_dir($file)) {
                foreach (glob("$dir3/$file/*") as $file1) {
                    unlink($file);
                    $image++;
                }
            } else if (!in_array(basename($file), $leave_files)) {
                unlink($file);
                $image++;
            }
        }

        return array('mongodb' => $response . '--' . $response1 . '--' . $response2 . '--' . $response3, 'data' => $data, 'rows' => $num, 'images' => $image);
    }

    
    
    public function testmail(){
        
        $mail = new sendAMail(APP_SERVER_HOST);
        $mailArr = $mail->sendMasWelcomeMail('ashish@mobifyi.com', 'ashish');
        return array('test' => $mailArr);
    }
    
    public function testcode(){
//        $args['ent_coupon'] = "hi";
//        $this->User['entityId'] = 202;
//        $couponsColl = $this->mongo->selectCollection('coupons');
//        $cond = array('status' => 0, 'coupon_code' => $args['ent_coupon'], '$or' => array(array('coupon_type' => 3, 'user_id' => (string) $this->User['entityId'], 'status' => 0, 'expiry_date' => array('$gte' => time())), array('coupon_type' => 2, 'bookings.slave_id' => array('$ne' => (string) $this->User['entityId']),'expiry_date' => array('$gte' => time()))));
////return $cond;
//        $couponsCollOne = $couponsColl->findOne($cond);
//
//        if (!is_array($couponsCollOne)){
//            
//            $cond_Slave = array('status' => 0, 'coupon_code' => $args['ent_coupon'],'coupon_type' => 2, 'bookings.slave_id' => (string) $this->User['entityId']);
//            $couponsCollOne_ = $couponsColl->findOne($cond_Slave);
//             if (is_array($couponsCollOne_))
//             return $this->_getStatusMessage(128, 1);
//             
//             return $this->_getStatusMessage(116, 1);
//                    
//        }
//        return array('allslave' => $AllSlaveids,'bookings' => $couponsCollOne['bookings'],'condition' => $couponsCollOne);
        
        $args['ent_cityid'] = "2";
        $rediusAmount = 4;
          $RediousPrice = $this->mongo->selectCollection('RediousPrice');

            $appcommisioninpersentage = $RediousPrice->find(array('cityid' => $args['ent_cityid'], 'from_' => array('$lte' => (int) $rediusAmount)))->sort(array('from_' => -1))->limit(1);
//        return array('data' => 'test');

            foreach ($appcommisioninpersentage as $key) {
                $Commisiondata[] = $key;
            }



            $defautcommision = PAYMENT_APP_COMMISSION;

            if (!empty($Commisiondata)) {
                $defautcommision = $Commisiondata[0]['price'];
            }




            $appCommision = (float) (((float) $finalAmt) * ($defautcommision / 100)); //$apptDet['amount'] - $transferAmt;
            return array('commision' => $defautcommision,'commisiondata' => $Commisiondata);
        
    }
    
    protected function testMon($args) {

        $booking_data_livetrack = $this->mongo->selectCollection('booking_data_livetrack');
        $booking_route = $this->mongo->selectCollection('booking_route');
        $args = $booking_data_livetrack->findOne(array('_id' => new MongoId('574982805853f2ec18a0fe97')));
        $booking_route->update(array('bid' => 1090), array('$push' => array('app_route' => $args['args']['ent_app_jsonLatLong'])));

        return array('data' => $args['args']['ent_app_jsonLatLong'], 'test' => $args);
        exit();



        $location = $this->mongo->selectCollection('location');
        $types = $this->mongo->selectCollection('vehicleTypes');

//        $location->remove(array('type' => array('$in' => array(16, 17, 91, 92))), array('multiple' => 1));
//        $types->remove(array('type' => array('$in' => array(16, 17, 91, 92))), array('multiple' => 1));

        $arr = array();
        $arr[] = time();
        $arr[] = date('Y-m-d H:i:s', time());
//        $response = $location->drop();
//        
        $location->ensureIndex(array("location" => "2d"));

        $cursor1 = $types->find();
        $cursor2 = $location->find();

        foreach ($cursor1 as $doc) {
            $arr[] = $doc;
        }
        foreach ($cursor2 as $doc) {
            $arr[] = $doc;
        }

        return $arr;
    }

    protected function pushSent($args) {
        $notifications = $this->mongo->selectCollection('notifications');

        $cursor2 = $notifications->find();

        foreach ($cursor2 as $doc) {
            $arr[] = $doc;
        }
        $notifications->drop();
        return $arr;
    }

    protected function testMon1($args) {
        $location1 = $this->mongo->selectCollection('testTable');
        return $location1->drop();
    }

    /*             ----------------                 HELPER METHODS             ------------------             */

    protected function _createCoupon($couponsColl) {

        $coupOn = $this->_generateRandomString(7);

        $find = $couponsColl->findOne(array('coupon_code' => (string) $coupOn, 'user_type' => 1));

        if (is_array($find) || count($find) > 0) {
            return $this->_createCoupon($couponsColl);
        } else {
            return $coupOn;
        }
    }

    protected function _createCoupon1() {

        $coupOn = $this->_generateRandomString(7);
        $checkPrevCouponQry = "select id from coupons where coupon_code = '" . $coupOn . "' and coupon_type IN (1,3) and user_type = 1";

        $res = mysql_query($checkPrevCouponQry, $this->db->conn);

        if (mysql_num_rows($res) > 0) {
            return $this->_createCoupon();
        } else {
            return $coupOn;
        }
    }

    private function week_start_end_by_date($date, $format = 'Y-m-d') {

//Is $date timestamp or date?
        if (is_numeric($date) AND strlen($date) == 10) {
            $time = $date;
        } else {
            $time = strtotime($date);
        }

        $week['week'] = date('W', $time);
        $week['year'] = date('o', $time);
        $week['year_week'] = date('oW', $time);
        $first_day_of_week_timestamp = strtotime($week['year'] . "W" . str_pad($week['week'], 2, "0", STR_PAD_LEFT));
        $week['first_day_of_week'] = date($format, $first_day_of_week_timestamp);
        $week['first_day_of_week_timestamp'] = $first_day_of_week_timestamp;
        $last_day_of_week_timestamp = strtotime($week['first_day_of_week'] . " +6 days");
        $week['last_day_of_week'] = date($format, $last_day_of_week_timestamp);
        $week['last_day_of_week_timestamp'] = $last_day_of_week_timestamp;

        return $week;
    }

    /*
     * Method name: _getSlvApptStatus
     * Desc: Get user appointment booking status
     * Input: Slave id
     * Output:  status available
     */

    protected function _getSlvApptStatus($slave) {

        $getApptStatusQry = "select booking_status from slave where slave_id = '" . $slave . "'";
        $getApptStatusres = mysql_query($getApptStatusQry, $this->db->conn);
        return mysql_fetch_assoc($getApptStatusres);
    }

    /*
     * Method name: _updateSlvApptStatus
     * Desc: Update user appointment booking status
     * Input: Slave id and status
     * Output:  true if updated else false
     */

    protected function _updateSlvApptStatus($slave, $status) {

        $getApptStatusQry = "update slave set booking_status = '" . $status . "' where slave_id = '" . $slave . "'";
        mysql_query($getApptStatusQry, $this->db->conn);
        if (mysql_affected_rows() > 0)
            return 0;
        else
            return 1;
    }

    /*
     * Method name: _getDirectionsData
     * Desc: Get google directions data from and to latlongs
     * Input: Keys, form and to latlongs
     * Output:  gives directions details if available else error msg
     */

    protected function _getDirectionsData($from, $to, $key = NULL) {

        if (is_null($key))
            $index = 0;
        else
            $index = $key;

        $keys_all = array('AIzaSyAp_1Skip1qbBmuou068YulGux7SJQdlaw', 'AIzaSyDczTv9Cu9c0vPkLoZtyJuCYPYRzYcx738', 'AIzaSyBZtOXPwL4hmjyq2JqOsd0qrQ-Vv0JtCO4', 'AIzaSyDXdyLHngG-zGUPj7wBYRKefFwcv2wnk7g', 'AIzaSyCibRhPUiPw5kOZd-nxN4fgEODzPgcBAqg', 'AIzaSyB1Twhseoyz5Z6o5OcPZ-3FqFNxne2SnyQ', 'AIzaSyCgHxcZuDslVJNvWxLs8ge4syxLNbokA6c', 'AIzaSyDH-y04IGsMRfn4z9vBis4O4LVLusWYdMk', 'AIzaSyB1Twhseoyz5Z6o5OcPZ-3FqFNxne2SnyQ', 'AIzaSyBQ4dTEeJlU-neooM6aOz4HlqPKZKfyTOc'); //$this->dirKeys;

        $url = 'https://maps.googleapis.com/maps/api/directions/json?origin=' . $from['lat'] . ',' . $from['long'] . '&destination=' . $to['lat'] . ',' . $to['long'] . '&sensor=false&key=' . $keys_all[$index];

        $ch = curl_init();
// Disable SSL verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
// Will return the response, if false it print the response
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// Set the url
        curl_setopt($ch, CURLOPT_URL, $url);
// Execute
        $result = curl_exec($ch);

// echo $result->routes;
// Will dump a beauty json :3
        $arr = json_decode($result, true);

        if (!is_array($arr['routes'][0])) {

            $index++;

            $arr['key_arr'] = array('key' => $keys_all[$index], 'index' => $index, 'all' => $keys_all);

            if (count($keys_all) > $index)
                return $this->_getDirectionsData($from, $to, $index);
            else
                return $arr;
        }

        $arr['key_arr'] = array('key' => $keys_all[$index], 'index' => $index, 'all' => $keys_all);

        return $arr;
    }

    /*
     * Method name: _getMasterReviews
     * Desc: Get master reviews with pagination
     * Input: Master email, page number
     * Output:  gives review details if available else error msg
     */

    protected function _getMasterReviews($args) {

        $pageNum = (int) $args['ent_page'];
        $reviewsArr = array();

        if ($args['ent_page'] == '')
            $pageNum = 1;

        $lowerLimit = ($this->reviewsPageSize * $pageNum) - $this->reviewsPageSize;
        $upperLimit = $this->reviewsPageSize * $pageNum;

        $selectReviewsQry = "select rev.mas_id,rev.slave_id,rev.review_dt,rev.star_rating,rev.review,(select count(*) from master_ratings where mas_id = rev.mas_id) as total_rows,(select first_name from slave where slave_id = rev.slave_id) as slave_name,d.profile_pic from slave_ratings rev,master d where d.email = '" . $args['ent_dri_email'] . "' and d.mas_id = rev.mas_id and rev.status = 1 and rev.review != '' order by rev.star_rating DESC limit $lowerLimit,$upperLimit";
        $selectReviewsRes = mysql_query($selectReviewsQry, $this->db->conn);

        if (mysql_num_rows($selectReviewsRes) <= 0)
            return $this->_getStatusMessage(28, $selectReviewsQry);

        while ($review = mysql_fetch_assoc($selectReviewsRes)) {
            $reviewsArr[] = array('docPic' => $review['profile_pic'], 'rating' => $review['star_rating'], 'review' => $review['review'], 'by' => $review['slave_name'], 'dt' => $review['review_dt'], 'total' => $review['total_rows']);
        }
        return $reviewsArr;
    }

    /*
     * Method name: _validate_token
     * Desc: Authorizes the user with token provided
     * Input: Token
     * Output:  gives entity details if available else error msg
     */

    protected function _validate_token($ent_sess_token, $ent_dev_id, $user_type, $test = NULL) {

        if ($ent_sess_token == '') {
            return $this->_getStatusMessage(1, 'Session');
        } else if ($ent_dev_id == '') {
            return $this->_getStatusMessage(1, 'Device id');
        } else {

            $sessDetArr = $this->_getSessDetails($ent_sess_token, $ent_dev_id, $user_type, $test);
//            print_r($sessDetArr);
            if ($sessDetArr['flag'] == '0') {
                $this->_updateActiveDateTime($sessDetArr['entityId'], $user_type);
                $this->User = $sessDetArr;
            } else if ($sessDetArr['flag'] == '3') {
                return $this->_getStatusMessage($sessDetArr['errNum'], 999);
            } else if ($sessDetArr['flag'] == '1') {

                $updateSessionQry = "update user_sessions set loggedIn = 2 where oid = '" . $sessDetArr['entityId'] . "' and user_type = '" . $user_type . "'";
                mysql_query($updateSessionQry, $this->db->conn);

                if ($user_type == '1') {
                    $updateWorkplaceQry = "update workplace set Status = 2 where workplace_id = '" . $sessDetArr['workplaceId'] . "'";
                    mysql_query($updateWorkplaceQry, $this->db->conn);

                    $location = $this->mongo->selectCollection('location');
                    $location->update(array('user' => (int) $sessDetArr['entityId']), array('$set' => array('type' => 0, 'carId' => 0, 'status' => 4)));
                }
                $this->User = $sessDetArr;
                return $this->_getStatusMessage(6, 102);
            } else {
                
                return $this->_getStatusMessage(7, $sessDetArr);
            }
        }
    }

    /*
     * Method name: _checkEntityLogin
     * Desc: Checks the unique id with the authentication type
     * Input: Unique id and the auth type
     * Output:  entity details if true, else false
     */

    protected function _checkEntityLogin($id, $auth_type) {

        $checkFBIdQry = "select ent.Entity_Id as entId,edet.Profile_Pic_Url,ent.Create_Dt,ent.Status from entity ent,entity_details edet where ent.Entity_Id = edet.Entity_Id and ent.Unique_Identifier = '" . $id . "' and ent.authType = '" . $auth_type . "'";
        $checkFBIdRes = mysql_query($checkFBIdQry, $this->db->conn);

        if (mysql_num_rows($checkFBIdRes) == 1) {

            $userDet = mysql_fetch_assoc($checkFBIdRes);

            if ($userDet['Profile_Pic_Url'] == "")
                $userDet['Profile_Pic_Url'] = $this->default_profile_pic;

            return array('flag' => '1', 'entityId' => $userDet['entId'], 'profilePic' => $userDet['Profile_Pic_Url'], 'joined' => $userDet['Create_Dt'], 'status' => $userDet['Status'], 'test' => $checkFBIdQry);
        } else {

            return array('flag' => '0', 'test' => $checkFBIdQry);
        }
    }

    /*
     * Method name: _getDeviceTypeName
     * Desc: Returns device name using device type id
     * Input: Device type id
     * Output:  Array with Device type name if true, else false
     */

    protected function _getDeviceTypeName($devTypeId) {

        $getDeviceNameQry = "select name from dev_type where dev_id = '" . $devTypeId . "'";
        $devNameRes = mysql_query($getDeviceNameQry, $this->db->conn);
        if (mysql_num_rows($devNameRes) > 0) {

            $devNameArr = mysql_fetch_assoc($devNameRes);
            return array('flag' => true, 'name' => $devNameArr['name']);
        } else {

            return array('flag' => false);
        }
    }

    /*
     * Method name: _verifyEmail
     * Desc: Checks email for uniqueness
     * Input: Email id to be checked
     * Output:  true if available else false
     */

    protected function _verifyEmail($email, $field, $table) {

        $searchEmailQry = "select $field,status from $table where email = '" . $email . "'";
        $searchEmailRes = mysql_query($searchEmailQry, $this->db->conn);

        if (mysql_num_rows($searchEmailRes) > 0)
            return mysql_fetch_assoc($searchEmailRes);
        else
            return false;
    }

    /*
     * Method name: _getStatusMessage
     * Desc: Get details of an error from db
     * Input: Error number that need details
     * Output:  Returns an array with error details
     */

    protected function _getStatusMessage($errNo, $text) {

        $msg = new getErrorMsg($errNo, $this->db);

        if ($errNo == '1')
            $msg->errMsg = $text . " is missing.";

        return array('errNum' => $msg->errId, 'errFlag' => $msg->errFlag, 'errMsg' => $msg->errMsg, 'test' => $text);
    }

    /*
     * Method name: _getSessDetails
     * Desc: retrieves a session details
     * Input: Object Id, Token and user_type
     * Output: 1 for Success and 0 for Failure
     */

    protected function _getSessDetails($token, $device_id, $user_type, $test = NULL) {

        if ($user_type == '1')
            $getDetQry = "select  us.oid, us.expiry, us.device, us.type, us.loggedIn, us.sid,doc.first_name,doc.last_name,doc.profile_pic,doc.email,doc.stripe_id,doc.mobile,doc.workplace_id,doc.status from user_sessions us, master doc where us.oid = doc.mas_id and us.token = '" . $token . "' and us.device = '" . $device_id . "' and us.user_type = '" . $user_type . "'"; // and us.loggedIn = 1
        else if ($user_type == '2')
            $getDetQry = "select  us.oid, us.expiry, us.device, us.type, us.loggedIn, us.sid,pat.first_name,pat.last_name,pat.profile_pic,pat.email,pat.stripe_id,pat.phone,pat.status as mobile,pat.paypal_token from user_sessions us, slave pat where us.oid = pat.slave_id and us.token = '" . $token . "' and us.device = '" . $device_id . "' and us.user_type = '" . $user_type . "'"; // and us.loggedIn = 1
//echo $getDetQry;

        $getDetRes = mysql_query($getDetQry, $this->db->conn);

        if (mysql_num_rows($getDetRes) > 0) {

            $sessDet = mysql_fetch_assoc($getDetRes);
//print_r($sessDet);


            if ($test == NULL)
                if ($sessDet['status'] == '4')
                    return array('flag' => '3', 'errNum' => 94);
                else if ($sessDet['status'] == '2' && $user_type == '1')
                    return array('flag' => '3', 'errNum' => 10);



            if ($sessDet['loggedIn'] == '2')
                return array('flag' => '2', 'sectest' => $getDetQry);
            else if ($sessDet['loggedIn'] == '3')
                return array('flag' => '3', 'errNum' => 96);


//            return array('query' => $getDetQry);
            if ($sessDet['profile_pic'] == "")
                $sessDet['profile_pic'] = $this->default_profile_pic;

//            if ($sessDet['expiry'] > $this->curr_date_time)
            return array('flag' => '0', 'status' => $sessDet['status'], 'sid' => $sessDet['sid'], 'entityId' => $sessDet['oid'], 'deviceId' => $sessDet['device'], 'deviceType' => $sessDet['type'], 'firstName' => $sessDet['first_name'], 'last_name' => $sessDet['last_name'], 'pPic' => $sessDet['profile_pic'], 'email' => $sessDet['email'], 'stripe_id' => $sessDet['stripe_id'], 'mobile' => $sessDet['mobile'], 'workplaceId' => $sessDet['workplace_id'], 'paypal' => $sessDet['paypal_token']); // 'currLat' => $sessDet['Current_Lat'], 'currLong' => $sessDet['Current_Long'],
//            else
//                return array('flag' => '1', 'entityId' => $sessDet['oid'], 'workplaceId' => $sessDet['workplace_id']);
        } else {
            return array('flag' => '2', 'datatest' => $token);
        }
    }

    /*
     * Method name: _checkSession
     * Desc: Check a session details
     * Input: Object Id, Token and user_type
     * Output: returns array of updated session details or new session details
     */

    protected function _checkSession($args, $oid, $user_type, $device_name, $workplaceArr = NULL) {

        $deleteAllOtherSessionsQry = "update user_sessions set loggedIn = '3' where user_type = '" . $user_type . "' and loggedIn = '1' and ((oid = '" . $oid . "' and device != '" . $args['ent_dev_id'] . "') or (oid != '" . $oid . "' and device = '" . $args['ent_dev_id'] . "'))";
        mysql_query($deleteAllOtherSessionsQry, $this->db->conn);

        $token_obj = new ManageToken($this->db);

        if ($args['ent_device_type'] == '1') {
            $AmazonSns = new AwsPush();
            $resPush = $AmazonSns->createPlatformEndpoint($args['ent_push_token'], $user_type);
            if ($resPush === false)
                $args['ent_push_token'] = 123;
            else
                $args['ent_push_token'] = $resPush['EndpointArn'];
        }

        if ($user_type == '1') {
            $checkUserSessionQry = "select sid, token, expiry,device from user_sessions where oid = '" . $oid . "' and user_type = '" . $user_type . "' and loggedIn = '1'"; // and device != '" . $args['ent_dev_id'] . "'

            $checkUserSessionRes = mysql_query($checkUserSessionQry, $this->db->conn);

            $num = mysql_num_rows($checkUserSessionRes);

            $res = mysql_fetch_assoc($checkUserSessionRes);

            if ($num == 1 && $res['device'] == $args['ent_dev_id']) {
                return $token_obj->updateSessToken($oid, $args['ent_dev_id'], $args['ent_push_token'], $user_type, $this->curr_date_time);
            } else if ($num >= 1 && $res['device'] != $args['ent_dev_id']) {
                $deleteAllOtherSessionsQry = "update user_sessions set loggedIn = '2' where user_type = '" . $user_type . "' and oid = '" . $oid . "'";
                mysql_query($deleteAllOtherSessionsQry, $this->db->conn);

                if (is_array($workplaceArr)) {
                    $updateWorkplaceQry = "update workplace set Status = 2,last_logout_lat = '" . $workplaceArr['lat'] . "',last_logout_long = '" . $workplaceArr['lng'] . "' where workplace_id = '" . $workplaceArr['workplaceId'] . "'";
                    mysql_query($updateWorkplaceQry, $this->db->conn);
                }

                return $token_obj->createSessToken($oid, $device_name, $args['ent_dev_id'], $args['ent_push_token'], $user_type, $this->curr_date_time);
//                return $this->_getStatusMessage(13, 108);
            } else {
                return $token_obj->createSessToken($oid, $device_name, $args['ent_dev_id'], $args['ent_push_token'], $user_type, $this->curr_date_time);
            }
        } else {

            $checkUserSessionQry = "select sid, token, expiry from user_sessions where oid = '" . $oid . "' and device = '" . $args['ent_dev_id'] . "' and user_type = '" . $user_type . "'";
            $checkUserSessionRes = mysql_query($checkUserSessionQry, $this->db->conn);

            if (mysql_num_rows($checkUserSessionRes) == 1)
                return $token_obj->updateSessToken($oid, $args['ent_dev_id'], $args['ent_push_token'], $user_type, $this->curr_date_time);
            else
                return $token_obj->createSessToken($oid, $device_name, $args['ent_dev_id'], $args['ent_push_token'], $user_type, $this->curr_date_time);
        }
    }

    /*
     * Method name: _getEntityDet
     * Desc: Gives facebook id for entity id 
     * Input: Request data, entity_id
     * Output: entity details for success or error array
     */

    protected function _getEntityDet($eid, $userType) {

        if ($userType == '1')
            $getEntityDetQry = "select profile_pic,first_name,last_name,mas_id,workplace_id,stripe_id from master where email = '" . $eid . "'";
        else
            $getEntityDetQry = "select profile_pic,first_name,slave_id,last_name,email,stripe_id from slave where email = '" . $eid . "'";

        $getEntityDetRes = mysql_query($getEntityDetQry, $this->db->conn);

        if (mysql_num_rows($getEntityDetRes) > 0) {

            $det = mysql_fetch_assoc($getEntityDetRes);

            if ($det['profile_pic'] == '')
                $det['profile_pic'] = $this->default_profile_pic;

            return $det;
        } else {
            return false;
        }
    }

    /*
     * Method name: _sendPush
     * Desc: Divides the tokens according to device type and sends a push accordingly
     * Input: Request data, entity_id
     * Output: 1 - success, 0 - failure
     */

    protected function _sendPush($senderId, $recEntityArr, $message, $notifType, $sname, $datetime, $user_type, $aplContent, $andrContent, $user_device = NULL) {

        $entity_string = '';
        $aplTokenArr = array();
        $andiTokenArr = array();
        $return_arr = array();

        $notifications = $this->mongo->selectCollection('notifications');

        foreach ($recEntityArr as $entity) {

            $entity_string = $entity . ',';
        }

        $entity_comma = rtrim($entity_string, ',');
//echo '--'.$entity_comma.'--';

        $device_check = '';
        if ($user_device != NULL)
            $device_check = " and device = '" . $user_device . "'";

        $getUserDevTypeQry = "select distinct type,push_token from user_sessions where oid in (" . $entity_comma . ") and loggedIn = '1' and user_type = '" . $user_type . "' and LENGTH(push_token) > 63" . $device_check;
        $getUserDevTypeRes = mysql_query($getUserDevTypeQry, $this->db->conn);

        if (mysql_num_rows($getUserDevTypeRes) > 0) {

            while ($tokenArr = mysql_fetch_assoc($getUserDevTypeRes)) {

                if ($tokenArr['type'] == 1)
                    $aplTokenArr[] = $tokenArr['push_token'];
                else if ($tokenArr['type'] == 2)
                    $andiTokenArr[] = $tokenArr['push_token'];
            }

            $aplTokenArr = array_values(array_filter(array_unique($aplTokenArr)));
            $andiTokenArr = array_values(array_filter(array_unique($andiTokenArr)));
//            print_r($andiTokenArr);
            if (count($aplTokenArr) > 0)
                $aplResponse = $this->_sendApplePush($aplTokenArr, $aplContent, $user_type);

            if (count($andiTokenArr) > 0)
                $andiResponse = $this->_sendAndroidPush($andiTokenArr, $andrContent, $user_type);

            foreach ($recEntityArr as $entity) {

                $ins_arr = array('notif_type' => (int) $notifType, 'sender' => (int) $senderId, 'reciever' => (int) $entity, 'message' => $message, 'notif_dt' => $datetime, 'apl' => $aplTokenArr, 'andr' => $andiTokenArr); //'aplTokens' => $aplTokenArr, 'andiTokens' => $andiTokenArr, 'andiRes' => $andiResponse, 

                $notifications->insert($ins_arr);

                $newDocID = $ins_arr['_id'];

                $return_arr[] = array($entity => $newDocID);
            }

            $return_arr[] = $aplResponse;
            $return_arr[] = $aplTokenArr;
            $return_arr[] = $recEntityArr;

            if ($aplResponse['errorNo'] != '')
                $errNum = $aplResponse['errorNo'];
            else if ($andiResponse['errorNo'] != '')
                $errNum = $andiResponse['errorNo'];
            else
                $errNum = 46;

            return array('insEnt' => $return_arr, 'errNum' => $errNum, 'andiRes' => $andiResponse);
        } else {
            return array('insEnt' => $return_arr, 'errNum' => 45, 'andiRes' => $andiResponse); //means push not sent
        }
    }

    protected function _sendApplePush($tokenArr, $aplContent, $user_type) {

        if (IOS_PUSH_TYPE === 'PUSHWOOSH') {

            $pushwoosh = new PushWoosh();

            $title = $aplContent['alert'];

            unset($aplContent['alert']);

            if ($user_type == '1')
                $pushReturn = $pushwoosh->pushDriver($title, $aplContent, $tokenArr);
            else
                $pushReturn = $pushwoosh->pushPassenger($title, $aplContent, $tokenArr);

            if ($pushReturn['info']['http_code'] == 200)
                return array('errorNo' => 44, 't' => $aplContent, 'tok' => $tokenArr, 'ret' => $pushReturn);
            else
                return array('errorNo' => 46);
        } else if (IOS_PUSH_TYPE === 'AMAZON') {
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
        }

        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', $this->ios_cert_path);
        stream_context_set_option($ctx, 'ssl', 'passphrase', $this->ios_cert_pwd);
        $apns_fp = stream_socket_client($this->ios_cert_server, $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);

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

    protected function _sendAndroidPush($tokenArr, $andrContent, $user_type) {

        $fields = array(
            'registration_ids' => $tokenArr,
            'data' => $andrContent,
        );

        if ($user_type == '1')
            $apiKey = ANDROID_DRIVER_PUSH_KEY;
        else
            $apiKey = ANDROID_PASSENGER_PUSH_KEY;

        $headers = array(
            'Authorization: key=' . $apiKey,
            'Content-Type: application/json'
        );
// Open connection
        $ch = curl_init();

// Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $this->androidUrl);

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
            return array('errorNo' => 44, 'result' => $tokenArr, 'key' => $apiKey);
        else
            return array('errorNo' => 46, 'result' => $tokenArr);
    }

    public function android_push($args) {


        define('API_ACCESS_KEY_ONE', $args['ent_severkey']);
        $registrationIds = array($args['ent_push_token']);
// prep the bundle
        $msg = array
            (
            'message' => $args['ent_msg']
        );
        $fields = array
            (
            'registration_ids' => $registrationIds,
            'data' => $msg
        );

        $headers = array
            (
            'Authorization: key=AIzaSyD_KxaOHjQLsBReYf6EmeWlxOsVevJESJw',
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->androidUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);
        echo $result;
    }

    /*
     * method name: generateRandomString
     * Desc: Generates a random string according to the length of the characters passed
     * Input: length of the string
     * Output: Random string
     */

    protected function _generateRandomString($length) {

        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';

        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $randomString;
    }

    protected function _updateActiveDateTime($entId, $user_type) {

        if ($this->curr_date_time == '')
            return true;

        if ($user_type == '1')
            $updateQry = "update master set last_active_dt = '" . $this->curr_date_time . "' where mas_id = '" . $entId . "'";
        else if ($user_type == '2')
            $updateQry = "update slave set last_active_dt = '" . $this->curr_date_time . "' where slave_id = '" . $entId . "'";

        mysql_query($updateQry, $this->db->conn);

        if (mysql_affected_rows() > 0)
            return true;
        else
            return false;
    }

    /*
     * Method name: _check_zip
     * Desc: Authorizes the user with token provided
     * Input: Zipcode
     * Output:  gives entity details if available else error msg
     */

    protected function _check_zip($zip_code) {

        $selectZipQry = "select zipcode from zipcodes where zipcode = '" . $zip_code . "'";
        $selectZipRes = mysql_query($selectZipQry, $this->db->conn);
        if (mysql_num_rows($selectZipRes) > 0)
            return true;
        else
            return false;
    }
    
     protected function updateLanguage($args) {

        if ($args['ent_lang'] == '' || $args['ent_user_type'] == '')
            return $this->_getStatusMessage(1, 15);


        $returned = $this->_validate_token($args['ent_sess_token'], $args['ent_dev_id'], $args['ent_user_type']);

        if (is_array($returned))
            return $returned;


        if ($args['ent_user_type'] == '1') {
            $table = "master";
            $id = "mas_id";
        } else {
            $table = "slave";
            $id = "slave_id";
        }
        $getApptStatusQry = "update $table set lang = '" . $args['ent_lang'] . "' where $id = '" . $this->User['entityId'] . "'";
        mysql_query($getApptStatusQry, $this->db->conn);

        if (mysql_affected_rows() <= 0)
            $cardRes = $this->_getStatusMessage(3, $getApptStatusQry);

        return $this->_getStatusMessage(23, 15);
    }
    

    protected function testUpdateLoc($args) {

        $location = $this->mongo->selectCollection('location');

        if ($args['ent_lat'] != '' && $args['ent_long'] != '')
            $setArr['location'] = array('longitude' => (float) $args['ent_long'], 'latitude' => (float) $args['ent_lat']);

        if ($args['ent_status'] != '')
            $setArr['status'] = (int) $args['ent_status'];

        $data = $location->findOne(array("user" => (int) $args['ent_doc']));

        $setArr['email'] = strtolower($data['email']);

        $newdata1 = array('$set' => $setArr);

        $location->update(array("user" => (int) $args['ent_doc']), $newdata1);

        $cursor2 = $location->find(array("user" => (int) $args['ent_doc']));

        foreach ($cursor2 as $doc) {
            var_dump($doc);
        }
    }

    public function logoutAllDrivers($args) {
        $insertAppointmentQry = "delete from user_sessions where user_type = 1";

        mysql_query($insertAppointmentQry, $this->db->conn);

        $location = $this->mongo->selectCollection('location');

        $masterDet = $location->update(array(), array('$set' => array('status' => 4)), array('multi' => 1));

        return array('message' => $masterDet);
    }

    public function checkAvlblCarsInCompany($args) {
        $getCarsQry = "select w.workplace_id,(select type_name from workplace_types where type_id = w.type_id) as type_name from workplace w where w.company = '" . $args['ent_comp_id'] . "' and w.status IN (2,3)";
        $getCarsRes = mysql_query($getCarsQry, $this->db->conn);

        $cars = array();

        while ($car = mysql_fetch_assoc($getCarsRes)) {
            $cars[] = array('carId' => $car['workplace_id'], 'type' => $car['type_name']);
        }
        return array('available_car_ids' => $cars);
    }

    protected function testPushWoosh($args) {
        return $this->_sendApplePush(array("arn:aws:sns:ap-southeast-1:972821710966:endpoint/APNS/CabRyder-Driver/9c3bae79-174f-3cb9-ab0e-5b629a5097ff"), array('alert' => 'test message from chetan', 't' => 1, 'sound' => 'default'), $args['type']);
    }

    protected function removePax($args) {

        $verifyEmail = $this->_verifyEmail($args['ent_token'], 'slave_id', 'slave'); //_verifyEmail($email,$field,$table);

        if (!is_array($verifyEmail))
            return $this->_getStatusMessage(20, $verifyEmail); //_getStatusMessage($errNo, $test_num);

        $email = $this->_loopUser($args, 'slave_id', 'slave', $verifyEmail['slave_id']);

        $updateUser = "update slave set email = '" . $email . "',phone = '' where email = '" . $args['ent_token'] . "'";
        mysql_query($updateUser, $this->db->conn);
        if (mysql_affected_rows() > 0) {
            $removeSession = "update user_sessions set loggedIn = 2 where oid = '" . $verifyEmail['slave_id'] . "' and user_type = 2 and loggedIn != 2";
            mysql_query($removeSession, $this->db->conn);
            return array('message' => 'user removed');
        } else {
            return array('message' => 'Failed');
        }
    }

    protected function removeDriver($args) {
        $verifyEmail = $this->_verifyEmail($args['ent_token'], 'mas_id', 'master'); //_verifyEmail($email,$field,$table);

        if (!is_array($verifyEmail))
            return $this->_getStatusMessage(20, 2); //_getStatusMessage($errNo, $test_num);

        $email = $this->_loopUser($args, 'slave_id', 'slave', $verifyEmail['slave_id']);

        $location = $this->db->mongo->selectCollection('location');
        $masDet = $location->findOne(array('user' => (int) $verifyEmail['mas_id']));

        $updateUser = "update master set email = '" . $email . "',workplace_id = '',mobile = '' where email = '" . $args['ent_token'] . "'";
        mysql_query($updateUser, $this->db->conn);
        if (mysql_affected_rows() > 0) {

            $location->update(array('user' => (int) $verifyEmail['mas_id']), array('email' => $email, 'status' => 4, 'carId' => 0, 'type' => 0));

            $removeSession = "update user_sessions set loggedIn = 2 where oid = '" . $verifyEmail['mas_id'] . "' and user_type = 1 and loggedIn != 2";
            mysql_query($removeSession, $this->db->conn);

            $removeSession = "update workplace set Status = 2 where workplace_id = '" . $masDet['workplace_id'] . "'";
            mysql_query($removeSession, $this->db->conn);

            return array('message' => 'user removed');
        } else {
            return array('message' => 'Failed');
        }
    }

    protected function _loopUser($args, $id, $table) {

        $rand = rand(111, 999) * rand(111, 999) . '-' . $args['ent_token'];

        $verifyEmail1 = $this->_verifyEmail($rand, $id, $table); //_verifyEmail($email,$field,$table);

        if (!is_array($verifyEmail1)) {
            return $rand;
        } else {
            return $this->_loopUser($args, $id, $table);
        }
    }

}

if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {

    $_SERVER['HTTP_ORIGIN'] = $_SERVER['SERVER_NAME'];
}

try {

//    echo json_encode(array('errMsg' => 'Server is under maintainance, will get back in few minutes..!', 'errNum' => 999, 'errFlag' => 1));
//    return false;

    $API = new MyAPI($_SERVER['REQUEST_URI'], $_REQUEST, $_SERVER['HTTP_ORIGIN']);

    echo $API->processAPI();
} catch (Exception $e) {

    echo json_encode(Array('error' => $e->getMessage()));
}
