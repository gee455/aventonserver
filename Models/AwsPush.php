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

use Aws\Common\Aws;

class AwsPush {

    //put your code here
    private $obj = null;
    // ManagementConsole(APNS_SANDBOX)
    private $driverPlatformApplicationArn = AMAZON_DRIVER_APPLICATION_ARN;
    private $psngrPlatformApplicationArn = AMAZON_PASSENGER_APPLICATION_ARN;

    /**
     * AWS SDK for PHP
     */
    private function getInstance() {
        if (is_null($this->obj)) {
            $this->obj = Aws::factory(array(
                        'key' => AMAZON_AWS_ACCESS_KEY,
                        'secret' => AMAZON_AWS_AUTH_SECRET,
                        'region' => AMAZON_AWS_SNS_REGION
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

