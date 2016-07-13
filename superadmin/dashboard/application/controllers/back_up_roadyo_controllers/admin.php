<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admin extends CI_Controller {

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
        $this->load->model("adminmodel");
        $this->load->library('session');
//        $this->load->library('excel');
        header("cache-Control: no-store, no-cache, must-revalidate");
        header("cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

    }

    public function index($loginerrormsg = NULL) {
        $data['loginerrormsg'] = $loginerrormsg;

        $this->load->view('admin/login', $data);
    }

        public  function setcity_session(){

                 $meta = array('city_id' => $this->input->post('city'),'company_id' => $this->input->post('company'));
                 $this->session->set_userdata($meta);
        }

    public function AuthenticateUser() {
        $email = $this->input->post("email");
        $password = $this->input->post("password");
        if ($email && $password) {


            $status = $this->adminmodel->ValidateSuperAdmin();

            if ($status) {
                if ($this->session->userdata('table') == 'company_info')
                    redirect(base_url() . "index.php/admin/Dashboard");
            } else {
                $loginerrormsg = "invalid email or password";
                $this->index($loginerrormsg);
            }
        } else
            redirect(base_url() . "index.php/admin");
    }

    public function uniq_val(){
     
       $this->adminmodel->uniq_val_chk();
    }

    
    
    public function Dashboard() {
        $sessionsetornot = $this->adminmodel->issessionset();
        if ($sessionsetornot) {
            $data['todaybooking'] = $this->adminmodel->Getdashboarddata();
            $data['pagename'] = "admin/Dashboard";
           $this->load->view("admin", $data);
        } else {
            redirect(base_url() . "index.php/admin");
        }
    }

    function datatable()
    {
$this->adminmodel->datatable();
    }


    public function Transection() {
        $sessionsetornot = $this->adminmodel->issessionset();
//        if ($sessionsetornot) {

            $data['gat_way'] = "2";
            $data['pagename'] = "admin/Transection";

        $this->load->library('Datatables');
        $this->load->library('table');
        $this->table->clear();
        $tmpl = array ( 'table_open'  => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="table table-hover demo-table-search dataTable no-footer" id="tableWithSearch" role="grid" aria-describedby="tableWithSearch_info" style="margin-top: 30px;">',
            'heading_row_start'   => '<tr role="row">',
            'heading_row_end'     => '</tr>',
            'heading_cell_start'  => '<th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 50px;">',
            'heading_cell_end'    => '</th>',

            'row_start'           => '<tr>',
            'row_end'             => '</tr>',
            'cell_start'          => '<td>',
            'cell_end'            => '</td>',

            'row_alt_start'       => '<tr>',
            'row_alt_end'         => '</tr>',
            'cell_alt_start'      => '<td>',
            'cell_alt_end'        => '</td>',

            'table_close'         => '</table>'
        );


        $this->table->set_template($tmpl);

        $this->table->set_heading('Booking Id','Driver Id','Date','Total Fare ("'.currency.'")','App commission ('.currency.')','Payment Gateway commission ('.currency.')','Driver Earning ('.currency.')','Transection Id','Booking Status','Payment Type','Download');

            $this->load->view("admin", $data);
//        } else {
//            redirect(base_url() . "index.php/admin");
//        }
    }




    //* my controllers name is naveena *//


    public function showAvailableCities() {

        $data = $this->adminmodel->loadAvailableCity();
        echo json_encode($data);
    }

    public function validateCompanyEmail() {

        return $this->adminmodel->validateCompanyEmail();
    }

    function dt_passenger($status) {
        $this->adminmodel->dt_passenger($status);
    }




    public function editdispatchers_city() {

        $data['editingcity'] = $this->adminmodel->editdispatchers_city();
    }

    public function datatable_cities() {

        $this->adminmodel->datatable_cities();
    }

    public function datatable_companys($status) {
        $this->adminmodel->datatable_companys($status);
    }

    public function datatable_vehicletype() {
        $this->adminmodel->datatable_vehicletype();
    }

    public function datatable_vehicles($status) {

        $this->adminmodel->datatable_vehicles($status);
    }

    public function datatable_driver($status) {
        $this->adminmodel->datatable_driver($status);
    }

    public function datatable_dispatcher($status) {
        $this->adminmodel->datatable_dispatcher($status);
    }

    public function datatable_document($status = '') {
        $this->adminmodel->datatable_document($status);
    }

    public function datatable_driverreview($status) {
        $this->adminmodel->datatable_driverreview($status);
    }

    public function datatable_bookings($status) {
        $this->adminmodel->datatable_bookings($status);
    }

    public function datatable_compaigns($status) {
        $this->adminmodel->datatable_compaigns($status);
    }

    public function cities() {
        $data['country'] = $this->adminmodel->get_country();

        $data['city_list'] = $this->adminmodel->get_city();

//        print_r($data);
//       exit();


        $this->load->library('Datatables');
        $this->load->library('table');

        $tmpl = array('table_open' => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="table table-hover demo-table-search dataTable no-footer" id="tableWithSearch" role="grid" aria-describedby="tableWithSearch_info" style="margin-top: 30px;">',
            'heading_row_start' => '<tr style= "font-size:20px"role="row">',
            'heading_row_end' => '</tr>',
            'heading_cell_start' => '<th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 127px;font-size: 14px;">',
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

        $data['pagename'] = "admin/cities";


        $this->load->view("admin", $data);
    }

//    public function pagination() {
//       $this->load->view('bookings',$data);
//    }


    public function showcities() {

        $data = $this->adminmodel->loadcity();
        echo json_encode($data);
    }

    public function logoutdriver() {
        $data = $this->adminmodel->logoutdriver();
    }

    public function showcompanys() {

        $data = $this->adminmodel->loadcompany();
        $vt = $this->input->post('vt');

        if($vt == '1')
        $this->session->set_userdata(array('city_id' => $this->input->post('city')));

        $return = "<option value='0'>Select Company ...</option><option value='0'>None</option>";

        foreach ($data as $city) {
            $return .= "<option value='" . $city['company_id'] . "'>" . $city['companyname'] . "</option>";
        }

        echo $return;
    }

    public function insertcities() {
        $this->adminmodel->insert_city_available();
        return;
    }

    public function editlonglat() {
        $data['edit'] = $this->adminmodel->editlonglat();
    }

    public function addingcountry() {
        return $this->adminmodel->addcountry();
    }

    public function addingcity() {
        $data['addingc'] = $this->adminmodel->addcity();
    }

    public function addnewcity($status = "") {
        $data['status'] = $status;

        $data['country'] = $this->adminmodel->get_country();

        $data['pagename'] = 'admin/addnewcity';

        $this->load->view("admin", $data);
    }

    public function add_edit($status = "", $param = '', $param2 = '') {

        $data['param'] = "";

        if ($status == 'edit') {
            $data['city'] = $this->adminmodel->city();
            $data['get_company_data'] = $this->adminmodel->get_company_data($param);

            $data['status'] = $status;
            $data['param'] = $param;
        } elseif ($status == 'add') {
            $data['city'] = $this->adminmodel->city();
            $data['status'] = $status;
            $data['param'] = "";
        }
        $data['pagename'] = 'admin/add_edit';

        $this->load->view("admin", $data);
    }

    public function activatecompany() {
        $data = $this->adminmodel->activate_company();
    }

    public function delete_dispatcher() {
        $data['delete'] = $this->adminmodel->delete_dispatcher();
    }

    public function suspendcompany() {
        $data['suspend'] = $this->adminmodel->suspend_company();
    }

    public function deactivatecompany() {
        $data['deactivate'] = $this->adminmodel->deactivate_company();
    }

    public function insertcompany() {
        $data['company'] = $this->adminmodel->insert_company();
    }

    public function updatecompany($param = '') {

        $data['updatecompany'] = $this->adminmodel->update_company($param);
    }

    public function company_s($status = '') {
        $data['status'] = $status;
        $data['city'] = $this->adminmodel->city();
        $data['company'] = $this->adminmodel->get_companyinfo($status);

        $this->load->library('Datatables');
        $this->load->library('table');

        $tmpl = array('table_open' => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="table table-hover demo-table-search dataTable no-footer" id="tableWithSearch" role="grid" aria-describedby="tableWithSearch_info" style="margin-top: 30px;">',
            'heading_row_start' => '<tr style= "font-size:20px"role="row">',
            'heading_row_end' => '</tr>',
            'heading_cell_start' => '<th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 127px;font-size: 14px;">',
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

        $this->table->set_heading('COMPANY ID', 'COMPANY NAME', 'ADDRESS LINE1', 'CITY NAME', 'STATE', 'POST CODE', 'FIRST NAME', 'LAST NAME', 'EMAIL', 'MOBILE', 'SELECT');

        $data['pagename'] = "admin/company_s";
        $this->load->view("admin", $data);
//        $this->load->view("cities");
    }

    public function vehicle_type() {

        $data['vehicletype'] = $this->adminmodel->get_vehicle_data();

        $this->load->library('Datatables');
        $this->load->library('table');

        $tmpl = array('table_open' => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="table table-hover demo-table-search dataTable no-footer" id="tableWithSearch" role="grid" aria-describedby="tableWithSearch_info" style="margin-top: 30px;">',
            'heading_row_start' => '<tr style= "font-size:20px"role="row">',
            'heading_row_end' => '</tr>',
            'heading_cell_start' => '<th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 127px;font-size: 14px;">',
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
        $this->table->set_heading('TYPE ID', 'TYPE NAME', 'MAX SIZE', 'BASE FARE', 'MIN FARE', 'PRICE PER MINUTE' . ' (' . currency . ')', 'PRICE PER KILOMETER' . ' (' . currency . ')', 'TYPE DESCRIPTION', 'CITY NAME', 'SELECT');

        $data['pagename'] = "admin/vehicle_type";
        $this->load->view("admin", $data);
//        $this->load->view("cities");
    }

    public function delete_vehicletype() {
        $data['delete'] = $this->adminmodel->delete_vehicletype();
    }

    public function activate_vehicle() {
        $this->adminmodel->activate_vehicle();
    }

    Public function reject_vehicle() {
        $this->adminmodel->reject_vehicle();
    }

    public function inactivedriver_review() {
        $data['driver_review'] = $this->adminmodel->inactivedriver_review();
    }

    public function activedriver_review() {
        $data['driver_review'] = $this->adminmodel->activedriver_review();
    }

    public function vehicletype_addedit($status = '', $param = '') {

        ;
        $data['param'] = "";
        print($data);

        if ($status == 'edit') {
            $data['editvehicletype'] = $this->adminmodel->edit_vehicletype($param);

            $data['status'] = $status;
            $data['param'] = $param;
        } elseif ($status == 'add') {
            $data['city'] = $this->adminmodel->city();
            $data['status'] = $status;
            $data['param'] = "";
        }
        $data['pagename'] = "admin/vehicletype_addedit";
        $data['city'] = $this->adminmodel->get_city();
        $this->load->view("admin", $data);
    }

    public function editvehicle($status = '') {
        $return['data'] = $this->adminmodel->editvehicle($status);

        $return['vehId'] = $status;

        $return['pagename'] = "admin/editvehicle";
        $this->load->view("admin", $return);
    }

    public function insert_vehicletype() {
        $data = $this->adminmodel->insert_vehicletype();
    }

    public function update_vehicletype($param = '') {
        $data['updatevehicletype'] = $this->adminmodel->update_vehicletype($param);
    }

    public function inactivedispatchers() {
        $data = $this->adminmodel->inactivedispatchers();
    }

    public function activedispatchers() {
        $data = $this->adminmodel->activedispatchers();
    }

    public function editdispatchers() {

        $data['city'] = $this->adminmodel->city();
        $data = $this->adminmodel->editdispatchers();
    }

    public function insertdispatches() {
        $data = $this->adminmodel->insertdispatches();
    }

    public function editpass() {
        $data = $this->adminmodel->editpass();
    }

    public function editdriverpassword() {
        $data = $this->adminmodel->editdriverpassword();
    }

    public function passengers($status = '') {

        $data['status'] = $status;
        $data['passenger_info'] = $this->adminmodel->get_passengerinfo($status);


        $this->load->library('Datatables');
        $this->load->library('table');

        $tmpl = array('table_open' => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="table table-hover demo-table-search dataTable no-footer" id="tableWithSearch" role="grid" aria-describedby="tableWithSearch_info" style="margin-top: 30px;">',
            'heading_row_start' => '<tr style= "font-size:20px"role="row">',
            'heading_row_end' => '</tr>',
            'heading_cell_start' => '<th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 127PX;font-size: 14px;">',
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
        $this->table->set_heading('PASSENGER ID', 'FIRST NAME', 'LAST NAME', 'MOBILE', 'EMAIL', 'REGISTRATION DATE', 'PROFILE PIC', 'DEVICE TYPE', 'SELECT');




        //print_r($data);
        $data['pagename'] = "admin/passengers";
        $this->load->view("admin", $data);
    }

    public function acceptdrivers() {
        $this->adminmodel->acceptdrivers();
    }

    public function rejectdrivers() {
        $this->adminmodel->rejectdrivers();
    }

    public function inactivepassengers() {
        $data['inactive'] = $this->adminmodel->inactivepassengers();
    }

    public function activepassengers() {
        $data['inactive'] = $this->adminmodel->activepassengers();
    }

    public function insertpass() {
        $data['pass'] = $this->adminmodel->insertpass();
//        print_r($res);
    }



    public function Vehicles($status = '') {
        $data['company'] = $this->adminmodel->company_data();
        $data['vehicles'] = $this->adminmodel->Vehicles($status);


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

        $this->table->set_heading('VEHICLE ID', 'VEHICLE MAKE', 'VEHICLE MODEL', 'TYPE NAME','COMPANY NAME', 'VEHICLE  REGISTRATION NUMBER', 'LICENSE PLATE NUMBER', 'VEHICLE INSURANCE NUMBER', 'VEHICLE COLOR', 'SELECT');


        $data['pagename'] = 'admin/vehicles';
        $data['status'] = $status;
        $this->load->view("admin", $data);
    }




    public function deletecompany() {
        $data = $this->adminmodel->deletecompany();
    }

    public function datatable_drivers($for = '', $status = '') {
        $data = $this->adminmodel->datatable_drivers($for, $status);
    }

    public function deletecountry() {
        $data = $this->adminmodel->deletecountry();
    }

//    public function Drivers($for = '', $status = '') {
//
//        $this->load->library('Datatables');
//        $this->load->library('table');
//
//        $tmpl = array('table_open' => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="table table-hover demo-table-search dataTable no-footer" id="tableWithSearch" role="grid" aria-describedby="tableWithSearch_info" style="margin-top: 30px;">',
//            'heading_row_start' => '<tr style= "font-size:20px"role="row">',
//            'heading_row_end' => '</tr>',
//            'heading_cell_start' => ' <th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 127px;font-size: 14px;">',
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
//        $this->table->set_heading('DRIVER ID', 'FIRST NAME', 'LAST NAME', 'MOBILE', 'EMAIL', 'REG DATE', 'ZIPCODE', 'PROFILE PIC', 'DEVICE TYPE', 'SELECT');
//        $data['status'] = $status;
//
//
//        $data['pagename'] = 'company/drivers';
//        $this->load->view("admin", $data);
//    }


    public function Drivers($for = '', $status = '') {

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
        $this->table->set_heading('DRIVER ID', 'FIRST NAME', 'LAST NAME', 'MOBILE', 'EMAIL', 'REG DATE', 'COMPANY', 'PROFILE PIC', 'DEVICE TYPE', 'SELECT');

//        if ($for == 'my') {
//            $data['drivers'] = $this->adminmodel->Drivers($status);
//            $data['pagename'] = 'company/drivers';
//
//            /** we are passing my for Mysql to check the condition * */
//            $data['db'] = 'my';
//            $this->load->view("admin", $data);
//        } else if ($for == 'mo') {
//            $data['drivers'] = $this->adminmodel->get_Drivers_from_mongo($status);
//
//            $data['status'] = $status;
//            $data['db'] = 'mo';
//
//
//
//
//        }

        $data['pagename'] = 'admin/drivers';
        $this->load->view("admin", $data);
    }


    public function addnewvehicle() {

         $data['company'] = $this->adminmodel->get_company();
        $data['pagename'] = 'admin/addnewvehicle';

        $this->load->view("admin", $data);
    }

//    public function addnewvehicles() {
//
//        $data['addnewvehicle'] = $this->adminmodel->insert_addnewvehicles();
//    }

    public function addnewdriver() {


        $data['pagename'] = 'admin/addnewdriver';

        $this->load->view("admin", $data);
    }



    public function editNewVehicleData() {


        $this->adminmodel->editNewVehicleData();
        redirect(base_url() . "index.php/admin/Vehicles/5");
    }

    //* my controllers name is naveena *//

        public  function transection_data_ajax(){

         $this->adminmodel->getTransectionData();

    }

    public  function transection_data_form_date($stdate = '' , $enddate = '' ,$status = '' ,$company_id =''){
        $this->adminmodel->transection_data_form_date($stdate, $enddate,$status ,$company_id );
    }


    public function callExel($stdate = '', $enddate = '') {
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $data = $this->adminmodel->get_all_data($stdate, $enddate);

//        print_r( array (new ArrayObject (array ('name'=> 'ashish','call' => '123') )) );
        $this->excel->stream('Transaction.xls', $data);
    }

    public function deleteDrivers() {

//        $driverlist = $this->input->post('val');
//        $this->load->library('mongo_db');
//        $mongo = $this->mongo_db->db;
//        $location = $mongo->selectCollection('location');
//        $this->load->database();
//        foreach ($driverlist as $result) {
//            $this->db->query("DELETE from master where mas_id='" . $result . "' ");
//            $location->remove(array('user' => (int) $result));
//        }
        $this->adminmodel->deletedriver();
//        $this->Drivers('my', '1');
    }

    public function callExel_payroll() {
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
        $data['status'] = $status;

        $data['pagename'] = "admin/bookings";
        $this->load->library('Datatables');
        $this->load->library('table');
        $tmpl = array ( 'table_open'  => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="table table-hover demo-table-search dataTable no-footer" id="tableWithSearch" role="grid" aria-describedby="tableWithSearch_info" style="margin-top: 30px;">',
            'heading_row_start'   => '<tr role="row">',
            'heading_row_end'     => '</tr>',
            'heading_cell_start'  => '<th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 50px;">',
            'heading_cell_end'    => '</th>',

            'row_start'           => '<tr>',
            'row_end'             => '</tr>',
            'cell_start'          => '<td>',
            'cell_end'            => '</td>',

            'row_alt_start'       => '<tr>',
            'row_alt_end'         => '</tr>',
            'cell_alt_start'      => '<td>',
            'cell_alt_end'        => '</td>',

            'table_close'         => '</table>'
        );


        $this->table->set_template($tmpl);

        $this->table->set_heading('BOOKING ID','DRIVER ID','DRIVER NAME','PASSENGER NAME','PICKUP ADDRESS','DROP ADDRESS','PICKUP TIME & DATE','DISTANCE(IN METERS)','STATUS');
        $this->load->view("admin", $data);
    }

    public  function  bookings_data_ajax($status = '',$comapnyid = ''){
         $this->adminmodel->getbooking_data($status,$comapnyid);

    }



    public function dispatched($status = '') {
        $data['status'] = $status;
        $data['city'] = $this->adminmodel->city();
        $data['getdata'] = $this->adminmodel->get_dispatchers_data($status);
        
         $this->load->library('Datatables');
        $this->load->library('table');
        $tmpl = array ( 'table_open'  => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="table table-hover demo-table-search dataTable no-footer" id="tableWithSearch" role="grid" aria-describedby="tableWithSearch_info" style="margin-top: 30px;">',
            'heading_row_start'   => '<tr role="row">',
            'heading_row_end'     => '</tr>',
            'heading_cell_start'  => '<th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 50px;font-size:14px">',
            'heading_cell_end'    => '</th>',

            'row_start'           => '<tr>',
            'row_end'             => '</tr>',
            'cell_start'          => '<td>',
            'cell_end'            => '</td>',

            'row_alt_start'       => '<tr>',
            'row_alt_end'         => '</tr>',
            'cell_alt_start'      => '<td>',
            'cell_alt_end'        => '</td>',

            'table_close'         => '</table>'
        );


        $this->table->set_template($tmpl);

        $this->table->set_heading('DISPATCHER ID','CITY','EMAIL','DISPATCHER NAME','NO OF BOOKINGS','OPTION');
       
        
        
        
        $data['pagename'] = "admin/dispatched";
    
        $this->load->view("admin", $data);
    }

    public function finance($status = '') {
        $data['status'] = $status;
        $data['pagename'] = "admin/finance";
        $this->load->view("admin", $data);
    }

    public function document($status = '') {

        $data['status'] = $status;

        $data['master'] = $this->adminmodel->driver();

        $data['document_data'] = $this->adminmodel->get_documentdata($status);

        $data['workname'] = $this->adminmodel->get_workplace();


        $data['pagename'] = "admin/document";
        $this->load->view("admin", $data);
    }


    public function passenger_rating() {

        $data['passenger_rating'] = $this->adminmodel->passenger_rating();



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

        $this->table->set_heading('PASSENGER ID', 'PASSENGER NAME', 'PASSENGER EMAIL', 'AVG RATING');


        $data['pagename'] = "admin/passenger_rating";
        $this->load->view("admin", $data);
    }

    public function datatable_passengerrating() {

        $this->adminmodel->datatable_passengerrating();
    }

    public function driver_review($status = '') {
        $data['status'] = $status;
        $data['driver_review'] = $this->adminmodel->driver_review($status);
        
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

        $this->table->set_heading('BOOKING ID', 'BOOKING DATE AND TIME', 'DRIVER NAME','PASSENGER ID', 'REVIEW','RATING','STATUS','SELECT');
        
        
        $data['pagename'] = "admin/driver_review";
        $this->load->view("admin", $data);
    }

//    public function disputes($status = '') {
//        $data['status'] = $status;
//        $data['city'] = $this->adminmodel->get_city();
//        $data['disputesdata'] = $this->adminmodel->get_disputesdata($status);
//        $data['master'] = $this->adminmodel->driver();
//        $data['slave'] = $this->adminmodel->passenger();
//
//
//        $data['pagename'] = "admin/disputes";
//        $this->load->view("admin", $data);
//    }

    public function disputes($status = '') {
        $data['status'] = $status;
        $data['city'] = $this->adminmodel->get_city();
        $data['disputesdata'] = $this->adminmodel->get_disputesdata($status);
        $data['master'] = $this->adminmodel->driver();
        $data['slave'] = $this->adminmodel->passenger();



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

        $this->table->set_heading('DISPUTE ID', 'PASSENGER ID', 'PASSENGER NAME', 'DRIVER ID', 'DRIVER NAME', 'DISPUTE MESSAGE', 'DISPUTE DATE', 'BOOKING ID', 'SELECT');



        $data['pagename'] = "admin/disputes";
        $this->load->view("admin", $data);
    }

    public function datatable_disputes($status) {

        $this->adminmodel->datatable_disputes($status);
    }

    public function documentgetdata() {

        echo json_encode($this->adminmodel->documentgetdata());
        exit();
    }

    public function documentgetdatavehicles() {

        echo json_encode($this->adminmodel->documentgetdatavehicles());
        exit();
    }

    public function resolvedisputes() {
        $data = $this->adminmodel->resolvedisputes();
    }

    public function delete() {
        
          $data['getvehicletype']= $this->adminmodel->get_vehivletype();
           $data['getcompany']= $this->adminmodel->get_company();
             $data['city'] = $this->adminmodel->city();
               $data['driver'] = $this->adminmodel->get_driver();
                $data['vehiclemodal'] = $this->adminmodel->vehiclemodal();
              $data['country'] = $this->adminmodel->get_country();
//          print_r($data['getvehicletype']);
          
        
        $data['pagename'] = "admin/delete";
      
        $this->load->view("admin", $data);
    }

    public function deactivecompaigns() {
        $data['deactivate'] = $this->adminmodel->deactivecompaigns();
    }
    
    public function deletetype(){
        $data = $this->adminmodel->deletetype();
    }

    public function godsview() {
        $data['pagename'] = "admin/godsview";
        $data['cities'] = $this->adminmodel->get_cities();
        $this->load->view("admin", $data);
    }

    public function vehicle_models($status = '') {
        $data['status'] = $status;
        $data['vehiclemake'] = $this->adminmodel->get_vehiclemake();
        
        if($status == 1){

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


            $this->table->set_heading('ID', 'TYPE NAME', 'SELECT');
        }

        else if($status == 2){

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

            $this->table->set_heading('ID','MAKE', 'MODEL', 'SELECT');


        }


        $data['pagename'] = "admin/vehicle_models";
        $this->load->view("admin", $data);
    }

    function datatable_vehiclemodels($status = '') {


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
        $data = $this->adminmodel->insert_typename();
    }

    public function insertmodal() {
        $data = $this->adminmodel->insert_modal();
    }

    public function deletevehicletype() {
        $data = $this->adminmodel->deletevehicletype();
    }

    public function delete_company() {
        $data = $this->adminmodel->delete_company();
    }

    public function deletevehiclemodal() {
        $data = $this->adminmodel->deletevehiclemodal();
    }
    
      public function deletedriver() {
        $data = $this->adminmodel->deletedriver();
    }
    
     public function deletemodal() {
        $data = $this->adminmodel->deletemodal();
    }

    public function compaigns($status = '') {

        $data['status'] = $status;
        $data['city'] = $this->adminmodel->get_city();
        $data['compaign'] = $this->adminmodel->get_compaigns_data($status);

        $data['pagename'] = "admin/compaigns";
        $this->load->view("admin", $data);
    }

       public  function   compaigns_ajax($for = ''){

                $this->adminmodel->get_compaigns_data_ajax($for);

      }

    public function insertcompaigns() {
        echo $this->adminmodel->insertcampaigns();
    }

    public function cancled_booking() {
        $data['pagename'] = "admin/cancled_booking";
        $this->load->view("admin", $data);
//        $this->load->view("cities");
    }

    public function Get_dataformdate($stdate = '', $enddate = '') {
//        $data = $this->adminmodel->get_all_data();
        $data['transection_data'] = $this->adminmodel->getDatafromdate($stdate, $enddate);
        $data['stdate'] = $stdate;
        $data['enddate'] = $enddate;
        $data['gat_way'] = '2';
        $data['pagename'] = "admin/Transection";
        $this->load->view("admin", $data);
    }

        public  function  Get_dataformdate_for_all_bookingspg($stdate = '' , $enddate = '' ,$status = '' ,$company_id =''){
         $this->adminmodel->getDatafromdate_for_all_bookings($stdate, $enddate,$status ,$company_id );
        }
//    public function get_vehicle_data(){
//        $data['vehicletype']=$this->adminmodel->get_vehicle_data();
//
//    }

    public function search_by_select($selectdval = '') {
//        $data['transection_data'] =
            $this->adminmodel->getDataSelected($selectdval);
//        $data['pagename'] = "admin/Transection";
//        $data['gat_way'] = $selectdval;
//        $this->load->view("admin", $data);
    }

    public function profile() {
        $sessionsetornot = $this->adminmodel->issessionset();
        if ($sessionsetornot) {
            $data['userinfo'] = $this->adminmodel->getuserinfo();
            $data['pagename'] = "admin/profile";
            $this->load->view("admin", $data);
        } else {
            redirect(base_url() . "index.php/admin");
        }
    }

    public function services() {

        $sessionsetornot = $this->adminmodel->issessionset();
        if ($sessionsetornot) {

            $data['service'] = $this->adminmodel->getActiveservicedata();
            $data['pagename'] = "admin/Addservice";
            $this->load->view("admin", $data);
        } else {
            redirect(base_url() . "index.php/admin");
        }
    }

    public function updateservices($tablename = '') {

        $this->adminmodel->updateservices($tablename);
        redirect(base_url() . "index.php/admin/services");
    }

    function deleteservices($tablename = "") {
        $this->adminmodel->deleteservices($tablename);
        redirect(base_url() . "index.php/admin/services");
    }

    function Banking() {
        $sessionsetornot = $this->adminmodel->issessionset();
        if ($sessionsetornot) {

//            $data['service'] = $this->adminmodel->getActiveservicedata();
            $data['pagename'] = "admin/banking";
            $this->load->view("admin", $data);
        } else {
            redirect(base_url() . "index.php/admin");
        }
    }

    public function addservices() {

        $data['service'] = $this->adminmodel->addservices();
        redirect(base_url() . "index.php/admin/services");
    }

    public function booking() {
        $sessionsetornot = $this->madmin->issessionset();
        if ($sessionsetornot) {
            $data['bookinlist'] = $this->madmin->getPassangerBooking();
            $data['pagename'] = "booking";
            $this->load->view("index", $data);
        } else {
            redirect(base_url() . "index.php/admin");
        }
    }

    function Logout() {

        $this->session->sess_destroy();
        redirect(base_url() . "index.php/admin");
    }

    function udpadedataProfile() {
        $this->adminmodel->updateDataProfile();

        if ($this->input->post('val')) {
            $filename = "demo.png";
            if (move_uploaded_file($_FILES['userfile']['tmp_name'], base_url() . 'files/' . $filename)) {
                echo $filename;
            }
        }
        redirect(base_url() . "index.php/admin/profile");
    }

    function udpadedata($IdToChange = '', $databasename = '', $db_field_id_name = '') {

        $this->madmin->updateData($IdToChange, $databasename, $db_field_id_name);
        redirect(base_url() . "index.php/admin/profile");
    }

    public function updateMasterBank() {
//        return;
        $ret = $this->adminmodel->updateMasterBank();
        $data['error'] = $ret['flag'];
        $data['error_message'] = $ret['message'];
        $data['error_array'] = $ret;
        $data['userData'] = $ret['data'];
        $data['pagename'] = "master/banking";
        $this->load->view("master", $data);
    }



    public  function payroll(){

//        $data['payroll']=$this->adminmodel->payroll();
        $data['pagename']='admin/payroll';

        $this->load->library('Datatables');
        $this->load->library('table');
        $tmpl = array ( 'table_open'  => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="table table-hover demo-table-search dataTable no-footer" id="tableWithSearch" role="grid" aria-describedby="tableWithSearch_info" style="margin-top: 30px;">',
            'heading_row_start'   => '<tr role="row">',
            'heading_row_end'     => '</tr>',
            'heading_cell_start'  => '<th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 50px;">',
            'heading_cell_end'    => '</th>',

            'row_start'           => '<tr>',
            'row_end'             => '</tr>',
            'cell_start'          => '<td>',
            'cell_end'            => '</td>',

            'row_alt_start'       => '<tr>',
            'row_alt_end'         => '</tr>',
            'cell_alt_start'      => '<td>',
            'cell_alt_end'        => '</td>',

            'table_close'         => '</table>'
        );

        $this->table->set_template($tmpl);

        $this->table->set_heading('DRIVER ID','NAME','TODAY EARNINGS ('.currency.')','WEEK EARNINGS ('.currency.')','MONTH EARNINGS ('.currency.')','LIFE TIME EARNINGS ('.currency.')','DUE','SHOW');
        $this->load->view("admin", $data);


    }

    public  function  payroll_ajax(){
        $this->adminmodel->payroll();


    }


        public function payroll_data_form_date($stdate = '' , $enddate = '',$company_id =''){
            $this->adminmodel->payroll_data_form_date($stdate , $enddate ,$company_id );
        }

    public  function DriverDetails_form_Date($stdate = '' , $enddate = '',$company_id ='',$mas_id = ''){
        $this->adminmodel->DriverDetails_form_Date($stdate , $enddate ,$company_id ,$mas_id);
    }

    public  function Driver_pay($id = ''){

        $data['driverdata']=$this->adminmodel->Driver_pay($id);
        $data['payrolldata'] = $this->adminmodel->get_payrolldata($id);
        $data['totalamountpaid'] = $this->adminmodel->Totalamountpaid($id);
        $data['mas_id']=$id;
        $data['pagename']='admin/driverpayment';
        $this->load->view("admin", $data);
    }

    public function pay_driver_amount($id = ''){
        $this->adminmodel->insert_payment($id);
        redirect(base_url() . "index.php/admin/Driver_pay/".$id);

    }
    


    public function validateEmail() {

        return $this->adminmodel->validateEmail();
    }

    public function AddNewDriverData() {
        $this->adminmodel->AddNewDriverData();
        redirect(base_url() . "index.php/admin/Drivers/my/1");
    }

    public function AddNewVehicleData() {
        $this->adminmodel->AddNewVehicleData();
        redirect(base_url() . "index.php/admin/Vehicles/5");
    }

    public function DriverDetails($mas_id = '') {
//        $data['driverdetails'] = $this->adminmodel->DriverDetails($mas_id);
        $data['pagename'] = 'admin/driverDetails';
        $data['mas_id'] = $mas_id;
        $this->load->library('Datatables');
        $this->load->library('table');
        $tmpl = array ( 'table_open'  => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="table table-hover demo-table-search dataTable no-footer" id="tableWithSearch" role="grid" aria-describedby="tableWithSearch_info" style="margin-top: 30px;">',
            'heading_row_start'   => '<tr role="row">',
            'heading_row_end'     => '</tr>',
            'heading_cell_start'  => '<th class="sorting" tabindex="0" aria-controls="tableWithSearch" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 50px;">',
            'heading_cell_end'    => '</th>',

            'row_start'           => '<tr>',
            'row_end'             => '</tr>',
            'cell_start'          => '<td>',
            'cell_end'            => '</td>',

            'row_alt_start'       => '<tr>',
            'row_alt_end'         => '</tr>',
            'cell_alt_start'      => '<td>',
            'cell_alt_end'        => '</td>',

            'table_close'         => '</table>'
        );

        $this->table->set_template($tmpl);

        $this->table->set_heading('BOOKING ID','CUSTOMER NAME','CUSTOMER PAID ('.currency.')','APP COMMISSION ('.currency.')','PAYMENT GATEWAY COMM. ('.currency.')','DRIVER EARNING ('.currency.')');

        $this->load->view("admin", $data);
    }

    public  function  DriverDetails_ajax($mas_id = ''){
        $this->adminmodel->DriverDetails($mas_id);
    }

    public function deletecities() {
        $data['del'] = $this->adminmodel->deletecity();
    }
  
    

    public function deleteVehicles() {

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

                $db =$this->mongo_db->db;

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

        $this->load->database();
        if ($param == 'vtype') {
            $get_vechile_type = $this->db->query("select type_id,type_name from workplace_types where city_id = '" . $_REQUEST['city'] . "'")->result();
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
        }
         else if ($param == 'companyselect') {
            $get_company = $this->db->query("select company_id,companyname from company_info where city = '" . $this->input->post('company'). "' and status = 3")->result();
            echo " <option value=''>Select a Company  </option>";
            foreach ($get_company as $row) {
                echo "<option value='" . $row->company_id . "' id='" . $row->company_id . "' >" . $row->companyname . "</option>";
            }
        }
    }




}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */