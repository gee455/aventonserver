<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AwsPush
 *
 * @author admin.3embed
 */

if (!defined('PHPEXCEL_ROOT')) {
    define('PHPEXCEL_ROOT', dirname(__FILE__) . '/');
    require(PHPEXCEL_ROOT . 'AWS.php');
}



use Aws\Common\Aws;

class AwsPush {

    //put your code here
    private $obj = null;
    // ManagementConsole(APNS_SANDBOX)
    private $driverPlatformApplicationArn = "";
    private $psngrPlatformApplicationArn = "";
    
    
     private $AMAZON_AWS_AUTH_SECRET;
      private $AMAZON_AWS_ACCESS_KEY;
      private $AMAZON_AWS_SNS_REGION;
    
    public function __construct($AMAZON_DRIVER_APPLICATION_ARN,$AMAZON_PASSENGER_APPLICATION_ARN,$AMAZON_AWS_ACCESS_KEY,$AMAZON_AWS_AUTH_SECRET,$AMAZON_AWS_SNS_REGION){
        echo 'ram';
//     $this->driverPlatformApplicationArn = $AMAZON_DRIVER_APPLICATION_ARN;
//     $this->psngrPlatformApplicationArn  = $AMAZON_PASSENGER_APPLICATION_ARN;
//      $this->AMAZON_AWS_ACCESS_KEY = $AMAZON_AWS_ACCESS_KEY;
//      $this->AMAZON_AWS_AUTH_SECRET = $AMAZON_AWS_AUTH_SECRET;
//      $this->AMAZON_AWS_SNS_REGION = $AMAZON_AWS_SNS_REGION;
    }

    /**
     * AWS SDK for PHP
     */
    private function getInstance() {
        if (is_null($this->obj)) {
            $this->obj = Aws::factory(array(
                        'key' => $this->AMAZON_AWS_ACCESS_KEY,
                        'secret' => $this->AMAZON_AWS_AUTH_SECRET,
                        'region' => $this->AMAZON_AWS_SNS_REGION
                    ))->get('sns');
        }
        return $this->obj;
    }

    /**
     * Push(EndpointArn)
     */
    public function createPlatformEndpoint($token, $userType) {
        if ($userType == '1')
            $options = array(
                'PlatformApplicationArn' => $this->driverPlatformApplicationArn,
                'Token' => $token,
            );
        else
            $options = array(
                'PlatformApplicationArn' => $this->psngrPlatformApplicationArn,
                'Token' => $token,
            );

        try {
            $res = $this->getInstance()->createPlatformEndpoint($options);
        } catch (Exception $e) {
//            echo $e->getMessage();
            return false;
        }
        return $res; // $res['EndpointArn']
    }

    /**
     * 
     */
    public function publish($message, $EndpointArn) {
        try {
            $res = $this->getInstance()->publish(array(
                'Message' => $message,
                'TargetArn' => $EndpointArn
            ));
        } catch (Exception $e) {
//          echo $e->getMessage();
            return false;
        }
        return $res;
    }

    /**
     * (JSON)
     */
    public function publishJson($args) {
        try {
            $res = $this->getInstance()->publish($args);
        } catch (Exception $e) {
//          echo $e->getMessage();
            return false;
        }
        return $res;
    }

}

