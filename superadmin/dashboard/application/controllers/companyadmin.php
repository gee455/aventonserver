<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Companyadmin extends CI_Controller {

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     * 		http://example.com/index.php/welcome
     * 	- or -
     * 		http://example.com/index.php/welcome/index
     * 	- or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see http://codeigniter.com/user_guide/general/urls.html
     */
    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model("companymodal");
//        $this->load->library('excel');
        header("cache-Control: no-store, no-cache, must-revalidate");
        header("cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
    }

    public function index($loginerrormsg = NULL) {

        $data['loginerrormsg'] = $loginerrormsg;
        $this->load->view('company_test/login', $data);
    }

    public function AuthenticateUser() {
        $email = $this->input->post("email");
        $password = $this->input->post("password");
        if ($email && $password) {


            $status = $this->companymodal->ValidateSuperAdmin();


            if (is_array($status)) {
                $this->index($status['Message']);
            } else if ($status) {
                if ($this->session->userdata('table') == 'company_info')
                    redirect(base_url() . "index.php/companyadmin/Dashboard");
            }
            else {
                $loginerrormsg = "invalid email or password";
                $this->index($loginerrormsg);
            }
        } else
            redirect(base_url() . "index.php/companyadmin");
    }

//     function ForgotPassword(){
//        $this->companymodal->ForgotPassword();
//    }

    public function Dashboard() {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }


        $sessionsetornot = $this->companymodal->issessionset();
        if ($sessionsetornot) {
            $data['todaybooking'] = $this->companymodal->Getdashboarddata();
            $data['pagename'] = "company_test/Dashboard";
            $this->load->view("company_test", $data);
        } else {
            redirect(base_url() . "index.php/companyadmin");
        }
    }

    public function datatable_vehicles($status) {


        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        $this->companymodal->datatable_vehicles($status);
    }

    public function callExel($stdate = '', $enddate = '') {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $data = $this->companymodal->get_all_data($stdate, $enddate);

//        print_r( array (new ArrayObject (array ('name'=> 'ashish','call' => '123') )) );
        $this->excel->stream('Transaction.xls', $data);
    }

    public function callExel_payroll() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $value = $this->companymodal->payroll();
        $slno = 1;
        foreach ($value as $result) {
            $data[] = array(
                'slno' => $slno,
                'Driver_Id' => $result->mas_id,
                'Driver_Name' => $result->first_name,
                'Today_earning' => $result->today_earnings,
                'Week_earning' => $result->week_earnings,
                'Month_earning' => $result->month_earnings,
                'Total_earning' => $result->total_earnings,
            );
            $slno++;
        }

//        print_r( array (new ArrayObject (array ('name'=> 'ashish','call' => '123') )) );
        $this->excel->stream('Transaction.xls', $data);
    }

