<?php  if(!defined('BASEPATH')) exit('No direct script access allowed');


require_once 'aws.phar';
include  $_SERVER['DOCUMENT_ROOT'].dirname(__FILE__).'/AwsPush.php';

        
class PushNotifications {
    
    public $_ci;
    
    public $androidDriverserverKE;
    public $androidPassangerserverKEy;

    public $pushtocken = array();
    public $amazon;
    public function __construct(){
     
    
     
        //get instance of CI class
        if (function_exists('get_instance'))
        {
            $this->_ci = get_instance();
        }
        else
        {
            $this->_ci = NULL;
        }
        $config_data = $this->_ci->config;//->item($config);

        
        $_data[] = $config_data['config'];
            
        
       echo 'here<pre>'; 
       print_r($_data);
        $this->androidDriverserverKE = $config_data['ANDROID_DRIVER_PUSH_KEY'];
        $this->androidPassangerserverKEy = $config_data['ANDROID_PASSENGER_PUSH_KEY'];
        
        $this->amazon = new AwsPush($config_data['ANDROID_DRIVER_PUSH_KEY'],
                $config_data['ANDROID_PASSENGER_PUSH_KEY'],
                $config_data['AMAZON_AWS_ACCESS_KEY'],
                $config_data['AMAZON_AWS_AUTH_SECRET'],
                $config_data['AMAZON_AWS_SNS_REGION']);
        echo 'here';
        
    }
    
    public function sendPush($pushtoken =  array(),$deviceType = "",$usertype = "",$message = ""){
        return $message;
        if($deviceType == 2){ // devtype 2 android and 1 ios
            if($usertype == 1){ // usertype 1 driver
                $pushKey =$this->androidDriverserverKE;  
            }else{ // usertype  2 passanger
                $pushKey =$this->androidPassangerserverKEy; 
            }
           $return =  $this->androidpush($pushtoken,$message,$pushKey);
           return $return;
          }
          else if($deviceType == 1){
              $return  = $this->sendIOSPush($pushtoken,$message); 
              return $return;
          }
    }
    
    public function androidpush($tokenArr,$andrContent,$apiKey) {
   
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
     
     public function sendIOSPush($pushtocken = array(),$message = ''){
         
         foreach($pushtocken as $push_token){
               $amazon->publishJson(array(
                            'MessageStructure' => 'json',
                            'TargetArn' => $push_token,
                            'Message' => json_encode(array(
                                'APNS' => json_encode(array(
                                    'aps' => array('alert' => $message)
                                ))
                            )),
                        ));
                if ($pushReturn2[0]['MessageId'] == '')
                            $ret[] = array('errorNo' => 44);
                        else
                            $ret[] = array('errorNo' => 46);
             }
            return $ret;
                        
     }


}
