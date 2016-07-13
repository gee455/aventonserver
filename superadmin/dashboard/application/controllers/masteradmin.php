<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Masteradmin extends CI_Controller {

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
        $this->load->model("mastermodal");
//        $this->load->library('excel');
        header("cache-Control: no-store, no-cache, must-revalidate");
        header("cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
        
    }

    public function index($loginerrormsg = NULL) {

        $data['loginerrormsg'] = $loginerrormsg;
        $this->load->view('master/login', $data);
    }

    public function AuthenticateUser() {
        $email = $this->input->post("email");
        $password = $this->input->post("password");
        if ($email && $password) {


            $status = $this->mastermodal->ValidateSuperAdmin();

            if (is_array($status)) {
                $this->index($status['Message']);
            }
            else if ($status) {
                if ($this->session->userdata('table') == 'master')
                    redirect(base_url() . "index.php/masteradmin/Dashboard");
            }
            else {
                $loginerrormsg = "invalid email or password";
                $this->index($loginerrormsg);
            }
        } else
            redirect(base_url() . "index.php/masteradmin");
    }

    
    
     // banking sectore start
    
     function Banking() {
        $sessionsetornot = $this->mastermodal->issessionset();
        if ($sessionsetornot) {

//            $data['service'] = $this->mastermodal->getActiveservicedata();
            $data['pagename'] = "master/banking";
            $this->load->view("master", $data);
        } else {
            redirect(base_url() . "index.php");
        }
    }

    function Bank() 
    {
        $sessionsetornot = $this->mastermodal->issessionset();
        if ($sessionsetornot) 
        {
            $data['pagename'] = "master/bank";
            $data['Bank_Arr'] = $this->mastermodal->GetStripeIdForDoctor();
            $this->load->view("master", $data);
        } else {
            redirect(base_url() . "index.php");
        }        
    }
    
    function Bank_ajax() 
    {
        $sessionsetornot = $this->mastermodal->issessionset();
        if ($sessionsetornot) 
        {
            $Bank_Arr = $this->mastermodal->GetStripeIdForDoctor();
            echo json_encode($Bank_Arr);
        } else {
            redirect(base_url() . "index.php");
        }        
    }
    
    public function AddRecipient() {
        $res = $this->mastermodal->AddRecipient();
        echo json_encode($res);
    }
    
    public function DeleteRecipient()
    {
        $res = $this->mastermodal->DeleteRecipient();
        echo json_encode($res);
    }
    
    public function MakeDefaultRecipient()
    {
        $res = $this->mastermodal->MakeDefaultRecipient();
        echo json_encode($res);
    }
    
    //banking sectore end
    

    public function Dashboard() {
        if ($this->session->userdata('table') != 'master') {
            redirect(base_url() . "index.php/masteradmin");
        }
        $sessionsetornot = $this->mastermodal->issessionset();
        if ($sessionsetornot) {
            $data['todaybooking'] = $this->mastermodal->Getdashboarddata();
            $data['pagename'] = "master/Dashboard";
            $this->load->view("master", $data);
        } else {
            redirect(base_url() . "index.php/masteradmin");
        }
    }


    public function Transection() {
        if ($this->session->userdata('table') != 'master') {
            redirect(base_url() . "index.php/masteradmin");
        }
        $sessionsetornot = $this->mastermodal->issessionset();
//        if ($sessionsetornot) {

        $data['gat_way'] = "2";
        $data['pagename'] = "master/Transection";

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

        $this->load->view("master", $data);
//        } else {
//            redirect(base_url() . "index.php/masteradmin/admin");
//        }
    }

    public function callExel($stdate = '', $enddate = '') {
        if ($this->session->userdata('table') != 'master') {
            redirect(base_url() . "index.php/masteradmin");
        }
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $data = $this->mastermodal->get_all_data($stdate, $enddate);

//        print_r( array (new ArrayObject (array ('name'=> 'ashish','call' => '123') )) );
        $this->excel->stream('Transaction.xls', $data);
    }
    
    
     public function bookings($status = '') {
        
        if ($this->session->userdata('table') != 'master') {
            $this->Logout();
        }
        $data['status'] = $status;

        $data['pagename'] = "master/bookings";
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

        $this->table->set_heading('BOOKING ID', 'DRIVER ID', 'DRIVER NAME', 'PASSENGER NAME', 'PICKUP ADDRESS', 'DROP ADDRESS', 'PICKUP TIME & DATE', 'DISTANCE(IN METERS)', 'STATUS', 'ROOT');
        $this->load->view("master", $data);
    }
    
    
    
    
     public function Get_dataformdate_for_all_bookingspg($stdate = '', $enddate = '', $status = '', $company_id = '') {
        
        if ($this->session->userdata('table') != 'master') {
            $this->Logout();
        }
        $this->mastermodal->getDatafromdate_for_all_bookings($stdate, $enddate, $status, $company_id);
    }
    
     public function bookings_data_ajax($status = '') {
         if ($this->session->userdata('table') != 'master') {
            redirect(base_url() . "index.php/masteradmin");
        }
        
        if ($this->session->userdata('table') != 'master') {
            $this->Logout();
        }
        $this->mastermodal->getbooking_data($status);
    }
    
     public function datatable_bookings($status) {
         
         if ($this->session->userdata('table') != 'master') {
            redirect(base_url() . "index.php/masteradmin");
        }
        
        if ($this->session->userdata('table') != 'master') {
            $this->Logout();
        }
        $this->mastermodal->datatable_bookings($status);
    }

    public function Get_dataformdate($stdate = '', $enddate = '') {
        if ($this->session->userdata('table') != 'master') {
            redirect(base_url() . "index.php/masteradmin");
        }
//        $data = $this->mastermodal->get_all_data();
        $data['transection_data'] = $this->mastermodal->getDatafromdate($stdate, $enddate);
        $data['stdate'] = $stdate;
        $data['enddate'] = $enddate;
        $data['pagename'] = "master/Transection";
        $this->load->view("master", $data);
    }

//    public function search_by_select($selectdval) {
//        $data['transection_data'] = $this->mastermodal->getDataSelected($selectdval);
//        $data['pagename'] = "master/Transection";
//        $this->load->view("master", $data);
//    }
    
    
     public function search_by_select($selectdval = '') {
         if ($this->session->userdata('table') != 'master') {
            redirect(base_url() . "index.php/masteradmin");
        }
        $this->mastermodal->getDataSelected($selectdval);

    }

    public function profile() {
        if ($this->session->userdata('table') != 'master') {
            redirect(base_url() . "index.php/masteradmin");
        }
        $sessionsetornot = $this->mastermodal->issessionset();
        if ($sessionsetornot) {
            $data['userinfo'] = $this->mastermodal->getuserinfo();
//            print_r($data);
//            exit();
            $data['pagename'] = "master/profile";
            $this->load->view("master", $data);
        } else {
            redirect(base_url() . "index.php/masteradmin");
        }
    }

    public function services() {
        if ($this->session->userdata('table') != 'master') {
            redirect(base_url() . "index.php/masteradmin");
        }
        $sessionsetornot = $this->mastermodal->issessionset();
        if ($sessionsetornot) {

            $data['service'] = $this->mastermodal->getActiveservicedata();
            $data['pagename'] = "master/Addservice";
            $this->load->view("master", $data);
        } else {
            redirect(base_url() . "index.php/masteradmin");
        }
    }

    public function updateservices($tablename = '') {
        if ($this->session->userdata('table') != 'master') {
            redirect(base_url() . "index.php/masteradmin");
        }
        $this->mastermodal->updateservices($tablename);
        redirect(base_url() . "index.php/masteradmin/services");
    }

    function deleteservices($tablename = "") {
        if ($this->session->userdata('table') != 'master') {
            redirect(base_url() . "index.php/masteradmin");
        }
        $this->mastermodal->deleteservices($tablename);
        redirect(base_url() . "index.php/masteradmin/services");
    }

   

    public function callExel_payroll() {
        if ($this->session->userdata('table') != 'master') {
            redirect(base_url() . "index.php/masteradmin");
        }
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        
        
        $value = $this->mastermodal->payroll();
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

    public function payroll_ajax() {
        if ($this->session->userdata('table') != 'master') {
            redirect(base_url() . "index.php/masteradmin");
        }
        $this->mastermodal->payroll();
    }
    
    public function driverdetails_ajax_() {
        
        if ($this->session->userdata('table') != 'master') {
            redirect(base_url() . "index.php/masteradmin");
        }
        
       echo  $this->mastermodal->Get_Driver_Details();
        
        
    }

    public function transection_data_ajax() {
        if ($this->session->userdata('table') != 'master') {
            redirect(base_url() . "index.php/masteradmin");
        }
        $this->mastermodal->getTransectionData();
    }

    public function transection_data_form_date($stdate = '', $enddate = '', $status = '', $company_id = '') {
        if ($this->session->userdata('table') != 'master') {
            redirect(base_url() . "index.php/masteradmin");
        }
        $this->mastermodal->transection_data_form_date($stdate, $enddate, $status, $company_id);
    }

    public function Driver_pay() {
        if ($this->session->userdata('table') != 'master') {
            redirect(base_url() . "index.php/masteradmin");
        }
        $data['driverdata'] = $this->mastermodal->Driver_pay();
        $data['payrolldata'] = $this->mastermodal->get_payrolldata();
        $data['totalamountpaid'] = $this->mastermodal->Totalamountpaid();
        $data['mas_id'] = $id;
        $data['pagename'] = 'master/driverpayment';
        $this->load->view("master", $data);
    }
    

    public function driverpayment() {
         if ($this->session->userdata('table') != 'master') {
            redirect(base_url() . "index.php/masteradmin");
        }
        
         $data['driverdata'] = $this->mastermodal->Driver_pay();
         
     
         $data['payrolldata'] = $this->mastermodal->get_payrolldata();
        
         $data['totalamountpaid'] = $this->mastermodal->Totalamountpaid();
             
        $data['pagename'] = 'master/driverpayment';
        $this->load->view("master", $data);
        
    }

    public function pay_driver_amount($id = '') {
        if ($this->session->userdata('table') != 'master') {
            redirect(base_url() . "index.php/masteradmin");
        }
        $this->mastermodal->insert_payment($id);
        redirect(base_url() . "index.php/masteradmin/Driver_pay/" . $id);
    }
    
    
     public function DriverDetails_form_Date($stdate = '', $enddate = '') {
         if ($this->session->userdata('table') != 'master') {
            redirect(base_url() . "index.php/masteradmin");
        }
       
        $this->mastermodal->DriverDetails_form_Date($stdate, $enddate);
    }

    public function driverDetails() {
        

        if ($this->session->userdata('table') != 'master') {
            redirect(base_url() . "index.php/masteradmin");
        }
        
//        $data['payroll']=$this->adminmodel->payroll();
        $data['pagename'] = 'master/driverDetails';

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

        $this->table->set_heading('BOOKING ID', 'COUSTOMER NAME', 'CUSTOMER PAID(' . currency . ')', 'APP COMMISSION (' . currency . ')', 'PAYMENT GATEWAY (' . currency . ')', 'DRIVER EARNINGS (' . currency . ')');
        $this->load->view("master", $data);
    }

    public function payroll() {
        if ($this->session->userdata('table') != 'master') {
            redirect(base_url() . "index.php/masteradmin");
        }

//        $data['payroll']=$this->adminmodel->payroll();
        $data['pagename'] = 'master/payroll';

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
        $this->load->view("master", $data);
    }

    public function addservices() {
        if ($this->session->userdata('table') != 'master') {
            redirect(base_url() . "index.php/masteradmin");
        }

        $data['service'] = $this->mastermodal->addservices();
        redirect(base_url() . "index.php/masteradmin/services");
    }

    public function booking() {
        if ($this->session->userdata('table') != 'master') {
            redirect(base_url() . "index.php/masteradmin");
        }
        $sessionsetornot = $this->madmin->issessionset();
        if ($sessionsetornot) {
            $data['bookinlist'] = $this->madmin->getPassangerBooking();
            $data['pagename'] = "booking";
            $this->load->view("index", $data);
        } else {
            redirect(base_url() . "index.php/masteradmin");
        }
    }

    function Logout() {

        $this->session->sess_destroy();
        redirect(base_url() . "index.php/masteradmin");
    }
    
    function changemasterpassword(){
        if ($this->session->userdata('table') != 'master') {
            redirect(base_url() . "index.php/masteradmin");
        }
      echo  $this->mastermodal->changemasterpassword();
    }

    function udpadedataProfile() {
        if ($this->session->userdata('table') != 'master') {
            redirect(base_url() . "index.php/masteradmin");
        }
        $this->mastermodal->updateDataProfile();
        if ($this->session->userdata('table') != 'master') {
            redirect(base_url() . "index.php/masteradmin");
        }
        redirect(base_url() . "index.php/masteradmin/profile");
    }

    function udpadedata($IdToChange = '', $databasename = '', $db_field_id_name = '') {
        if ($this->session->userdata('table') != 'master') {
            redirect(base_url() . "index.php/masteradmin");
        }

        $this->madmin->updateData($IdToChange, $databasename, $db_field_id_name);
        redirect(base_url() . "index.php/masteradmin/masteradmin/profile");
    }

    public function updateMasterBank() {
        if ($this->session->userdata('table') != 'master') {
            redirect(base_url() . "index.php/masteradmin");
        }
//        return;
        $ret = $this->mastermodal->updateMasterBank();
        $data['error'] = $ret['flag'];
        $data['error_message'] = $ret['message'];
        $data['error_array'] = $ret;
        $data['userData'] = $ret['data'];
        $data['pagename'] = "master/banking";
        $this->load->view("master", $data);
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */