<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PushWoosh
 *
 * @author embed
 */
class PushWoosh {

//put your code here

    public function pushPassenger($title, $append_array, $device_tokens = NULL) {

        $sound = $append_array['sound'];

        unset($append_array['sound']);

        if (is_array($device_tokens))
            $data = [
                'application' => "B5AFA-39A25",
                'auth' => "rns3VxLMKpxT1ra44FrcEsLvmw975pYb04uTrnDe0O31es6fLyWXSA08YqxIF5fV9TljwxFszGeu1tG0ThXh",
                'notifications' => [
                    [
                        'send_date' => 'now',
                        'content' => $title,
                        'data' => $append_array,
                        'devices' => $device_tokens,
                        'link' => '',
                        'ios_sound' => $sound
                    ]
                ]
            ];
//        else
//            $data = [
//                'application' => "B5AFA-39A25",
//                'auth' => "rns3VxLMKpxT1ra44FrcEsLvmw975pYb04uTrnDe0O31es6fLyWXSA08YqxIF5fV9TljwxFszGeu1tG0ThXh",
//                'notifications' => [
//                    [
//                        'send_date' => 'now',
//                        'content' => $title,
//                        'data' => $append_array,
//                        'link' => ''
//                    ]
//                ]
//            ];


        $url = 'https://cp.pushwoosh.com/json/1.3/' . 'createMessage';
        $request = json_encode(['request' => $data]);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);

        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        return array('response' => $response, 'info' => $info);
    }

    public function pushDriver($title, $append_array, $device_tokens = NULL) {

        $sound = $append_array['sound'];

        unset($append_array['sound']);

        if (is_array($device_tokens))
            $data = [
                'application' => "09CE4-7BA06",
                'auth' => "rns3VxLMKpxT1ra44FrcEsLvmw975pYb04uTrnDe0O31es6fLyWXSA08YqxIF5fV9TljwxFszGeu1tG0ThXh",
                'notifications' => [
                    [
                        'send_date' => 'now',
                        'content' => $title,
                        'data' => $append_array,
                        'devices' => $device_tokens,
                        'link' => '',
                        'ios_sound' => $sound
                    ]
                ]
            ];
//        else
//            $data = [
//                'application' => "09CE4-7BA06",
//                'auth' => "rns3VxLMKpxT1ra44FrcEsLvmw975pYb04uTrnDe0O31es6fLyWXSA08YqxIF5fV9TljwxFszGeu1tG0ThXh",
//                'notifications' => [
//                    [
//                        'send_date' => 'now',
//                        'content' => $title,
//                        'data' => $append_array,
//                        'link' => ''
//                    ]
//                ]
//            ];


        $url = 'https://cp.pushwoosh.com/json/1.3/' . 'createMessage';
        $request = json_encode(['request' => $data]);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);

        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        return array('response' => $response, 'info' => $info);
    }

}

//echo 1;
//$pushwoosh = new PushWoosh('YEFONQwpBelpdqzX3urIj1vY1j2cDyKo5EmOdCk5L4XexNIcfxsGeQWd4XQT5orasB8ikSAiReVL8DduOkew', '9082A-0F7D8');
//echo 1;
//print_r($pushwoosh->push("Test message from chetan", array('id' => 1), explode(',', 'hiuhIDSAHiuhui')));