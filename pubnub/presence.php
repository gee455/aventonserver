<?php

error_reporting(1);

require('Pubnub.php');

$pubnub = new Pubnub('pub-c-1661abbb-4282-48da-8cbd-03714d515f44', 'sub-c-da0bfb8e-7c72-11e5-a643-02ee2ddab7fe');

$pubnub->subscribe(array(
    "channel" => 'presence_channel',
    "callback" => function($message) {

        print_r($message);

        $keepListening = true;
        return $keepListening;
    }
));
?>
