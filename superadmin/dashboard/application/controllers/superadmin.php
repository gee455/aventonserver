<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Superadmin extends CI_Controller {

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
        $this->load->model("superadminmodal");
        $this->load->library('session');
//        $this->load->library('excel');
        header("cache-Control: no-store, no-cache, must-revalidate");
        header("cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
    }

    public function index($loginerrormsg = NULL) {
        $data['loginerrormsg'] = $loginerrormsg;

          if ($this->session->userdata('table') == 'company_info') {
            redirect(base_url() . "index.php/superadmin/Dashboard");
        }else
            $this->load->view('company/login', $data);
    }

    public function setcity_session() {

        $meta = array('city_id' => $this->input->post('city'), 'company_id' => $this->input->post('company'));
        $this->session->set_userdata($meta);
    }
    
    function selectCityZone(){
          if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
     
         $res = $this->superadminmodal->getZoneCities();
        
    }
    
     public function get_appointment_details() {
        $this->superadminmodal->get_appointment_details();
    }
    
     public function zones() {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        
         $data['zones_data'] = $this->superadminmodal->zones_data();
         $data['cities'] = $this->superadminmodal->cityForZones();
        
         $data['pagename'] = "company/zones";
            $this->load->view("company", $data);
    }
    
     public function tripDetails($param) {
        
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        
         $return['data'] = $this->superadminmodal->tripDetails($param);
         
         
         $return['pagename'] = "company/trip_details";
         $this->load->view("company", $return);
        //$this->superadminmodal->tripDetails();
        
    }


    public function AuthenticateUser() {
        $email = $this->input->post("email");
        $password = $this->input->post("password");
        
     
        if ($email && $password) {


            $status = $this->superadminmodal->ValidateSuperAdmin();

            if ($status) {
                if ($this->session->userdata('table') == 'company_info')
                    redirect(base_url() . "index.php/superadmin/Dashboard");
            } else {
                $loginerrormsg = "invalid email or password";
                $this->index($loginerrormsg);
            }
        } else
            redirect(base_url() . "index.php/superadmin");
    }

    function ForgotPassword() {
        $this->superadminmodal->ForgotPassword();
    }

    public function uniq_val() {

        $this->superadminmodal->uniq_val_chk();
    }

    public function startpage() {

        $data['pagename'] = 'company/startpage';

        $this->load->view("company", $data);
    }
    
     public function NotificationData() {
          $res =  $this->superadminmodal->NotificationData();
          echo json_encode(array('Result'=>$res));
     }
     
      public function NotificationDataAll() {
          $res =  $this->superadminmodal->NotificationDataAll();
          echo json_encode(array('Result'=>$res));
     }

    // notifyi pdriver
    public function NotifyiConsole($id) {


        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
//            $data['Driver_data'] =  $this->superadminmodal->get_notifieduser(1);
//            $data['Passenger_data'] =  $this->superadminmodal->get_notifieduser(2);
            
           
             $data['status'] =  $id;

//        $this->load->library('Datatables');
//        $this->load->library('table');
//        $this->table->clear();
//        $tmpl = array('table_open' => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="table table-hover demo-table-search dataTable no-footer" id="tableWithSearch" role="grid" aria-describedby="tableWithSearch_info" style="margin-top: 30px;">',
//            'heading_row_start' => '<tr role="row">',
//            'heading_row_end' => '</tr>',
//            'heading_cell_start' => '<th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 50px;">',
//            'heading_cell_end' => '</th>',
//            'row_start' => '<tr>',
//            'row_end' => '</tr>',
//            'cell_start' => '<td>',
//            'cell_end' => '</td>',
//            'row_alt_start' => '<tr>',
//            'row_alt_end' => '</tr>',
//            'cell_alt_start' => '<td>',
//            'cell_alt_end' => '</td>',
//            'table_close' => '</table>'
//        );
//
//
//        $this->table->set_template($tmpl);
//
//        $this->table->set_heading('SLNO','CITY', 'MESSAGE', 'NUMBER OF DRIVERS SENT TO','NUMBER OF RIDERS SENT TO', 'DATE');
//
//

        $data['pagename'] = 'company/NotifyiConsole';

        $data['citys'] = $this->superadminmodal->get_city_available();


        $this->load->view("company", $data);
    }

//    public function ajax_NotifyiConsole($id) {
//        $this->superadminmodal->get_notifieduser($id);
//    }
    
    //Get driver email id
    public function show_allEmails() {
        $data = $this->superadminmodal->show_allEmails();
        echo json_encode($data);
    }
    

    public function SendPush() {

        $this->load->library('mongo_db');

        $mongo = $this->mongo_db->db;

        $driversArrAndroid = array();
        $driversArrIos = $array =array();
        $User_ids = array();

        $citylatlon = explode('-', $this->input->post('city'));
        $message = $this->input->post('message');

        $msg = "Driver";
        $query = "";
        $usertype = $this->input->post('usertype');
        $city_id = $this->input->post('city');
        if ($usertype == 2) {
            $query = '(SELECT us.push_token FROM slave s,user_sessions us WHERE (3956 * ACOS( COS( RADIANS(' . $citylatlon[0] . ') ) * COS( RADIANS(s.latitude) ) * COS( RADIANS(s.longitude) - RADIANS(' . $citylatlon[1] . ')) + SIN( RADIANS(' . $citylatlon[0] . ')) * SIN( RADIANS(s.latitude) ) ) ) <= ' . PUSH_PASSANGER_REDIUOS . ' AND us.oid = s.slave_id AND us.user_type = "2")';
            $msg = "Passanger";
            $data = $this->db->query($query)->result();
            foreach ($data as $res)
            {
                $driversArrAndroid[] = $res->push_token;
                $User_ids[] = $res->oid;
            }
        } else {
            $resultArr = $mongo->selectCollection('$cmd')->findOne(array(
                'geoNear' => 'location',
                'near' => array(
                    (double) $citylatlon[1], (double) $citylatlon[0]
                //  (double) $_REQUEST['lat'], (double) $_REQUEST['lon']
                ), 'spherical' => true, 'maxDistance' => 50000 / 6378137, 'distanceMultiplier' => 6378137)
            );


            foreach ($resultArr['results'] as $res) {

                $doc = $res['obj'];

                if ($doc['User_type'] == 1)
                {
                    $driversArrIos[] = $doc['pushToken'];
                      $User_ids[] = $doc['user'];
                }
                if ($doc['User_type'] == 2)
                {
                    $driversArrAndroid[] = $doc['pushToken'];
                     $User_ids[] = $doc['user'];
                }
            }

            if (empty(array_filter($driversArrAndroid))) {// || empty(array_filter($driversArrIos))) {
                echo json_encode(array('flag' => 2, 'msg' => $msg));
                return;
            }
        }

          foreach ($driversArrAndroid as $val) {
            if (!in_array($val,$array)) {
                $array[] = $val;
            }
        }

        $driversArrAndroid = $array;
        
        $data = $this->superadminmodal->senPushToDriver($driversArrIos, $driversArrAndroid, $message,$city_id, $usertype,$User_ids);
        if ($data['errorNo'] == 44)
            echo json_encode(array('count' => $data['count'],'user_id'=>$User_ids,'IOS'=>$data['test1'], 'msg' => $msg, 'flag' => 1, 'err' => $data['err'], 'array' => $data));
        else
            echo json_encode(array('count' => $data['count'], 'dataFrompush' => $data, 'user_id'=>$User_ids, 'flag' => 3, 'err' => "Something went wrong.", 'array' => array('and' => $driversArrAndroid, 'ios' => $driversArrIos)));
    }
    
     public function sendPushToSpecific() {

        $this->load->library('mongo_db');

        $mongo = $this->mongo_db->db;

        $driversArrAndroid = array();
        $driversArrIos =array();
        $User_ids = array();
        $user_data = array();

  
//        $emails = $this->input->post('emails');
        $User_id = $this->input->post('User_id');
        $message = $this->input->post('message');
        $city_id = $this->input->post('city_id');
        
//          foreach ($User_id as $a)
//                 $user_data [] = $a;
          
//           $users = implode(',', array_filter(array_unique($User_id)));
          
        
        $msg = "Driver";
        $query = "";
        $usertype = $this->input->post('usertype');
        
        if ($usertype == 2) {
            $query = "select * from slave where slave_id in ('". $User_id ."')"; //If the passengers are deleted so for that check user exist on not
            $msg = "Passanger";
            $data = $this->db->query($query)->result();
            foreach ($data as $res)
            {
//                $driversArrAndroid[] = $res->push_token;
                $User_ids[] = $res->slave_id;
            }
            
            $query1 = "select * from user_sessions where oid in ('". $User_ids ."') and user_type = 2 and loggedIn = 1";
            $data1 = $this->db->query($query1)->result();
             foreach ($data1 as $res)
            {
             
               if ($res->type == 1)
                    $driversArrIos[] = $res->push_token;
                else if ($res->type == 2)
                    $driversArrAndroid[] = $res->push_token;
            }
           
            
            
        } else {
            
             $query = "select * from master where mas_id in ('". $User_id ."')";
            $msg = "Driver";
            $data = $this->db->query($query)->result();
            foreach ($data as $res)
            {
//                print_r($res);
//                $driversArrAndroid[] = $res->push_token;
                $User_ids[] = $res->mas_id;
            }
//            print_r($User_ids);
            
            $d = $users = implode(',', array_filter(array_unique($User_ids)));
        
            $query1 = "select * from user_sessions where oid in ('" . $d . "') and user_type = 1 and loggedIn = 1";
            $data1 = $this->db->query($query1)->result();
           
            foreach ($data1 as $res)
            {
             
               if ($res->type == 1)
                    $driversArrIos[] = $res->push_token;
                else if ($res->type == 2)
                    $driversArrAndroid[] = $res->push_token;
            }
           
            
            if (empty(array_filter($driversArrAndroid)) && empty(array_filter($driversArrIos))) {// || empty(array_filter($driversArrIos))) {
                echo json_encode(array('flag' => 2, 'msg' =>'No user found'));
                return;
            }
        }
        
         $aplTokenArr = array_values(array_filter(array_unique($driversArrIos)));
            $andiTokenArr = array_values(array_filter(array_unique($driversArrAndroid)));
//            
       
//          foreach ($driversArrAndroid as $val) {
//            if (!in_array($val,$array)) {
//                $array[] = $val;
//            }
//        }

//        $driversArrAndroid = $array;
        
        $data = $this->superadminmodal->senPushToDriver($aplTokenArr, $andiTokenArr, $message,$city_id, $usertype,$User_ids);
        if ($data['errorNo'] == 44)
            echo json_encode(array('count' => $data['count'],'user_id'=>$User_ids,'IOS'=>$data['test1'], 'msg' => $msg, 'flag' => 1, 'err' => $data['err'], 'array' => $data));
        else
            echo json_encode(array('count' => $data['count'], 'dataFrompush' => $data, 'user_id'=>$User_ids, 'flag' => 3, 'err' => "Something went wrong.", 'array' => array('and' => $driversArrAndroid, 'ios' => $driversArrIos)));
    }

    public function Dashboard() {
        $sessionsetornot = $this->superadminmodal->issessionset();
        if ($sessionsetornot) {
            $data['todaybooking'] = $this->superadminmodal->Getdashboarddata();
            $data['pagename'] = "company/Dashboard";
            $this->load->view("company", $data);
        } else {
            redirect(base_url() . "index.php/superadmin");
        }
    }

    function datatable() {
        $this->superadminmodal->datatable();
    }

    public function Transection() {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data['gat_way'] = "2";
        $data['pagename'] = "company/Transection";

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

//        $this->table->set_heading('Booking Id', 'Driver name', 'Driver Id', 'Date', 'Sub Total (' . currency . ')-C.T', 'Total Fare (' . currency . ')-T.F', 'Discount (' . currency . ')-D', 'App commission (' . currency . ')  -   A.C = (C.T *A.C.P)+D.F', 'Payment Gateway commission (' . currency . ')-P.G.C', 'App Expences - A.Ex=P.G.C+D', 'App Earnings - (A.C-AEx)', 'Tip (' . currency . ')', 'Driver Earnings', 'Booking Status', 'Payment Type','Trip Details','Download');
        $this->table->set_heading('Booking Id', 'Driver name', 'Driver Id', 'Date', 'Total Fare (' . currency . ')-T.F','SubTotal(' . currency . ')', 'Discount (' . currency . ')-D', 'App commission (' . currency . ')  -   A.C = (C.T *A.C.P)+D.F', 'Payment Gateway commission (' . currency . ')-P.G.C', 'Tip (' . currency . ')', 'Driver Earnings', 'Booking Status', 'Payment Type','Trip Details','Download');
//        $this->table->set_heading('Booking Id', 'Business name', 'Driver name', 'Driver Id', 'Date', 'Total Fare ("' . currency . '")-(T.F)', 'Discount', 'App commission (%)', 'App commission (' . currency . ')','App Earnings', 'Payment Gateway commission (' . currency . ')', 'Store Earnings', 'Delivery Fee (' . currency . ')', 'Transaction Id', 'Booking Status', 'Payment Type', 'Download');

        $this->load->view("company", $data);
//        } else {
//            redirect(base_url() . "index.php/admin");
//        }
    }

    // driver wallet

    public function DriverRecharge() {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data['gat_way'] = "2";
        $data['pagename'] = "company/DriverRechargeList";
        $this->load->library('Datatables');
        $this->load->library('table');
        $this->table->clear();

        $tmpl = array('table_open' => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="table table-hover demo-table-search dataTable no-footer" id="tableWithSearch" role="grid" aria-describedby="tableWithSearch_info" style="margin-top: 30px;">',
            'heading_row_start' => '<tr style= "font-size:20px"role="row">',
            'heading_row_end' => '</tr>',
            'heading_cell_start' => '<th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 127px;">',
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
        $this->table->set_heading('SLNO', 'DRIVER ID', 'DRIVER NAME', 'CURRENT BALANCE', 'LAST RECHARGE DATE', 'OPERATION');
        $this->load->view("company", $data);
    }

    public function DriverRechargeStatement($id) {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        $data['driverId'] = $id;
        $data['driverinfo'] = $this->superadminmodal->GetDriverDetils($id);
        $data['pagename'] = "company/DriverRechargeStatement";
        $this->load->library('Datatables');
        $this->load->library('table');


        $this->table->clear();
        $tmpl = array('table_open' => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="table table-hover demo-table-search dataTable no-footer" id="tableWithSearch" role="grid" aria-describedby="tableWithSearch_info" style="margin-top: 30px;">',
            'heading_row_start' => '<tr style= "font-size:20px"role="row">',
            'heading_row_end' => '</tr>',
            'heading_cell_start' => '<th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 127px;">',
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
        $this->table->set_heading('OPENING BALANCE', 'BOOKINGID', 'COMISSION', 'COLSING BALANCE');
        $this->load->view("company", $data);
    }

    public function DriverRechargeStatement_ajax($param) {
        $this->superadminmodal->DriverRechargeStatement($param);
    }

    public function Recharge($id) {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data['driverId'] = $id;
        $data['driverinfo'] = $this->superadminmodal->GetDriverDetils($id);
        $data['pagename'] = "company/DriverRechargeDetails";
        $this->load->library('Datatables');
        $this->load->library('table');
        $this->table->clear();
        $tmpl = array('table_open' => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="table table-hover demo-table-search dataTable no-footer" id="tableWithSearch" role="grid" aria-describedby="tableWithSearch_info" style="margin-top: 30px;">',
            'heading_row_start' => '<tr style= "font-size:20px"role="row">',
            'heading_row_end' => '</tr>',
            'heading_cell_start' => '<th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 127px;">',
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
        $this->table->set_heading('SLNO', 'RECHARGE AMOUNT', 'RECHARGE DATE', 'OPERATION');
        $this->load->view("company", $data);
    }

    public function DriverRechargeDetails_ajax($param) {

        $this->superadminmodal->DriverRechargeDetails($param);
    }

    public function RechargeOperation($for, $id, $masid = '') {

        $data = $this->superadminmodal->RechargeOperation($for, $id, $masid);
        if ($data == 44)
            redirect(base_url() . "index.php/superadmin/Recharge/" . $masid);
    }

    public function GetRechargedata_ajax($param) {

        $this->superadminmodal->GetRechargedata_ajax();
    }

    // end driver wallet
    //* my controllers name is naveena *//


    public function showAvailableCities() {


        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        $data = $this->superadminmodal->loadAvailableCity();
        echo json_encode($data);
    }

    public function validateCompanyEmail() {


        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        return $this->superadminmodal->validateCompanyEmail();
    }

    function dt_passenger($status) {


        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $this->superadminmodal->dt_passenger($status);
    }

    public function editdispatchers_city() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        $data['editingcity'] = $this->superadminmodal->editdispatchers_city();
    }

    public function datatable_cities() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        $this->superadminmodal->datatable_cities();
    }

    public function datatable_companys($status) {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $this->superadminmodal->datatable_companys($status);
    }

    public function datatable_vehicletype() {


        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $this->superadminmodal->datatable_vehicletype();
    }
   

    public function datatable_vehicles($status) {


        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        $this->superadminmodal->datatable_vehicles($status);
    }

    public function datatable_driver($status) {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $this->superadminmodal->datatable_driver($status);
    }

    public function datatable_dispatcher($status) {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $this->superadminmodal->datatable_dispatcher($status);
    }

    public function datatable_document($status = '') {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $this->superadminmodal->datatable_document($status);
    }

    public function datatable_driverreview($status) {


        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $this->superadminmodal->datatable_driverreview($status);
    }

    public function datatable_bookings($status) {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $this->superadminmodal->datatable_bookings($status);
    }

    public function datatable_compaigns($status) {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        $this->superadminmodal->datatable_compaigns($status);
    }

    public function datatable_promodetails($id = '') {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        $this->superadminmodal->datatable_promodetails($id);
    }

    
    public function get_appointmentDetials() {
        $this->superadminmodal->get_appointmentDetials();
    }
    
    public function CompleteBooking() {
        $this->superadminmodal->CompleteBooking();
    }
    public function cities() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data['country'] = $this->superadminmodal->get_country();

        $data['city_list'] = $this->superadminmodal->get_city();

//        print_r($data);
//       exit();


        $this->load->library('Datatables');
        $this->load->library('table');

        $tmpl = array('table_open' => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="table table-hover demo-table-search dataTable no-footer" id="tableWithSearch" role="grid" aria-describedby="tableWithSearch_info" style="margin-top: 30px;">',
            'heading_row_start' => '<tr style= "font-size:20px"role="row">',
            'heading_row_end' => '</tr>',
            'heading_cell_start' => '<th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 127px;">',
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


        $this->table->set_heading('COUNTRY', 'CITY', 'LATITUDE', 'LONGITUDE', 'SELECT');

        $data['pagename'] = "company/cities";


        $this->load->view("company", $data);
    }

//    public function pagination() {
//       $this->load->view('bookings',$data);
//    }


    public function showcities() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data = $this->superadminmodal->loadcity();
        echo json_encode($data);
    }

    public function logoutdriver() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data = $this->superadminmodal->logoutdriver();
    }

    public function showcompanys() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data = $this->superadminmodal->loadcompany();
        $vt = $this->input->post('vt');

        if ($vt == '1')
            $this->session->set_userdata(array('city_id' => $this->input->post('city')));

        $return = "<option value='0'>Select Company ...</option><option value='0'>None</option>";

        foreach ($data as $city) {
            $return .= "<option value='" . $city['company_id'] . "'>" . $city['companyname'] . "</option>";
        }

        echo $return;
    }

    public function insertcities() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $this->superadminmodal->insert_city_available();
        return;
    }

    public function editlonglat() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data['edit'] = $this->superadminmodal->editlonglat();
    }

    public function addingcountry() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        return $this->superadminmodal->addcountry();
    }

    public function addingcity() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data['addingc'] = $this->superadminmodal->addcity();
    }

    public function addnewcity($status = "") {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data['status'] = $status;

        $data['country'] = $this->superadminmodal->get_country();

        $data['pagename'] = 'company/addnewcity';

        $this->load->view("company", $data);
    }

    public function add_edit($status = "", $param = '', $param2 = '') {


        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        $data['param'] = "";

        if ($status == 'edit') {
            $data['city_ram'] = $this->superadminmodal->city_sorted();
            $data['get_company_data'] = $this->superadminmodal->get_company_data($param);

            $data['status'] = $status;
            $data['param'] = $param;
             $data['pagename'] = 'company/company_edit';
        } elseif ($status == 'add') {
            $data['city_ram'] = $this->superadminmodal->city_sorted();
            $data['status'] = $status;
            $data['param'] = "";
             $data['pagename'] = 'company/add_edit';
        }


       

        $data['test'] = array('test' => 1);

        $this->load->view("company", $data);
    }

    public function activatecompany() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data = $this->superadminmodal->activate_company();
    }

    public function delete_dispatcher() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data['delete'] = $this->superadminmodal->delete_dispatcher();
    }

    public function suspendcompany() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data['suspend'] = $this->superadminmodal->suspend_company();
    }

    public function deactivatecompany() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data['deactivate'] = $this->superadminmodal->deactivate_company();
    }

    public function insertcompany() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $this->superadminmodal->insert_company();
        redirect(base_url() . "index.php/superadmin/company_s/1");
    }

    public function updatecompany($param = '') {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        $this->superadminmodal->update_company($param);
        redirect(base_url() . "index.php/superadmin/company_s/1");
    }

    public function company_s($status = '') {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data['status'] = $status;
        $data['city'] = $this->superadminmodal->city();
        $data['company'] = $this->superadminmodal->get_companyinfo($status);

        $this->load->library('Datatables');
        $this->load->library('table');

        $tmpl = array('table_open' => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="table table-hover demo-table-search dataTable no-footer" id="tableWithSearch" role="grid" aria-describedby="tableWithSearch_info" style="margin-top: 30px;">',
            'heading_row_start' => '<tr style= "font-size:20px"role="row">',
            'heading_row_end' => '</tr>',
            'heading_cell_start' => '<th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 127px;">',
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

        $this->table->set_heading('COMPANY ID', 'COMPANY NAME', 'ADDRESS', 'CITY NAME', 'STATE', 'POST CODE', 'FIRST NAME', 'LAST NAME', 'EMAIL', 'MOBILE', 'SELECT');

        $data['pagename'] = "company/company_s";
        $this->load->view("company", $data);
//        $this->load->view("cities");
    }
    
     public function vehicletype_reordering() {
        
        
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $this->superadminmodal->vehicletype_reordering();
    }
    
    public function testpush(){
     $this->superadminmodal->testpush();
    }
    
     public function vehicle_type() {
        
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        $data['vehicletype'] = $this->superadminmodal->get_vehicle_data();

        $this->load->library('Datatables');
        $this->load->library('table');

        $tmpl = array('table_open' => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="table table-hover demo-table-search dataTable no-footer" id="tableWithSearch" role="grid" aria-describedby="tableWithSearch_info" style="margin-top: 30px;">',
            'heading_row_start' => '<tr style= "font-size:20px"role="row">',
            'heading_row_end' => '</tr>',
            'heading_cell_start' => '<th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 127px;">',
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
        $this->table->set_heading('TYPE ID', 'TYPE NAME', 'MAX SIZE', 'BASE FARE', 'MIN FARE','WAITING CHARGE/MINUTE','CANCELLATION FEE', 'PRICE/MINUTE' . ' (' . currency . ')', 'PRICE/MILE' . ' (' . currency . ')', 'TYPE DESCRIPTION', 'CITY NAME','ON IMAGE','OFF IMAGE','MAP ICON','ORDERING','SELECT');

        $data['pagename'] = "company/vehicle_type";
        $this->load->view("company", $data);
//        $this->load->view("cities");
    }

    public function delete_vehicletype() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data['delete'] = $this->superadminmodal->delete_vehicletype();
    }

    public function activate_vehicle() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $this->superadminmodal->activate_vehicle();
    }

    Public function reject_vehicle() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $this->superadminmodal->reject_vehicle();
    }
    
     public function refered($code = '', $refCode = '', $page = 1) {

        $data['refered'] = $this->superadminmodal->refered($code, $refCode, $page);
//        print_r($data);
//        exit();

        $data['coupon_id'] = $code;

        $data['pagename'] = "company/refered";

        $this->load->view("company", $data);
    }

    public function inactivedriver_review() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data['driver_review'] = $this->superadminmodal->inactivedriver_review();
    }
    
    //Delete Passenger
     public function deletepassengers() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
       $this->superadminmodal->deletepassengers();
       
       
    }

    public function activedriver_review() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        $data['driver_review'] = $this->superadminmodal->activedriver_review();
    }

   public function vehicletype_addedit($status = '', $param = '') {
        
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        
        $data['param'] = "";
//        print($data);

        if ($status == 'edit') {
            $data['editvehicletype'] = $this->superadminmodal->edit_vehicletype($param);

            $data['status'] = $status;
            $data['param'] = $param;
        } elseif ($status == 'add') {
            $data['city'] = $this->superadminmodal->city();
            $data['status'] = $status;
            $data['param'] = "";
        }
        $data['pagename'] = "company/vehicletype_addedit";
        $data['city'] = $this->superadminmodal->get_city();
        $this->load->view("company", $data);
    }

    public function editvehicle($status = '') {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $return['data'] = $this->superadminmodal->editvehicle($status);

        $return['vehId'] = $status;

        $return['pagename'] = "company/editvehicle";
        $this->load->view("company", $return);
    }

    
    public function insert_vehicletype() {
        
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data = $this->superadminmodal->insert_vehicletype();
        redirect(base_url() . "index.php/superadmin/vehicle_type");
    }

    public function update_vehicletype($param = '') {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data['updatevehicletype'] = $this->superadminmodal->update_vehicletype($param);
        redirect(base_url() . "index.php/superadmin/vehicle_type");
    }

    public function inactivedispatchers() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data = $this->superadminmodal->inactivedispatchers();
    }

    public function activedispatchers() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data = $this->superadminmodal->activedispatchers();
    }
    
    public function deletedispatchers() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data = $this->superadminmodal->deletedispatchers();
    }

    public function editdispatchers() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        $data['city'] = $this->superadminmodal->city();
        $data = $this->superadminmodal->editdispatchers();
    }

    public function insertdispatches() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data = $this->superadminmodal->insertdispatches();
    }

    public function editpass() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data = $this->superadminmodal->editpass();
    }

    public function editdriverpassword() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data = $this->superadminmodal->editdriverpassword();
    }

    public function editsuperpassword() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data = $this->superadminmodal->editsuperpassword();
    }

    public function passengers($status = '') {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        $data['status'] = $status;
        $data['passenger_info'] = $this->superadminmodal->get_passengerinfo($status);


        $this->load->library('Datatables');
        $this->load->library('table');

        $tmpl = array('table_open' => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="table table-hover demo-table-search dataTable no-footer" id="tableWithSearch" role="grid" aria-describedby="tableWithSearch_info" style="margin-top: 30px;">',
            'heading_row_start' => '<tr style= "font-size:20px"role="row">',
            'heading_row_end' => '</tr>',
            'heading_cell_start' => '<th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 127PX;">',
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
        $this->table->set_heading('PASSENGER ID', 'FIRST NAME', 'LAST NAME', 'MOBILE', 'EMAIL', 'REGISTRATION DATE', 'PROFILE IMAGE', 'DEVICE TYPE', 'SELECT');

        //print_r($data);
        $data['pagename'] = "company/passengers";
        $this->load->view("company", $data);
    }

    public function acceptdrivers() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data['result'] = $this->superadminmodal->acceptdrivers();
    }
    //Manually logout the driver from admin panel
     public function driver_logout() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
    
        $data['result'] = $this->superadminmodal->driver_logout();
    }

       public function getdrivervehicle() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data['result'] = $this->superadminmodal->getdrivervehicle();
    }
    
    public function rejectdrivers() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $this->superadminmodal->rejectdrivers();
    }

    public function inactivepassengers() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data['inactive'] = $this->superadminmodal->inactivepassengers();
    }

    public function activepassengers() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data['inactive'] = $this->superadminmodal->activepassengers();
    }

    public function insertpass() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data['pass'] = $this->superadminmodal->insertpass();