//    public function cities($status = '') {
//         if ($this->session->userdata('table') != 'company_info') {
//            $this->Logout();
//        }
//        $data['country'] = $this->companymodal->get_country();
//        
//        $data['city']= $this->companymodal->get_city();
//        
//        $data['pagename'] = "company_test/cities";
//        
//      
//        $this->load->view("company_test", $data);
//       
//    }
//    
//    
//    public function showcities() {
//         if ($this->session->userdata('table') != 'company_info') {
//            $this->Logout();
//        }
//       
//       $data = $this->companymodal->loadcity();
//       echo json_encode($data);
//    }
//    
//    public function insertcities(){
//         if ($this->session->userdata('table') != 'company_info') {
//            $this->Logout();
//        }
//        $this->companymodal->insert_city_available();
//        return;
//    }
//    
//    public function editlonglat()
//    {
//         if ($this->session->userdata('table') != 'company_info') {
//            $this->Logout();
//        }
//       $data['edit'] = $this->companymodal->editlonglat();
//        
//    }
//    
//    public function addingcountry(){
//         if ($this->session->userdata('table') != 'company_info') {
//            $this->Logout();
//        }
//        return $this->companymodal->addcountry();
//    }
//    
//     public function addingcity(){
//          if ($this->session->userdata('table') != 'company_info') {
//            $this->Logout();
//        }
//        $data['addingc']= $this->companymodal->addcity();
//    }
//
//    public function addnewcity($status="") {
//         if ($this->session->userdata('table') != 'company_info') {
//            $this->Logout();
//        }
//        $data['status']=$status;
//        
//        $data['country'] = $this->companymodal->get_country();
//       
//        $data['pagename'] = 'company_test/addnewcity';
//
//        $this->load->view("company_test", $data);
//    }
//
//    public  function get_vehicle_type(){
//        
//         if ($this->session->userdata('table') != 'company_info') {
//            $this->Logout();
//        }
//
//
//        $this->load->library('mongo_db');
//
//        $mongo = $this->mongo_db->db;
//        $typesData = array();
//        $cond = array(
//            'geoNear' => 'vehicleTypes',
//            'near' => array(
//                (double) $this->input->post('pic_long'), (double) $this->input->post('pic_lat')
//            ), 'spherical' => true, 'maxDistance' => 50000 / 6378137, 'distanceMultiplier' => 6378137);
//
//        $resultArr1 = $mongo->selectCollection('$cmd')->findOne($cond);
//
//        foreach ($resultArr1['results'] as $res) {
//            $doc = $res['obj'];
//
//            $types[] = (int) $doc['type'];
//
//            $typesData[$doc['type']] = array(
//                'type_id' => (int) $doc['type'],
//                'type_name' => $doc['type_name'],
//                'max_size' => (int) $doc['max_size'],
//                'basefare' => (float) $doc['basefare'],
//                'min_fare' => (float) $doc['min_fare'],
//                'price_per_min' => (float) $doc['price_per_min'],
//                'price_per_km' => (float) $doc['price_per_km'],
//                'type_desc' => $doc['type_desc']
//            );
//        }
//
//        echo json_encode($typesData);
//    }

    public function getDtiversArround() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        $this->load->library('mongo_db');

        $mongo = $this->mongo_db->db;
        $query = array();
        $apptStatusVals = array(2, 3, 4);
        if ($this->input->post('type_id') != "")
            $query['type'] = (int) $this->input->post('type_id');
        if (in_array($this->input->post('selected'), $apptStatusVals)) {

            $query['apptStatus'] = $this->input->post('selected') == '2' ? 6 : ( $this->input->post('selected') == '3' ? 7 : 8);
            $query['status'] = 5;
        } else {
            $query['status'] = 3;
        }

        $resultArr = $mongo->selectCollection('$cmd')->findOne(array(
            'geoNear' => 'location',
            'near' => array(
                (double) $this->input->post('longitude'), (double) $this->input->post('lattitude')
            //  (double) $_REQUEST['lat'], (double) $_REQUEST['lon']
            ), 'spherical' => true, 'maxDistance' => 50000 / 6378137, 'distanceMultiplier' => 6378137, 'query' => $query)
        );


        $driversArr = array();

        foreach ($resultArr['results'] as $res) {

            if ($this->session->userdata('table') != 'company_info') {
                $this->Logout();
            }
            $doc = $res['obj'];
            $dis = $res['dis'];

            $iconPath = "http://107.170.66.211/roadyo_live/Wko8TuOH/icons/";
            $switch = ($doc['status'] != 3) ? (int) $doc['apptStatus'] : 3;
            $ico = $iconPath . "indica_";
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

            $driversArr[] = array('lat' => (double) $doc['location']['latitude'], 'lon' => (double) $doc['location']['longitude'], 'id' => $doc['user'], 'type_id' => $doc['type'], 'status' => (int) $doc['status'], 'icon' => $icon);
        }

        echo json_encode(array('result' => $driversArr, 'texxt' => $query));
    }

    public function refreshMap($param = '') {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $this->load->library('mongo_db');
        $this->load->database();
        $mongo = $this->mongo_db->db;
        $query = array();
        $apptStatusVals = array(2, 3, 4);

        if ($this->input->post('type_id') != "")
            $query['type'] = (int) $this->input->post('type_id');

        if (in_array($this->input->post('selected'), $apptStatusVals)) {

            $query['apptStatus'] = $this->input->post('selected') == '2' ? 6 : ( $this->input->post('selected') == '3' ? 7 : 8);
            $query['status'] = 5;
        } else {
            $query['status'] = 3;
        }

        $resultArr = $mongo->selectCollection('$cmd')->findOne(array(
            'geoNear' => 'location',
            'near' => array(
                (double) $this->input->post('longitude'), (double) $this->input->post('lattitude')
            //  (double) $_REQUEST['lat'], (double) $_REQUEST['lon']
            ), 'spherical' => true, 'maxDistance' => 50000 / 6378137, 'distanceMultiplier' => 6378137, 'query' => $query)
        );

        foreach ($resultArr['results'] as $res) {
            $doc = $res['obj'];
            $driversArr[] = $doc['user']; //'u'.
//            $query = $this->db->query("select type_icon from workplace_types where type_id ='".$doc['type']."'")->row_array();
            $iconPath = "http://107.170.66.211/roadyo_live/Wko8TuOH/icons/";
            $switch = ($doc['status'] != 3) ? (int) $doc['apptStatus'] : 3;
            $ico = $iconPath . "indica_";
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

            $dreiverdata[$doc['user']] = array('lat' => (double) $doc['location']['latitude'], 'lon' => (double) $doc['location']['longitude'], 'id' => $doc['user'], 'type_id' => $doc['type'], 'status' => (int) $doc['status'], 'icon' => $icon);
        }
        echo json_encode(array('online' => $driversArr, 'master_data' => $dreiverdata));
    }

    public function getDtiverDetail() {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $this->companymodal->getDtiverDetail();
    }

//    public function add_edit($status="",$param = '') {
//         if ($this->session->userdata('table') != 'company_info') {
//            $this->Logout();
//        }
//          
//        $data['param'] = "";
//        
//        if($status == 'edit'){
//            $data['city']= $this->companymodal->city();
//             $data['get_company_data']= $this->companymodal->get_company_data($param);
//            
//             $data['status']=$status;
//             $data['param'] = $param;
//        }elseif ($status == 'add') {
//             $data['city']= $this->companymodal->city();
//              $data['status']=$status;
//              $data['param'] = "";
//              
//        }
//        $data['pagename'] = 'company_test/add_edit';
//
//        $this->load->view("company_test", $data);
//    }
//    
//    public function activatecompany(){
//         if ($this->session->userdata('table') != 'company_info') {
//            $this->Logout();
//        }
//        $data['activate']=$this->companymodal->activate_company();
//    }
//    
//    public function suspendcompany(){
//         if ($this->session->userdata('table') != 'company_info') {
//            $this->Logout();
//        }
//        $data['suspend']=$this->companymodal->suspend_company();
//    }
//    
//     public function deactivatecompany(){
//          if ($this->session->userdata('table') != 'company_info') {
//            $this->Logout();
//        }
//        $data['deactivate']=$this->companymodal->deactivate_company();
//    }
//    
//    public function insertcompany(){
//         if ($this->session->userdata('table') != 'company_info') {
//            $this->Logout();
//        }
//        $data['company']=$this->companymodal->insert_company();
//    }
//    
//    public function updatecompany($param=''){
//         if ($this->session->userdata('table') != 'company_info') {
//            $this->Logout();
//        }
//       
//        $data['updatecompany'] = $this->companymodal->update_company($param);
//    }
//
//    public function company_s($status='') {
//         if ($this->session->userdata('table') != 'company_info') {
//            $this->Logout();
//        }
//        $data['status']=$status;
//          $data['city']= $this->companymodal->city();
//        $data['company']= $this->companymodal->get_companyinfo($status);
//        
//        
//        
//        $data['pagename'] = "company_test/company_s";
//        $this->load->view("company_test", $data);
////        $this->load->view("cities");
//    }
//
//    public function vehicle_type() {
//         if ($this->session->userdata('table') != 'company_info') {
//            $this->Logout();
//        }
//        $data['pagename'] = "company/vehicle_type";
//         $data['vehicletype']=$this->companymodal->get_vehicle_data();
//        $this->load->view("company_test", $data);
////        $this->load->view("cities");
//    }
//    
//    public function delete_vehicletype(){
//         if ($this->session->userdata('table') != 'company_info') {
//            $this->Logout();
//        }
//        $data['delete']=$this->companymodal->delete_vehicletype();
//    }
//    
//    public function inactivedriver_review(){
//         if ($this->session->userdata('table') != 'company_info') {
//            $this->Logout();
//        }
//        $data['driver_review']=$this->companymodal->inactivedriver_review();
//    }
//    
//     public function activedriver_review(){
//          if ($this->session->userdata('table') != 'company_info') {
//            $this->Logout();
//        }
//        $data['driver_review']=$this->companymodal->activedriver_review();
//    }
//    
//    
//    public function vehicletype_addedit($status='',$param=''){
//         if ($this->session->userdata('table') != 'company_info') {
//            $this->Logout();
//        }
//        
//         $data['param'] = "";
//         print($data);
//        
//        if($status == 'edit'){
//             $data['editvehicletype']=$this->companymodal->edit_vehicletype($param);
//             
//             $data['status']=$status;
//             $data['param'] = $param;
//            
//        }elseif ($status == 'add') {
//             $data['city']= $this->companymodal->city();
//              $data['status']=$status;
//              $data['param'] = "";
//              
//        }
//        $data['pagename'] = "company_test/vehicletype_addedit"; 
//         $data['city']= $this->companymodal->get_city();
//         $this->load->view("company_test", $data);
//    }
//    
// 
//
//    
//    public function insert_vehicletype(){
//         if ($this->session->userdata('table') != 'company_info') {
//            $this->Logout();
//        }
//        $data=$this->companymodal->insert_vehicletype();
//    }
//    
//    public function update_vehicletype($param=''){
//         if ($this->session->userdata('table') != 'company_info') {
//            $this->Logout();
//        }
//        $data['updatevehicletype'] =$this->companymodal->update_vehicletype($param);
//    }
//    
//    
//    public function inactivedispatchers(){
//         if ($this->session->userdata('table') != 'company_info') {
//            $this->Logout();
//        }
//        $data=$this->companymodal->inactivedispatchers();
//    }
//    
//     public function activedispatchers(){
//          if ($this->session->userdata('table') != 'company_info') {
//            $this->Logout();
//        }
//        $data=$this->companymodal->activedispatchers();
//    }
//    
//     public function editdispatchers(){
//          if ($this->session->userdata('table') != 'company_info') {
//            $this->Logout();
//        }
//        $data=$this->companymodal->editdispatchers();
//    }
//    
//    public function insertdispatches(){
//         if ($this->session->userdata('table') != 'company_info') {
//            $this->Logout();
//        }
//        $data=$this->companymodal->insertdispatches();
//        
//    }

    public function editpass() {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data = $this->companymodal->editpass();
    }

//    public function passengers($status = '') {
//         if ($this->session->userdata('table') != 'company_info') {
//            $this->Logout();
//        }
//
//        $data['status'] = $status;
//        $data['passenger_info']=$this->companymodal->get_passengerinfo($status);
//        //print_r($data);
//        $data['pagename'] = "company_test/passengers";
//        $this->load->view("company_test", $data);
//    }
//
//    public function inactivepassengers(){
//         if ($this->session->userdata('table') != 'company_info') {
//            $this->Logout();
//        }
//        $data['inactive']=$this->companymodal->inactivepassengers();
//    }
//    
//    
//    public function activepassengers(){
//         if ($this->session->userdata('table') != 'company_info') {
//            $this->Logout();
//        }
//        $data['inactive']=$this->companymodal->activepassengers();
//    }

    public function insertpass() {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data['pass'] = $this->companymodal->insertpass();
        print_r($res);
    }

    public function bookings($status = '') {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data['status'] = "$status";
        $data['pagename'] = "company_test/bookings";
        $this->load->view("company_test", $data);
    }

//    public function dispatched($status='') {
//         if ($this->session->userdata('table') != 'company_info') {
//            $this->Logout();
//        }
//        $data['status']="$status";
//         $data['city']= $this->companymodal->city();
//        $data['pagename'] = "company_test/dispatched";
//        $data['getdata']=$this->companymodal->get_dispatchers_data($status);
//        $this->load->view("company_test", $data);
//    }

    public function finance($status = '') {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data['status'] = $status;
        $data['pagename'] = "company_test/finance";
        $this->load->view("company_test", $data);
    }

    public function document($status = '') {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }



        $data['status'] = $status;

        $data['master'] = $this->companymodal->driver();

        $data['document_data'] = $this->companymodal->get_documentdata($status);

        $data['workname'] = $this->companymodal->get_workplace();


        $data['pagename'] = "company_test/document";
        $this->load->view("company_test", $data);
    }

//    public function passenger_rating() {
//         if ($this->session->userdata('table') != 'company_info') {
//            $this->Logout();
//        }
//        $data['pagename'] = "company_test/passenger_rating";
//        $data['passenger_rating']=$this->companymodal->passenger_rating();
//        $this->load->view("company_test", $data);
//    }
//
//    public function driver_review($status='') {
//         if ($this->session->userdata('table') != 'company_info') {
//            $this->Logout();
//        }
//        $data['status']=$status;
//        $data['driver_review']=$this->companymodal->driver_review($status);
//        $data['pagename'] = "company_test/driver_review";
//        $this->load->view("company_test", $data);
//    }
//
//    public function disputes($status='') {
//         if ($this->session->userdata('table') != 'company_info') {
//            $this->Logout();
//        }
//        $data['status']=$status;
//        $data['city']= $this->companymodal->get_city();
//        $data['disputesdata']=$this->companymodal->get_disputesdata($status);
//        $data['master']=$this->companymodal->driver();
//        $data['slave']=$this->companymodal->passenger();
//        
//        $data['pagename'] = "company_test/disputes";
//        $this->load->view("company_test", $data);
//    }
//    
//    public function resolvedisputes()
//    {
//         if ($this->session->userdata('table') != 'company_info') {
//            $this->Logout();
//        }
//        $data=$this->companymodal->resolvedisputes();
//    }
//
//    public function delete() {
//         if ($this->session->userdata('table') != 'company_info') {
//            $this->Logout();
//        }
//        $data['pagename'] = "company_test/delete";
//        $this->load->view("company_test", $data);
//    }
//    
//    public function deactivecompaigns() {
//         if ($this->session->userdata('table') != 'company_info') {
//            $this->Logout();
//        }
//    $data['deactivate']=$this->companymodal->deactivecompaigns();
//    }
//
//    public function godsview() {
//         if ($this->session->userdata('table') != 'company_info') {
//            $this->Logout();
//        }
//        $data['pagename'] = "company_test/godsview";
//        $this->load->view("company_test", $data);
//    }
//
//    public function vehicle_models($status='') {
//         if ($this->session->userdata('table') != 'company_info') {
//            $this->Logout();
//        }
//        $data['status']=$status;
//        $data['vehiclemake']=$this->companymodal->get_vehiclemake();
//        $data['vehiclemodal']=$this->companymodal->get_vehiclemodal();
//        $data['pagename'] = "company_test/vehicle_models";
//        $this->load->view("company_test", $data);
//    }
//    
//    public function inserttypename(){
//         if ($this->session->userdata('table') != 'company_info') {
//            $this->Logout();
//        }
//        $data=$this->companymodal->insert_typename();
//    }
//    public function insertmodal(){
//         if ($this->session->userdata('table') != 'company_info') {
//            $this->Logout();
//        }
//        $data=$this->companymodal->insert_modal();
//    }
//
//    public function deletevehicletype(){
//         if ($this->session->userdata('table') != 'company_info') {
//            $this->Logout();
//        }
//        $data=$this->companymodal->deletevehicletype();
//    }
//    
//    public function delete_company(){
//         if ($this->session->userdata('table') != 'company_info') {
//            $this->Logout();
//        }
//        $data= $this->companymodal->delete_company();
//    }
//    
//    public function deletevehiclemodal(){
//         if ($this->session->userdata('table') != 'company_info') {
//            $this->Logout();
//        }
//        $data=$this->companymodal->deletevehiclemodal();
//    }
//    
//    public function compaigns($status='') {
//         if ($this->session->userdata('table') != 'company_info') {
//            $this->Logout();
//        }
//     
//             $data['status']=$status;
//             $data['city']= $this->companymodal->get_city();
//             $data['compaign']=$this->companymodal->get_compaigns_data($status);
//            
//        $data['pagename'] = "company_test/compaigns";
//        $this->load->view("company_test", $data);
//    }
//    
//    public function  insertcompaigns(){
//         if ($this->session->userdata('table') != 'company_info') {
//            $this->Logout();
//        }
//        echo $this->companymodal->insertcampaigns();
//    }

    public function cancled_booking() {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data['pagename'] = "company_test/cancled_booking";
        $this->load->view("company_test", $data);
//        $this->load->view("cities");
    }

    public function Get_dataformdate($stdate = '', $enddate = '') {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
//        $data = $this->companymodal->get_all_data();
        $data['transection_data'] = $this->companymodal->getDatafromdate($stdate, $enddate);
        $data['stdate'] = $stdate;
        $data['enddate'] = $enddate;
        $data['gat_way'] = '2';
        $data['pagename'] = "company_test/Transection";
        $this->load->view("company_test", $data);
    }

//    public function get_vehicle_data(){
//        $data['vehicletype']=$this->companymodal->get_vehicle_data();
//      
//    }

    public function search_by_select($selectdval) {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data['transection_data'] = $this->companymodal->getDataSelected($selectdval);
//        $data['pagename'] = "company_test/Transection";
//        $data['gat_way'] = $selectdval;
//        $this->load->view("company_test", $data);
    }

    public function profile() {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $sessionsetornot = $this->companymodal->issessionset();
        if ($sessionsetornot) {
            $data['userinfo'] = $this->companymodal->getuserinfo();
            $data['pagename'] = "company_test/profile";
            $this->load->view("company_test", $data);
        } else {
            redirect(base_url() . "index.php/companyadmin");
        }
    }

    public function services() {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        $sessionsetornot = $this->companymodal->issessionset();
        if ($sessionsetornot) {
            if ($this->session->userdata('table') != 'company_info') {
                $this->Logout();
            }

            $data['service'] = $this->companymodal->getActiveservicedata();
            $data['pagename'] = "company_test/Addservice";
            $this->load->view("company_test", $data);
        } else {
            redirect(base_url() . "index.php/companyadmin");
        }
    }

    public function updateservices($tablename = '') {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        $this->companymodal->updateservices($tablename);
        redirect(base_url() . "index.php/companyadmin/services");
    }

    function deleteservices($tablename = "") {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $this->companymodal->deleteservices($tablename);
        redirect(base_url() . "index.php/companyadmin/services");
    }

    function Banking() {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        $sessionsetornot = $this->companymodal->issessionset();
        if ($sessionsetornot) {

//            $data['service'] = $this->companymodal->getActiveservicedata();
            $data['pagename'] = "company_test/banking";
            $this->load->view("company_test", $data);
        } else {
            redirect(base_url() . "index.php/companyadmin");
        }
    }

    public function addservices() {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        $data['service'] = $this->companymodal->addservices();
        redirect(base_url() . "index.php/companyadmin/services");
    }

//    public function booking() {
//        $sessionsetornot = $this->madmin->issessionset();
//        if ($sessionsetornot) {
//            $data['bookinlist'] = $this->madmin->getPassangerBooking();
//            $data['pagename'] = "booking";
//            $this->load->view("index", $data);
//        } else {
//            redirect(base_url() . "index.php/companyadmin");
//        }
//    }

    function Logout() {

        $this->session->sess_destroy();
        redirect(base_url() . "index.php/companyadmin");
    }

    function udpadedataProfile() {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $this->companymodal->updateDataProfile();

        if ($this->input->post('val')) {
            $filename = "demo.png";
            if (move_uploaded_file($_FILES['userfile']['tmp_name'], base_url() . 'files/' . $filename)) {
                echo $filename;
            }
        }
        redirect(base_url() . "index.php/companyadmin/profile");
    }

//   
    public function updateMasterBank() {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        $ret = $this->companymodal->updateMasterBank();
        $data['error'] = $ret['flag'];
        $data['error_message'] = $ret['message'];
        $data['error_array'] = $ret;
        $data['userData'] = $ret['data'];
        $data['pagename'] = "master/banking";
        $this->load->view("master", $data);
    }

    public function Vehicles($status = '') {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }


        $this->load->library('Datatables');
        $this->load->library('table');

        $tmpl = array('table_open' => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="table table-hover demo-table-search dataTable no-footer" id="tableWithSearch" role="grid" aria-describedby="tableWithSearch_info" style="margin-top: 30px;">',
            'heading_row_start' => '<tr style= "font-size:20px"role="row">',
            'heading_row_end' => '</tr>',
            'heading_cell_start' => '<th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 50px;font-size: 14px;">',
            'heading_cell_end' => '</th>',
            'row_start' => '<tr>',
            'row_end' => '</tr>',
            'cell_start' => '<td>',
            'cell_end' => '</td>',
            'row_alt_start' => '<tr>',
            'row_alt_end' => '</tr>',
            'cell_alt_start' => '<td>',
            'cell_alt_end' => '</td>',
            'table_close' => '</table>'
        );
        $this->table->set_template($tmpl);

        $this->table->set_heading('VEHICLE ID', 'VEHICLE MAKE', 'VEHICLE MODEL', 'TYPE NAME', 'COMPANY NAME', 'VEHICLE  REGISTRATION NUMBER', 'LICENSE PLATE NUMBER', 'VEHICLE INSURANCE NUMBER', 'VEHICLE COLOR', 'SELECT');


//        echo 'hello';
//        exit();
        $data['pagename'] = 'company_test/vehicles';
        $data['status'] = $status;
        $this->load->view("company_test", $data);
    }

    public function datatable_drivers($for = '', $status = '') {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data = $this->companymodal->datatable_drivers($for, $status);
    }

    public function Drivers($for = '', $status = '') {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }


        $this->load->library('Datatables');
        $this->load->library('table');
        $data['status'] = $status;

        $tmpl = array('table_open' => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="table table-hover demo-table-search dataTable no-footer" id="tableWithSearch" role="grid" aria-describedby="tableWithSearch_info" style="margin-top: 30px;">',
            'heading_row_start' => '<tr style= "font-size:20px"role="row">',
            'heading_row_end' => '</tr>',
            'heading_cell_start' => ' <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 127px;font-size: 14px;">',
            'heading_cell_end' => '</th>',
            'row_start' => '<tr>',
            'row_end' => '</tr>',
            'cell_start' => '<td>',
            'cell_end' => '</td>',
            'row_alt_start' => '<tr>',
            'row_alt_end' => '</tr>',
            'cell_alt_start' => '<td>',
            'cell_alt_end' => '</td>',
            'table_close' => '</table>'
        );
        $this->table->set_template($tmpl);

        $this->table->set_heading('DRIVER ID', 'FIRST NAME', 'LAST NAME', 'MOBILE', 'EMAIL', 'REG DATE', 'COMPANY', 'VEHICLE ID', 'PROFILE PIC', 'DEVICE TYPE', 'VEHICLE IMAGE', 'LATITUDE', 'LONGITUDE', 'SELECT');


        $data['pagename'] = 'company_test/drivers';
        $this->load->view("company_test", $data);
    }

    public function payroll() {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

//        $data['payroll']=$this->adminmodel->payroll();
        $data['pagename'] = 'company_test/payroll';

        $this->load->library('Datatables');
        $this->load->library('table');
        $tmpl = array('table_open' => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="table table-hover demo-table-search dataTable no-footer" id="tableWithSearch" role="grid" aria-describedby="tableWithSearch_info" style="margin-top: 30px;">',
            'heading_row_start' => '<tr role="row">',
            'heading_row_end' => '</tr>',
            'heading_cell_start' => '<th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 50px;">',
            'heading_cell_end' => '</th>',
            'row_start' => '<tr>',
            'row_end' => '</tr>',
            'cell_start' => '<td>',
            'cell_end' => '</td>',
            'row_alt_start' => '<tr>',
            'row_alt_end' => '</tr>',
            'cell_alt_start' => '<td>',
            'cell_alt_end' => '</td>',
            'table_close' => '</table>'
        );

        $this->table->set_template($tmpl);

        $this->table->set_heading('DRIVER ID', 'NAME', 'TODAY EARNINGS (' . currency . ')', 'WEEK EARNINGS (' . currency . ')', 'MONTH EARNINGS (' . currency . ')', 'LIFE TIME EARNINGS (' . currency . ')', 'PAID (' . currency . ')', 'DUE (' . currency . ')', 'SHOW');
        $this->load->view("company_test", $data);
    }

    public function payroll_ajax() {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $this->companymodal->payroll();
    }

    public function payroll_data_form_date($stdate = '', $enddate = '', $company_id = '') {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $this->companymodal->payroll_data_form_date($stdate, $enddate, $company_id);
    }

    public function Driver_pay($id = '') {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        $data['driverdata'] = $this->companymodal->Driver_pay($id);
        $data['payrolldata'] = $this->companymodal->get_payrolldata($id);
        $data['totalamountpaid'] = $this->companymodal->Totalamountpaid($id);
        $data['mas_id'] = $id;
        $data['pagename'] = 'company_test/driverpayment';
        $this->load->view("company_test", $data);
    }

    public function pay_driver_amount($id = '') {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $this->companymodal->insert_payment($id);
        redirect(base_url() . "index.php/companyadmin/Driver_pay/" . $id);
    }

    public function addnewvehicle() {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        $data['vehicletype'] = $this->companymodal->getvehicletype();


        $data['pagename'] = 'company_test/addnewvehicle';

        $this->load->view("company_test", $data);
    }

    public function addnewdriver() {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }


        $data['pagename'] = 'company_test/addnewdriver';

        $this->load->view("company_test", $data);
    }

    public function validateEmail() {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        return $this->companymodal->validateEmail();
    }

    public function AddNewDriverData() {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $this->companymodal->AddNewDriverData();
        redirect(base_url() . "index.php/companyadmin/Drivers/my/1");
    }

    public function deleteDrivers() {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        $this->companymodal->deletedriver();
//        $this->Drivers('my', '1');
    }

    public function editdriver($status = '') {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        $return['data'] = $this->companymodal->editdriver($status);
//        
//         print_r($return);
//         exit();
        $return['driverid'] = $status;



        $return['pagename'] = 'company_test/editdriver';

        $this->load->view("company_test", $return);
    }

    public function editdriverdata() {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $this->companymodal->editdriverdata();
        redirect(base_url() . "index.php/companyadmin/Drivers/my/1");
    }

    public function AddNewVehicleData() {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $this->companymodal->AddNewVehicleData();
        redirect(base_url() . "index.php/companyadmin/Vehicles/5");
    }

    public function DriverDetails($mas_id = '') {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data['driverdetails'] = $this->companymodal->DriverDetails($mas_id);
        $data['pagename'] = 'company_test/driverDetails';

        $this->load->view("company_test", $data);
    }

    public function deletecities() {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data['del'] = $this->companymodal->deletecity();
    }

    public function deleteVehicles() {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        $driverlist = $this->input->post('val');
        $this->load->database();
        foreach ($driverlist as $result) {


            $affectedRows = 0;

            $selectCars = $this->db->query("select appointment_id from appointment where car_id = '" . $result . "'")->result_array();

            $apptIDs = array();
            foreach ($selectCars as $type) {
                $apptIDs[] = (int) $type['appointment_id'];
            }

            $masDet = $this->db->query("select mas_id from master where workplace_id= '" . $result . "'")->result_array();


            if (is_array($masDet)) {
                $m = new MongoClient();
                $db = $m->roadyo_live;

                $location = $db->selectCollection('location');

                $location->update(array('carId' => (int) $result), array('$set' => array('type' => 0, 'carId' => 0, 'status' => 4)), array('multiple' => 1));

                $this->db->query("update master set type_id = 0 and workplace_id = 0 where mas_id = '" . $masDet['mas_id'] . "'");
            }

            $varify = implode(',', $apptIDs);
            $this->db->query("delete from workplace where workplace_id = '" . $result . "'");
            $affectedRows += $this->db->affected_rows();

            $this->db->query("delete from appointment where appointment_id in ('" . $varify . "')");
            $affectedRows += $this->db->affected_rows();

            $this->db->query("delete from passenger_rating where appointment_id in ('" . $varify . "')");
            $affectedRows += $this->db->affected_rows();

            $this->db->query("delete from master_ratings where appointment_id in ('" . $varify . "')");
            $affectedRows += $this->db->affected_rows();

            $this->db->query("delete from user_sessions where user_type = 1 and oid = '" . $masDet['mas_id'] . "'");
            $affectedRows += $this->db->affected_rows();
        }
        echo json_encode(array('flag' => 0, 'affectedRows' => $affectedRows, 'message' => 'Process completed.'));
    }

    public function ajax_call_to_get_types($param = '') {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        $this->load->database();
        if ($param == 'vtype') {
            $get_vechile_type = $this->db->query("select type_id,type_name from workplace_types where city_id = '" . $this->input->post('city') . "'")->result();
            echo "<option value=''>Select a type</option>";
            foreach ($get_vechile_type as $typelist) {
                echo "<option value='" . $typelist->type_id . "' id='" . $typelist->type_id . "' >" . $typelist->type_name . "</option>";
            }
        } else if ($param == 'vmodel') {
            $loupon_sql = $this->db->query("SELECT * FROM vehiclemodel where vehicletypeid = '" . $_REQUEST['adv'] . "'")->result();
            $options = '';
            foreach ($loupon_sql as $loupon_sql_row) {
                $options .= "<option value='" . $loupon_sql_row->id . "' id='" . $loupon_sql_row->id . "'>" . $loupon_sql_row->vehiclemodel . "</option>";
            }
            echo $options;
        } else if ($param == 'companyselect') {
            $get_company = $this->db->query("select company_id,companyname from company_info where city = '" . $this->input->post('company') . "' and status = 3")->result();
            echo " <option value=''>Select a Company  </option>";
            foreach ($get_company as $row) {
                echo "<option value='" . $row->company_id . "' id='" . $row->company_id . "' >" . $row->companyname . "</option>";
            }
        }
    }

    public function documentgetdatavehicles() {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        echo json_encode($this->companymodal->documentgetdatavehicles());
        exit();
    }

    public function documentgetdata() {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        echo json_encode($this->companymodal->documentgetdata());
        exit();
    }

    public function editdriverpassword() {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data = $this->companymodal->editdriverpassword();
    }

    public function showcompanys() {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        $data = $this->companymodal->loadcompany();
        $vt = $this->input->post('vt');

        if ($vt == '1')
            $this->session->set_userdata(array('city_id' => $this->input->post('city')));

        $return = "<option value='0'>Select Company ...</option><option value='0'>None</option>";

        foreach ($data as $city) {
            $return .= "<option value='" . $city['company_id'] . "'>" . $city['companyname'] . "</option>";
        }

        echo $return;
    }

    public function setcity_session() {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        $meta = array('city_id' => $this->input->post('city'), 'company_id' => $this->input->post('company'));
        $this->session->set_userdata($meta);
    }

    public function transection_data_form_date($stdate = '', $enddate = '', $status = '', $company_id = '') {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $this->companymodal->transection_data_form_date($stdate, $enddate, $status, $company_id);
    }

    public function transection_data_ajax() {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        $this->companymodal->getTransectionData();
    }

    public function Transection() {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $sessionsetornot = $this->companymodal->issessionset();
//        if ($sessionsetornot) {

        $data['gat_way'] = "2";
        $data['pagename'] = "company_test/Transection";

        $this->load->library('Datatables');
        $this->load->library('table');
        $this->table->clear();
        $tmpl = array('table_open' => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="table table-hover demo-table-search dataTable no-footer" id="tableWithSearch" role="grid" aria-describedby="tableWithSearch_info" style="margin-top: 30px;">',
            'heading_row_start' => '<tr role="row">',
            'heading_row_end' => '</tr>',
            'heading_cell_start' => '<th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 50px;">',
            'heading_cell_end' => '</th>',
            'row_start' => '<tr>',
            'row_end' => '</tr>',
            'cell_start' => '<td>',
            'cell_end' => '</td>',
            'row_alt_start' => '<tr>',
            'row_alt_end' => '</tr>',
            'cell_alt_start' => '<td>',
            'cell_alt_end' => '</td>',
            'table_close' => '</table>'
        );


        $this->table->set_template($tmpl);

        $this->table->set_heading('Booking Id', 'Driver Id', 'Date', 'Fare ("' . currency . '")', 'Discount ("' . currency . '")', 'Discounted Fare ("' . currency . '")', 'App commission (' . currency . ')', 'Payment Gateway commission (' . currency . ')', 'Driver Earning (' . currency . ')', 'Transection Id', 'Booking Status', 'Payment Type', 'Download');

        $this->load->view("company_test", $data);
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */