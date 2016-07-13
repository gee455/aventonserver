<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Dispatch extends CI_Controller {


    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model("dispatchmodal");
        $this->load->library('session');
        error_reporting(0);

        header("cache-Control: no-store, no-cache, must-revalidate");
        header("cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

    }

    public function index($loginerrormsg = NULL) {

        if($this->dispatchmodal->issessionset()) {
            redirect(base_url() . "index.php/dispatch/dispather_bookingsControllers");
            }
        else{
            $data['loginerrormsg'] = $loginerrormsg;
            $this->load->view('dispatch/login', $data);
        }

    }


    public function AuthenticateUser() {
        $email = $this->input->post("email");
        $password = $this->input->post("password");
        if ($email && $password) {


            $status = $this->dispatchmodal->ValidateSuperAdmin();

            if ($status) {
                redirect(base_url() . "index.php/dispatch/dispather_bookingsControllers");
            } else {
                $loginerrormsg = "invalid email or password";
                $this->index($loginerrormsg);
            }
        }
        else
            redirect(base_url() . "index.php/dispatch");
    }


    public  function Dashbord(){

        $data['aap_booking'] = $this->dispatchmodal->get_app_bookings();
        $data['drivers'] = $this->dispatchmodal->get_all_drivers();
        $data['customers'] = $this->dispatchmodal->get_all_customers();
        $data['pagename'] = 'dispatch/dashbord';
        $this->load->view('dispatch', $data);

    }


    public function CustomerController(){

        $data['customers'] = $this->dispatchmodal->get_all_customers();
        $data['pagename'] = 'dispatch/Customers';
        $this->load->view('dispatch', $data);

    }
    public  function phonebooking_controller(){

        if($this->dispatchmodal->issessionset()) {
            $data['aap_booking'] = $this->dispatchmodal->get_app_bookings();
            $data['pagename'] = 'dispatch/phonebookigs';
            $this->load->view('dispatch', $data);
        }
        else{

            redirect(base_url() . "index.php/dispatch/Logout");

        }

    }


    public  function get_vehicle_type(){


        $this->load->library('mongo_db');

        $mongo = $this->mongo_db->db;
        $typesData = array();
        $cond = array(
            'geoNear' => 'vehicleTypes',
            'near' => array(
                (double) $this->input->post('pic_long'), (double) $this->input->post('pic_lat')
            ), 'spherical' => true, 'maxDistance' => 50000 / 6378137, 'distanceMultiplier' => 6378137);

        $resultArr1 = $mongo->selectCollection('$cmd')->findOne($cond);

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

        echo json_encode($typesData);
    }


    public  function dispather_bookingsControllers(){
        if($this->dispatchmodal->issessionset()) {

            $data['aap_booking'] =$this->dispatchmodal->get_app_bookings();
            $data['pagename'] = 'dispatch/dispather_bookings';
            $this->load->view('dispatch', $data);
        }
        else{

            redirect(base_url() . "index.php/dispatch/Logout");

        }

    }

    public function Ongoing_bookings(){

        if($this->dispatchmodal->issessionset()) {
            $data['ongoing_booking'] =$this->dispatchmodal->get_ongoing_bookings();
            $data['pagename'] = 'dispatch/ongoing_bookings';
            $this->load->view('dispatch', $data);
        }
        else{

            redirect(base_url() . "index.php/dispatch/Logout");

        }

    }

    public  function get_appointment_details(){
        $this->dispatchmodal->get_appointment_details();

    }

    public  function  BookingHistoryController($status = ''){


        if($this->dispatchmodal->issessionset()) {

            $data['aap_booking'] =$this->dispatchmodal->get_app_bookings_history($status);
            $data['pagename'] = 'dispatch/BookingHistory';
            $this->load->view('dispatch', $data);
        }
        else{

            redirect(base_url() . "index.php/dispatch/Logout");

        }

    }

    public  function  BookingHistoryController_ajax($status = ''){

        echo json_encode($this->dispatchmodal->get_app_bookings_history($status));
    }

    function  userstatus(){
        $this->load->library('mongo_db');

        $mongo = $this->mongo_db->db;


        $location = $mongo->selectCollection('location');
        $userDet = $location->findOne(array('user' => (int) $this->input->post('uid')));
        $data['user'] = (int) $this->input->post('uid');
        $data['status'] = 1;
        if($userDet['status'] != 3){
            $data['status'] = 2;
        }

        echo json_encode($data);

    }



    public  function DriversController(){

        if($this->dispatchmodal->issessionset()) {
            $data['pagename'] = 'dispatch/Drivers';
            $this->load->view('dispatch', $data);
        }
        else{

            redirect(base_url() . "index.php/dispatch/Logout");

        }

    }


    public  function  get_all_drivers(){
        $this->dispatchmodal->get_all_drivers();
    }

    public  function getDtiverDetail(){
        $this->dispatchmodal->getDtiverDetail();

    }


    public function get_driver_Data(){

        $datatosend = $this->dispatchmodal->get_driver_Data();


    }

//    ajax call for getting Driver arround customer
    public function getDtiversArround(){

//        header('Content-Type: application/json');


        $this->load->library('mongo_db');

        $mongo = $this->mongo_db->db;
        $query = array();
        $apptStatusVals = array(2, 3, 4);
        if($this->input->post('type_id') != "")
        $query['type'] =(int)$this->input->post('type_id');
        if (in_array($this->input->post('selected'), $apptStatusVals)) {

            $query['apptStatus'] = $this->input->post('selected') == '2' ? 6 : ( $this->input->post('selected') == '3' ? 7 : 8);
            $query['status'] = 5;
        }
      else{
          $query['status'] = 3;
      }

        $resultArr = $mongo->selectCollection('$cmd')->findOne(array(
                'geoNear' => 'location',
                'near' => array(
                    (double) $this->input->post('longitude'), (double) $this->input->post('lattitude')
                    //  (double) $_REQUEST['lat'], (double) $_REQUEST['lon']
                ), 'spherical' => true, 'maxDistance' => 50000 / 6378137, 'distanceMultiplier' => 6378137,'query' => $query)
        );

//print_r($resultArr);
        $driversArr = array();


//        $statusColors = array("3" => "green.png", "6" => 'blue.png', "7" => 'yellow.png', "8" => 'red.png');

        foreach ($resultArr['results'] as $res) {
            $doc = $res['obj'];
            $dis = $res['dis'];

            $iconPath = "http://107.170.66.211/roadyo_live/Wko8TuOH/icons/";
            $switch = ($doc['status'] != 3) ? (int) $doc['apptStatus'] : 3;
            $ico = $iconPath ."indica_";
            $icon = '';
            switch ($switch) {
                case 6: $icon = $ico . "blue.png";
                    break;
                case 7: $icon = $ico . "yellow.png";
                    break;
                case 8: $icon = $ico . "red.png";
                    break;
                default : $icon = $ico . "green.png";
                break;
            }
//
//            $image = $db->host . 'pics/xxhdpi/' . $doc['image'];
//
//            if ($doc['image'] == '')
//                $image = $db->defImage;
//
//
//            $html = '<div id="info" style="width:100px;height:50px;"><table style="font-family:roboto;font-style:regular;font-size:14px;color:black;"><td style="padding-top:5px;padding-bottom:5px;padding-left:10px;padding-right:10px;">' . $doc['name'] . ' ' . $doc['lname'] . '</td><table></div>';

            $driversArr[] = array('lat' => (double) $doc['location']['latitude'], 'lon' => (double) $doc['location']['longitude'],  'id' => $doc['user'], 'type_id' => $doc['type'],'status' => (int) $doc['status'],'icon'=>$icon);
        }

//  print_r($driversArr);

        echo json_encode(array('result' => $driversArr));


    }

    public  function refreshMap($param = '' ){

//        header('Content-Type: text/event-stream');
//        header('Cache-Control: no-cache');

//        $this->load->library('mongo_db');
//        $this->load->database();
//        $db = $this->mongo_db->db;
//        $collection = $db->location;
//        $sendtata = array();
//        $arry = json_decode($this->input->post('ids'));
//        foreach($arry as $result)
//            $sendtata[] = $result[0];

      //  $userDet = $collection->findOne(array('user' => (int) $param));
//        $query = $this->db->query("select type_icon from workplace_types where type_id ='".$userDet['type']."'")->row();
//        $iconPath = "http://107.170.66.211/roadyo_live/Wko8TuOH/icons/";
//        $switch = ($userDet['status'] != 3) ? (int) $userDet['apptStatus'] : 3;
//        $ico = $iconPath . $this->input->post('ty');
//        switch ($switch) {
//            case 6: $icon = $ico . "blue.png";
//                break;
//            case 7: $icon = $ico . "yellow.png";
//                break;
//            case 8: $icon = $ico . "red.png";
//                break;
//            default : $icon = $ico . "green.png";
//            break;
//        }

        //json_encode(array('res' => $driversArr));
//        echo "data:".json_encode(array('ids' => $sendtata,'asdf' => $this->input->post('ids')));//array('lat' => $userDet['location']['latitude'], 'lon' => $userDet['location']['longitude'],'status' => (int) $userDet['status']))."\n\n";
//
//        flush();
        $this->load->library('mongo_db');
        $this->load->database();
        $mongo = $this->mongo_db->db;
        $query = array();
        $apptStatusVals = array(2, 3, 4);

        if($this->input->post('type_id') != "")
        $query['type'] =(int)$this->input->post('type_id');

        if (in_array($this->input->post('selected'), $apptStatusVals)) {

            $query['apptStatus'] = $this->input->post('selected') == '2' ? 6 : ( $this->input->post('selected') == '3' ? 7 : 8);
            $query['status'] = 5;
        }
        else{
            $query['status'] = 3;
        }

        $resultArr = $mongo->selectCollection('$cmd')->findOne(array(
                'geoNear' => 'location',
                'near' => array(
                    (double) $this->input->post('longitude'), (double) $this->input->post('lattitude')
                    //  (double) $_REQUEST['lat'], (double) $_REQUEST['lon']
                ), 'spherical' => true, 'maxDistance' => 50000 / 6378137, 'distanceMultiplier' => 6378137,'query' => $query)
        );

        foreach ($resultArr['results'] as $res) {
            $doc = $res['obj'];
            $driversArr[] = $doc['user'];//'u'.
//            $query = $this->db->query("select type_icon from workplace_types where type_id ='".$doc['type']."'")->row_array();
            $iconPath = "http://107.170.66.211/roadyo_live/Wko8TuOH/icons/";
            $switch = ($doc['status'] != 3) ? (int) $doc['apptStatus'] : 3;
            $ico = $iconPath ."indica_";
            $icon = '';
            switch ($switch) {
                case 6: $icon = $ico . "blue.png";
                    break;
                case 7: $icon = $ico . "yellow.png";
                    break;
                case 8: $icon = $ico . "red.png";
                    break;
                default : $icon = $ico . "green.png";
                break;
            }

            $dreiverdata[$doc['user']] =array('lat' => (double) $doc['location']['latitude'], 'lon' => (double) $doc['location']['longitude'],  'id' => $doc['user'], 'type_id' => $doc['type'],'status' => (int) $doc['status'],'icon'=> $icon);
        }
        echo json_encode(array('online' => $driversArr,'master_data' => $dreiverdata));
    }


    public  function get_drivers_available(){


        $this->dispatchmodal->get_add_dreivers();

    }


    public  function get_slave_data(){

        $this->dispatchmodal->get_slave_data();

    }
    public function  add_appointment(){
        $this->dispatchmodal->add_appointment();

    }

    public  function Book_Driver($slave_id = '',$app_id = ''){



        if($this->dispatchmodal->issessionset()) {

            $data['slaveDetails'] = $this->dispatchmodal->get_salve_details($slave_id,$app_id);
            $data['pagename'] = 'dispatch/book_driver';
            $this->load->view('dispatch', $data);
        }
        else{

            redirect(base_url() . "index.php/dispatch/Logout");

        }


    }


    public function callExel($stdate = '',$enddate = '') {
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $data = $this->mastermodal->get_all_data($stdate,$enddate);
        $this->excel->stream('Transaction.xls',$data);
    }



    function Logout() {

        $this->session->sess_destroy();
        redirect(base_url() . "index.php/dispatch");
    }
    public  function test(){
        echo 'ds';
        $this->load->library('mongo_db');
        $db= $this->mongo_db->db;
        $collection = $db->location;

        $get_where = $collection->find(array('status' => 3));
        $onlinedrivers = array();
        foreach($get_where as $result){
            $onlinedrivers[]=$result['user'];
        }
        print_r($onlinedrivers);
        echo "asdf".$get_where;
        echo  implode(',',$onlinedrivers);
    }

}