//        print_r($res);
    }

    public function Vehicles($status = '') {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data['company'] = $this->superadminmodal->company_data();
        $data['vehicles'] = $this->superadminmodal->Vehicles($status);


        $this->load->library('Datatables');
        $this->load->library('table');

        $tmpl = array('table_open' => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="table table-hover demo-table-search dataTable no-footer" id="tableWithSearch" role="grid" aria-describedby="tableWithSearch_info" style="margin-top: 30px;">',
            'heading_row_start' => '<tr style= "font-size:20px"role="row">',
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

        $this->table->set_heading('VEHICLE ID', 'VEHICLE MAKE', 'VEHICLE MODEL', 'TYPE NAME','DRIVER ID','DRIVER NAME','DRIVER EMAIL','COMPANY NAME', 'VEHICLE  REGISTRATION NUMBER', 'LICENSE PLATE NUMBER', 'VEHICLE INSURANCE NUMBER', 'VEHICLE COLOR', 'CITY', 'SELECT');


        $data['pagename'] = 'company/vehicles';
        $data['status'] = $status;
        $this->load->view("company", $data);
    }

    public function deletecompany() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data = $this->superadminmodal->deletecompany();
    }

    public function datatable_drivers($for = '', $status = '') {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data = $this->superadminmodal->datatable_drivers($for, $status);
    }

    public function deletecountry() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data = $this->superadminmodal->deletecountry();
    }

    public function deletepagecity() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data = $this->superadminmodal->deletepagecity();

        print_r($data);
        exit();
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
            'heading_cell_start' => ' <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 127px;">',
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

        $this->table->set_heading('DRIVER ID', 'FIRST NAME', 'LAST NAME', 'MOBILE', 'EMAIL','VEHICLE ID', 'REG DATE','AVG RATING','COMPANY','PROFILE PIC','DEVICE TYPE','LATITUDE AND LONGITUDE','SELECT');


        $data['pagename'] = 'company/drivers';
        $this->load->view("company", $data);
    }

    public function addnewvehicle() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        $data['company'] = $this->superadminmodal->get_company();
        $data['pagename'] = 'company/addnewvehicle';

        $this->load->view("company", $data);
    }

//    public function addnewvehicles() {
//
//        $data['addnewvehicle'] = $this->superadminmodal->insert_addnewvehicles();
//    }

    public function addnewdriver() {


        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }


        $data['pagename'] = 'company/addnewdriver';

        $this->load->view("company", $data);
    }

    public function editdriver($status = '') {


        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $return['data'] = $this->superadminmodal->editdriver($status);
//        
//         print_r($return);
//         exit();
        $return['driverid'] = $status;



        $return['pagename'] = 'company/editdriver';

        $this->load->view("company", $return);
    }

    public function editNewVehicleData() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }


        $this->superadminmodal->editNewVehicleData();
        redirect(base_url() . "index.php/superadmin/Vehicles/5");
    }

    //* my controllers name is naveena *//

    public function transection_data_ajax() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        $this->superadminmodal->getTransectionData();
    }

    public function transection_data_form_date($stdate = '', $enddate = '', $status = '', $company_id = '') {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $this->superadminmodal->transection_data_form_date($stdate, $enddate, $status, $company_id);
    }

    public function callExel($stdate = '', $enddate = '') {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $data = $this->superadminmodal->get_all_data($stdate, $enddate);

//        print_r( array (new ArrayObject (array ('name'=> 'ashish','call' => '123') )) );
        $this->excel->stream('Transaction.xls', $data);
    }

    public function deleteDrivers() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        $this->superadminmodal->deletedriver();
    }

    public function callExel_payroll() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $value = $this->adminmodel->payroll();
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

    public function bookings($status = '') {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data['status'] = $status;

        $data['pagename'] = "company/bookings";
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

        $this->table->set_heading('BOOKING ID','BOOKING TYPE','DRIVER ID', 'DRIVER NAME', 'PASSENGER NAME', 'PICKUP ADDRESS', 'DROP ADDRESS','RECEIVED TIME', 'PICKUP TIME & DATE', 'DISTANCE(IN MILES)', 'STATUS');
        $this->load->view("company", $data);
    }

    public function RediousPrice() {
        
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        $data['rediousPrices'] = $this->superadminmodal->getRediousPrice();
        $data['pagename'] = "company/RediousPrice";
        $this->load->view("company", $data);
    }

    public function addRediousPrice() {
        
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        } 
        
        $from_ = $this->input->post('from_');
        $to_ = $this->input->post('to_');
        $price = $this->input->post('price');
        $cityid = $this->input->post('cityid');

//        $data['rediousPrices'] = 
        $this->superadminmodal->addRediousPrice($from_, $to_, $price, $cityid);
//        $data['pagename'] = "company/RediousPrice";
//        $this->load->view("company", $data);
    }

    public function editRediousPrice() {
        
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        $mid = $this->input->post('mid');
        $status = $this->input->post('status');
        if ($status == 'del') {
            $this->superadminmodal->DeleteRediousPrice($mid);
        } else {
            $from_ = $this->input->post('from_');
            $to_ = $this->input->post('to_');
            $price = $this->input->post('price');
            $cityid = $this->input->post('cityid');

//        $data['rediousPrices'] =
            $this->superadminmodal->editRediousPrice($from_, $to_, $price, $mid, $cityid);
        }
//        $data['pagename'] = "company/RediousPrice";
//        $this->load->view("company", $data);
    }

    public function bookings_data_ajax($status = '', $comapnyid = '') {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $this->superadminmodal->getbooking_data($status, $comapnyid);
    }

    public function dispatched($status = '') {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data['status'] = $status;
        $data['city'] = $this->superadminmodal->city();
        $data['getdata'] = $this->superadminmodal->get_dispatchers_data($status);

        $this->load->library('Datatables');
        $this->load->library('table');
        $tmpl = array('table_open' => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="table table-hover demo-table-search dataTable no-footer" id="tableWithSearch" role="grid" aria-describedby="tableWithSearch_info" style="margin-top: 30px;">',
            'heading_row_start' => '<tr role="row">',
            'heading_row_end' => '</tr>',
            'heading_cell_start' => '<th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 50px;font-size:14px">',
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

        $this->table->set_heading('DISPATCHER ID', 'CITY', 'EMAIL', 'DISPATCHER NAME', 'OPTION');




        $data['pagename'] = "company/dispatched";

        $this->load->view("company", $data);
    }

    public function finance($status = '') {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data['status'] = $status;
        $data['pagename'] = "company/finance";
        $this->load->view("company", $data);
    }

    public function joblogs($value = '') {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data['value'] = $value;
        $data['joblogs'] = $this->superadminmodal->get_joblogsdata($value);
//        
//        print_r($data);
//        exit();
        $data['pagename'] = "company/joblogs";
        $this->load->view("company", $data);
    }

    public function sessiondetails($value = '') {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data['value'] = $value;
        $data['session_details'] = $this->superadminmodal->get_sessiondetails($value);

        $data['pagename'] = "company/sessiondetails";
        $this->load->view("company", $data);
    }

    public function document($status = '') {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        $data['status'] = $status;

        $data['master'] = $this->superadminmodal->driver();

        $data['document_data'] = $this->superadminmodal->get_documentdata($status);

        $data['workname'] = $this->superadminmodal->get_workplace();


        $data['pagename'] = "company/document";
        $this->load->view("company", $data);
    }

    public function passenger_rating() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        $data['passenger_rating'] = $this->superadminmodal->passenger_rating();



        $this->load->library('Datatables');
        $this->load->library('table');

        $tmpl = array('table_open' => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="table table-hover demo-table-search dataTable no-footer" id="tableWithSearch" role="grid" aria-describedby="tableWithSearch_info" style="margin-top: 30px;">',
            'heading_row_start' => '<tr style= "font-size:20px"role="row">',
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

        $this->table->set_heading('PASSENGER ID', 'PASSENGER NAME', 'PASSENGER EMAIL', 'AVG RATING');


        $data['pagename'] = "company/passenger_rating";
        $this->load->view("company", $data);
    }

    public function datatable_passengerrating() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        $this->superadminmodal->datatable_passengerrating();
    }

    public function getmap_values() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        $this->superadminmodal->getmap_values();
    }

    public function driver_review($status = '') {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data['status'] = $status;
        $data['driver_review'] = $this->superadminmodal->driver_review($status);

        $this->load->library('Datatables');
        $this->load->library('table');

        $tmpl = array('table_open' => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="table table-hover demo-table-search dataTable no-footer" id="tableWithSearch" role="grid" aria-describedby="tableWithSearch_info" style="margin-top: 30px;">',
            'heading_row_start' => '<tr style= "font-size:20px"role="row">',
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

        $this->table->set_heading('BOOKING ID', 'BOOKING DATE AND TIME', 'DRIVER NAME', 'PASSENGER ID', 'REVIEW', 'RATING', 'SELECT');


        $data['pagename'] = "company/driver_review";
        $this->load->view("company", $data);
    }

    public function disputes($status = '') {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data['status'] = $status;
        $data['city'] = $this->superadminmodal->get_city();
        $data['disputesdata'] = $this->superadminmodal->get_disputesdata($status);
        $data['master'] = $this->superadminmodal->driver();
        $data['slave'] = $this->superadminmodal->passenger();



        $this->load->library('Datatables');
        $this->load->library('table');

        $tmpl = array('table_open' => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="table table-hover demo-table-search dataTable no-footer" id="tableWithSearch" role="grid" aria-describedby="tableWithSearch_info" style="margin-top: 30px;">',
            'heading_row_start' => '<tr style= "font-size:20px"role="row">',
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

        $this->table->set_heading('DISPUTE ID', 'PASSENGER ID', 'PASSENGER NAME', 'DRIVER ID', 'DRIVER NAME', 'DISPUTE MESSAGE', 'DISPUTE DATE', 'BOOKING ID', 'SELECT');



        $data['pagename'] = "company/disputes";
        $this->load->view("company", $data);
    }

    public function datatable_disputes($status) {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        $this->superadminmodal->datatable_disputes($status);
    }

    public function documentgetdata() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        echo json_encode($this->superadminmodal->documentgetdata());
        exit();
    }

    public function documentgetdatavehicles() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        echo json_encode($this->superadminmodal->documentgetdatavehicles());
        exit();
    }

    public function resolvedisputes() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data = $this->superadminmodal->resolvedisputes();
    }

    public function delete() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        $data['getvehicletype'] = $this->superadminmodal->get_vehivletype();
        $data['getcompany'] = $this->superadminmodal->get_company();
        $data['city_ram'] = $this->superadminmodal->city_sorted();
        $data['driver'] = $this->superadminmodal->get_driver();
        $data['vehiclemodal'] = $this->superadminmodal->vehiclemodal();
        $data['country'] = $this->superadminmodal->get_country();
//          print_r($data['getvehicletype']);


        $data['pagename'] = "company/delete";

        $this->load->view("company", $data);
    }

    public function deactivecompaigns() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data['deactivate'] = $this->superadminmodal->deactivecompaigns();
    }

    public function deletetype() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data = $this->superadminmodal->deletetype();
    }

    public function godsview() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data['pagename'] = "company/godsview";
        $data['cities'] = $this->superadminmodal->get_cities();
        $this->load->view("company", $data);
    }

    

    public function getDtiverDetail() {
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $this->superadminmodal->getDtiverDetail();
    }

   //    ajax call for getting Driver arround customer
    public function getDtiversArround() {


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

//print_r($resultArr);
        $driversArr = array();


//        $statusColors = array("3" => "green.png", "6" => 'blue.png', "7" => 'yellow.png', "8" => 'red.png');

        foreach ($resultArr['results'] as $res) {
            $doc = $res['obj'];
            $dis = $res['dis'];

            $iconPath = ServiceLink . '/images/';
            $switch = ($doc['status'] != 3) ? (int) $doc['apptStatus'] : 3;
            $ico = $iconPath . "Vehicle_";
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

//  print_r($driversArr);

        echo json_encode(array('result' => $driversArr));
    }

    public function refreshMap($param = '') {
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
            $iconPath = ServiceLink . '/images/';
            $switch = ($doc['status'] != 3) ? (int) $doc['apptStatus'] : 3;
            $ico = $iconPath . "Vehicle_";
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

    public function get_vehicle_type() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }


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

    public function vehicle_models($status = '') {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data['status'] = $status;
        $data['vehiclemake'] = $this->superadminmodal->get_vehiclemake();

        if ($status == 1) {

            $this->load->library('Datatables');
            $this->load->library('table');

            $tmpl = array('table_open' => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="table table-hover demo-table-search dataTable no-footer" id="tableWithSearch" role="grid" aria-describedby="tableWithSearch_info" style="margin-top: 30px;">',
                'heading_row_start' => '<tr style= "font-size:20px"role="row">',
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


            $this->table->set_heading('ID', 'TYPE NAME', 'SELECT');
        } else if ($status == 2) {

            $this->load->library('Datatables');
            $this->load->library('table');

            $tmpl = array('table_open' => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="table table-hover demo-table-search dataTable no-footer" id="tableWithSearch" role="grid" aria-describedby="tableWithSearch_info" style="margin-top: 30px;">',
                'heading_row_start' => '<tr style= "font-size:20px"role="row">',
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

            $this->table->set_heading('ID', 'MAKE', 'MODEL', 'SELECT');
        }


        $data['pagename'] = "company/vehicle_models";
        $this->load->view("company", $data);
    }

    function datatable_vehiclemodels($status = '') {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }


        $this->load->library('Datatables');
        $this->load->library('table');

        if ($status == 1) {

            $this->datatables->select("id,vehicletype")
                    ->add_column('select', '<input type="checkbox" class="checkbox" name="checkbox" value="$1"/>', 'id')
                    ->from("vehicleType"); //order by slave_id DESC ",false);
        } else if ($status == 2) {


            $this->datatables->select("vm.id,vt.vehicletype,vm.vehiclemodel")
                    ->add_column('select', '<input type="checkbox" class="checkbox" name="checkbox" value="$1"/>', 'vm.id')
                    ->from("vehiclemodel vm,vehicleType vt")
                    ->where("vm.vehicletypeid = vt.id"); //order by slave_id DESC ",false);
        }

        echo $this->datatables->generate();
    }

    public function inserttypename() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data = $this->superadminmodal->insert_typename();
    }

    public function insertmodal() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data = $this->superadminmodal->insert_modal();
    }

    public function deletevehicletype() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data = $this->superadminmodal->deletevehicletype();
    }

    public function delete_company() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data = $this->superadminmodal->delete_company();
    }

    public function deletevehiclemodal() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data = $this->superadminmodal->deletevehiclemodal();
    }

    public function deletevehicletypemodel() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data = $this->superadminmodal->deletevehicletypemodel();
    }

    public function deletedriver() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data = $this->superadminmodal->deletedriver();
    }

    public function deletemodal() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data = $this->superadminmodal->deletemodal();
    }

    public function promo_details($id = '') {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $this->load->library('Datatables');
        $this->load->library('table');

        $tmpl = array('table_open' => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="table table-hover demo-table-search dataTable no-footer" id="tableWithSearch" role="grid" aria-describedby="tableWithSearch_info" style="margin-top: 30px;">',
            'heading_row_start' => '<tr style= "font-size:20px"role="row">',
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

        $this->table->set_heading('INVOICE VALUE', 'DISCOUNT', 'VALUE AFTER DISCOUNT', 'USED ON', 'BOOKING ID', 'CUSTOMER ID', 'CUSTOMER EMAIL');

        $data['pagename'] = "company/promo_details";
        $data['mid'] = $id;
        $this->load->view("company", $data);
    }

    public function referral_details($id = '', $page = 1) {

        $data['referral_details'] = $this->superadminmodal->get_referral_details($id, $page);

        $data['coupon_id'] = $id;

        $data['pagename'] = "company/referral_details";

        $this->load->view("company", $data);
    }

    public function compaigns($status = '') {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data['status'] = $status;
        $data['city'] = $this->superadminmodal->get_city();
        $data['compaign'] = $this->superadminmodal->get_compaigns_data($status);

        $data['pagename'] = "company/compaigns";
        $this->load->view("company", $data);
    }

    public function compaigns_ajax($for = '') {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        $this->superadminmodal->get_compaigns_data_ajax($for);
    }

    public function insertcompaigns() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        echo $this->superadminmodal->insertcampaigns();
    }

    public function updatecompaigns() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        echo $this->superadminmodal->updatecompaigns();
    }

    public function editcompaigns() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $this->superadminmodal->editcompaigns();
    }

    public function cancled_booking() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data['pagename'] = "company/cancled_booking";
        $this->load->view("company", $data);
//        $this->load->view("cities");
    }

    public function Get_dataformdate($stdate = '', $enddate = '') {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
//        $data = $this->superadminmodal->get_all_data();
        $data['transection_data'] = $this->superadminmodal->getDatafromdate($stdate, $enddate);
        $data['stdate'] = $stdate;
        $data['enddate'] = $enddate;
        $data['gat_way'] = '2';
        $data['pagename'] = "company/Transection";
        $this->load->view("company", $data);
    }

    public function Get_dataformdate_for_all_bookingspg($stdate = '', $enddate = '', $status = '', $company_id = '') {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $this->superadminmodal->getDatafromdate_for_all_bookings($stdate, $enddate, $status, $company_id);
    }

    public function search_by_select($selectdval = '') {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
//        $data['transection_data'] =
        $this->superadminmodal->getDataSelected($selectdval);
//        $data['pagename'] = "company/Transection";
//        $data['gat_way'] = $selectdval;
//        $this->load->view("company", $data);
    }

    public function profile() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $sessionsetornot = $this->superadminmodal->issessionset();
        if ($sessionsetornot) {
            $data['userinfo'] = $this->superadminmodal->getuserinfo();
            $data['pagename'] = "company/profile";
            $this->load->view("company", $data);
        } else {
            redirect(base_url() . "index.php/superadmin");
        }
    }

    public function services() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        $sessionsetornot = $this->superadminmodal->issessionset();
        if ($sessionsetornot) {

            $data['service'] = $this->superadminmodal->getActiveservicedata();
            $data['pagename'] = "company/Addservice";
            $this->load->view("company", $data);
        } else {
            redirect(base_url() . "index.php/superadmin");
        }
    }

    public function updateservices($tablename = '') {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        $this->superadminmodal->updateservices($tablename);
        redirect(base_url() . "index.php/superadmin/services");
    }

    function deleteservices($tablename = "") {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $this->superadminmodal->deleteservices($tablename);
        redirect(base_url() . "index.php/superadmin/services");
    }

    function Banking() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $sessionsetornot = $this->superadminmodal->issessionset();
        if ($sessionsetornot) {

//            $data['service'] = $this->superadminmodal->getActiveservicedata();
            $data['pagename'] = "company/banking";
            $this->load->view("company", $data);
        } else {
            redirect(base_url() . "index.php/superadmin");
        }
    }

    public function addservices() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        $data['service'] = $this->superadminmodal->addservices();
        redirect(base_url() . "index.php/superadmin/services");
    }

    public function booking() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $sessionsetornot = $this->madmin->issessionset();
        if ($sessionsetornot) {
            $data['bookinlist'] = $this->madmin->getPassangerBooking();
            $data['pagename'] = "booking";
            $this->load->view("index", $data);
        } else {
            redirect(base_url() . "index.php/superadmin");
        }
    }

    function Logout() {

        $this->session->sess_destroy();
        redirect(base_url() . "index.php/superadmin");
    }

    function udpadedataProfile() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $this->superadminmodal->updateDataProfile();

        if ($this->input->post('val')) {
            $filename = "demo.png";
            if (move_uploaded_file($_FILES['userfile']['tmp_name'], base_url() . 'files/' . $filename)) {
                echo $filename;
            }
        }
        redirect(base_url() . "index.php/superadmin/profile");
    }

    function udpadedata($IdToChange = '', $databasename = '', $db_field_id_name = '') {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        $this->madmin->updateData($IdToChange, $databasename, $db_field_id_name);
        redirect(base_url() . "index.php/superadmin/profile");
    }

    public function updateMasterBank() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
//        return;
        $ret = $this->superadminmodal->updateMasterBank();
        $data['error'] = $ret['flag'];
        $data['error_message'] = $ret['message'];
        $data['error_array'] = $ret;
        $data['userData'] = $ret['data'];
        $data['pagename'] = "master/banking";
        $this->load->view("master", $data);
    }

    public function payroll() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

//        $data['payroll']=$this->adminmodel->payroll();
        $data['pagename'] = 'company/payroll';

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

//        $this->table->set_heading('DRIVER ID', 'NAME', 'TODAY EARNINGS (' . currency . ')', 'WEEK EARNINGS (' . currency . ')', 'MONTH EARNINGS (' . currency . ')', 'LIFE TIME EARNINGS (' . currency . ')', 'PAID (' . currency . ')', 'DUE (' . currency . ')', 'SHOW');
//        $this->table->set_heading('DRIVER ID', 'NAME', 'CARD EARNINGS(' . currency . ')','CASH EARNINGS(' . currency . ')','PG COMISSION(' . currency . ')', 'APP EARNINGS (' . currency . ')', 'DRIVER EARNINGS (' . currency . ')','DUE AMOUNT (' . currency . ')', 'SHOW');
        $this->table->set_heading('DRIVER ID', 'NAME', 'CASH EARNINGS(' . currency . ')','TIP (' . currency . ')', 'CARD EARNINGS(' . currency . ')', 'DRIVER EARNINGS (' . currency . ')', 'CASH COLLECTED(' . currency . ')', 'TOTAL RECEIVED (' . currency . ')', 'DUE AMOUNT (' . currency . ')', 'SHOW');
        $this->load->view("company", $data);
    }

    public function payroll_ajax() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $this->superadminmodal->payroll();
    }

    public function payroll_data_form_date($stdate = '', $enddate = '', $company_id = '') {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $this->superadminmodal->payroll_data_form_date($stdate, $enddate, $company_id);
    }

    public function DriverDetails_form_Date($stdate = '', $enddate = '', $company_id = '', $mas_id = '') {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $this->superadminmodal->DriverDetails_form_Date($stdate, $enddate, $company_id, $mas_id);
    }

    public function Driver_pay($id = '',$error = '') {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        $data['error'] = $error['error'];
        
        $data['driverdata'] = $this->superadminmodal->Driver_pay($id);
        $data['payrolldata'] = $this->superadminmodal->get_payrolldata($id);
        $data['totalamountpaid'] = $this->superadminmodal->Totalamountpaid($id);
        $data['mas_id'] = $id;
        $data['pagename'] = 'company/driverpayment';
        $this->load->view("company", $data);
    }
   
    public function pay_driver_amount($id = '') {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
       $error = $this->superadminmodal->insert_payment($id);
        redirect(base_url() . "index.php/superadmin/Driver_pay/" . $id.'/'.$error);
    }

    public function validateEmail() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        return $this->superadminmodal->validateEmail();
    }

    public function validatedispatchEmail() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        return $this->superadminmodal->validatedispatchEmail();
    }
    
      //Get the all On-Going jobs by filtered city
    public function filter_AllOnGoing_jobs() {
        
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        $this->superadminmodal->filter_AllOnGoing_jobs();

    }
        //Get the All On Going Jobs
     public function datatable_onGoing_jobs() {
        
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
       
        $this->superadminmodal->datatable_onGoing_jobs();
    }

    //Set the Table header/columns for On Going Jobs
    public function onGoing_jobs($status = '') {
        
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

//        $this->load->library('Datatables');
//        $this->load->library('table');
//        $data['status'] = $status;
//
//        $tmpl = array('table_open' => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="table table-hover demo-table-search dataTable no-footer" id="tableWithSearch" role="grid" aria-describedby="tableWithSearch_info" style="margin-top: 30px;">',
//            'heading_row_start' => '<tr style= "font-size:20px"role="row">',
//            'heading_row_end' => '</tr>',
//            'heading_cell_start' => ' <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 127px;">',
//            'heading_cell_end' => '</th>',
//            'row_start' => '<tr>',
//            'row_end' => '</tr>',
//            'cell_start' => '<td>',
//            'cell_end' => '</td>',
//            'row_alt_start' => '<tr>',
//            'row_alt_end' => '</tr>',
//            'cell_alt_start' => '<td>',
//            'cell_alt_end' => '</td>',
//            'table_close' => '</table>'
//        );
//        $this->table->set_template($tmpl);
//       
//           $this->table->set_heading('BOOKING ID', 'DRIVER ID', 'DRIVER NAME', 'CUSTOMER ID', 'CUSTOMER NAME', 'PICKUP ADDRESS', 'RECEIVED BOOKING TIME','STATUS');
      $data['ongoing_booking'] = $this->superadminmodal->get_ongoing_bookings();
        $data['pagename'] = 'company/onGoing_jobs';
        $this->load->view("company", $data);
    }
    
    public function getOngoingBookingAjax(){
        $data =  $this->superadminmodal->get_ongoing_bookings();
        echo json_encode(array('data' =>$data));
    }

      //Get the all Completed jobs by filtered city
      public function filter_Allcompleted_jobs() {
        
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        $this->superadminmodal->filter_Allcompleted_jobs();
    }
    //Get the All Coppleted Jobs
    public function datatable_completed_jobs() {
        
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
       
        $this->superadminmodal->datatable_completed_jobs();
    }

    //Set the Table header/columns for All Completed Jobs
    public function completed_jobs($status = '') {
        
        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        $this->load->library('Datatables');
        $this->load->library('table');
        $data['status'] = $status;

        $tmpl = array('table_open' => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="table table-hover demo-table-search dataTable no-footer" id="tableWithSearch" role="grid" aria-describedby="tableWithSearch_info" style="margin-top: 30px;">',
            'heading_row_start' => '<tr style= "font-size:20px"role="row">',
            'heading_row_end' => '</tr>',
            'heading_cell_start' => ' <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 127px;">',
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
       
           $this->table->set_heading('BOOKING ID', 'DRIVER ID', 'DRIVER NAME', 'CUSTOMER ID', 'CUSTOMER NAME', 'PICKUP ADDRESS',  'RECEIVED BOOKING TIME','STATUS','TRIP DETAILS');
    
        $data['pagename'] = 'company/completed_jobs';
        $this->load->view("company", $data);
    }
    
//    //Shows the Each Job Details
//    public function showJob_details($param)
//    {
//        if ($this->session->userdata('table') != 'company_info') {
//            $this->Logout();
//        }
//        
//         $return['data'] = $this->superadminmodal->tripDetails($param);
//        
//        $return['pagename'] = 'company/showJob_details';
//        $this->load->view("company", $return);
//        
//    }


    public function AddNewDriverData() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $this->superadminmodal->AddNewDriverData();
        redirect(base_url() . "index.php/superadmin/Drivers/my/1");
    }

    public function editdriverdata() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $this->superadminmodal->editdriverdata();
        redirect(base_url() . "index.php/superadmin/Drivers/my/1");
    }

    public function AddNewVehicleData() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $this->superadminmodal->AddNewVehicleData();
        redirect(base_url() . "index.php/superadmin/Vehicles/5");
    }

    public function DriverDetails($mas_id = '') {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
//        $data['driverdetails'] = $this->superadminmodal->DriverDetails($mas_id);
        $data['pagename'] = 'company/driverDetails';
        $data['mas_id'] = $mas_id;
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

        $this->table->set_heading('BOOKING ID', 'CUSTOMER NAME', 'CUSTOMER PAID (' . currency . ')', 'APP COMMISSION (' . currency . ')', 'PAYMENT GATEWAY COMM. (' . currency . ')','TIP (' . currency . ')','DRIVER EARNING (' . currency . ')');

        $this->load->view("company", $data);
    }

    public function DriverDetails_ajax($mas_id = '') {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $this->superadminmodal->DriverDetails($mas_id);
    }

    public function deletecities() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }
        $data['del'] = $this->superadminmodal->deletecity();
    }

    public function testmon($email,$firstname) {

        $this->superadminmodal->testmon($email,$firstname);
    }
    public function testMailGun() {

        $this->superadminmodal->mailGunTest();
    }

    public function deleteVehicles() {

        if ($this->session->userdata('table') != 'company_info') {
            $this->Logout();
        }

        $driverlist = $this->input->post('val');
        $this->load->database();
        $this->load->library('mongo_db');
        foreach ($driverlist as $result) {


            $affectedRows = 0;

            $selectCars = $this->db->query("select appointment_id from appointment where car_id = '" . $result . "'")->result_array();

            $apptIDs = array();
            foreach ($selectCars as $type) {
                $apptIDs[] = (int) $type['appointment_id'];
            }

            $masDet = $this->db->query("select mas_id from master where workplace_id= '" . $result . "'")->result_array();


            if (is_array($masDet)) {

                $db = $this->mongo_db->db;

                $location = $db->selectCollection('location');

                $location->update(array('carId' => (int) $result), array('$set' => array('type' => 0, 'carId' => 0, 'status' => 4)), array('multiple' => 1));

                $this->db->query("update master set type_id = 0 and workplace_id = 0 where mas_id = '" . $masDet[0]['mas_id'] . "'");
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

            $this->db->query("delete from user_sessions where user_type = 1 and oid = '" . $masDet[0]['mas_id'] . "'");
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
            $get_vechile_type_display = $this->db->query("select type_id,type_name from workplace_types where city_id = '" . $_REQUEST['city'] . "' ORDER BY type_name ASC")->result();
            echo "<option value=''>Select a type</option>";
            foreach ($get_vechile_type_display as $typelist) {
                echo "<option value='" . $typelist->type_id . "' id='" . $typelist->type_id . "' >" . $typelist->type_name . "</option>";
            }
        } else if ($param == 'vmodel') {
            $loupon_sql = $this->db->query("SELECT * FROM vehiclemodel where vehicletypeid = '" . $_REQUEST['adv'] . "' ORDER BY vehiclemodel ASC")->result();
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

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */