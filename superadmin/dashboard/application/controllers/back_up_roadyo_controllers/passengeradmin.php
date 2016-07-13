<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
error_reporting(0);

class Passengeradmin extends CI_Controller {

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
        $this->load->model("Passengermodal");
//        $this->load->library('session');
        header("cache-Control: no-store, no-cache, must-revalidate");
        header("cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
    }

    public function index($loginerrormsg = NULL) {

        $data['loginerrormsg'] = $loginerrormsg;
        $this->load->view('passenger/login', $data);
    }

    public function AuthenticateUser() {
        $email = $this->input->post("email");
        $password = $this->input->post("password");
        if ($email && $password) {


            $status = $this->Passengermodal->ValidateSuperAdmin();

            if ($status) {
                if ($this->session->userdata('table') == 'slave')
                    redirect(base_url() . "index.php/passengeradmin/booking");
            } else {
                $loginerrormsg = "invalid email or password";
                $this->index($loginerrormsg);
            }
        } else
            redirect(base_url() . "index.php/passengeradmin");
    }

    public function loadDashbord() {
        

        if ($this->session->userdata('table') != 'slave') {
            $this->Logout();
        }

        $sessionsetornot = $this->Passengermodal->issessionset();
        if ($sessionsetornot) {
            $data['userinfo'] = $this->Passengermodal->getuserinfo();
            $data['pagename'] = "passenger/Dashbord";
            $this->load->view("passenger", $data);
        } else {
            redirect(base_url() . "index.php/passengeradmin");
        }
    }

    function changeslavepassword() {
         if ($this->session->userdata('table') != 'slave') {
            $this->Logout();
        }
        echo $this->Passengermodal->changeslavepassword();
    }
    
    public function booking() {
        
        if ($this->session->userdata('table') != 'slave') {
            $this->Logout();
        }
        $data['status'] = $status;

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

        $this->table->set_heading('BOOKING ID', 'DRIVER NAME', 'DRIVER PHOTO', 'PICKUP ADDRESS', 'DROP ADDRESS', 'PICKUP TIME & DATE','DROP TIME & DATE', 'FARE', 'INVOICE');
        
        $data['pagename'] = "passenger/booking";
        $this->load->view("passenger", $data);
    }
    
     public function bookings_data_ajax() {
        
        if ($this->session->userdata('table') != 'slave') {
            $this->Logout();
        }
        
        echo $this->Passengermodal->getbooking_data();
    }

//    public function booking() {
//        if ($this->session->userdata('table') != 'slave') {
//            $this->Logout();
//        }
//        $sessionsetornot = $this->Passengermodal->issessionset();
//        if ($sessionsetornot) {
//            $data['bookinlist'] = $this->Passengermodal->getPassangerBooking();
//            $data['pagename'] = "passenger/booking";
//            $this->load->view("passenger", $data);
//        } else {
//            redirect(base_url() . "index.php/passengeradmin");
//        }
//    }

    function Logout() {

        $this->session->sess_destroy();
        redirect(base_url() . "index.php/passengeradmin");
    }

    function udpadedata($IdToChange = '', $databasename = '', $db_field_id_name = '') {
        if ($this->session->userdata('table') != 'slave') {
            $this->Logout();
        }


        $this->Passengermodal->updateData($IdToChange, $databasename = '', $db_field_id_name = '');

        redirect(base_url() . "index.php/passengeradmin/loadDashbord");
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */