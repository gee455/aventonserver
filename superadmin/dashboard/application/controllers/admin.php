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
        $this->load->model("madmin");
//        $this->load->library('session');
        header("cache-Control: no-store, no-cache, must-revalidate");
        header("cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
    }

    public function index($loginerrormsg = NULL) {

        $data['loginerrormsg'] = $loginerrormsg;
        $this->load->view('login', $data);
    }

    public function AuthenticateUser() {
        $email = $this->input->post("email");
        $password = $this->input->post("password");
        if ($email && $password) {


            $status = $this->madmin->ValidateSuperAdmin();

            if ($status) {
                if ($this->session->userdata('table') == 'slave')
                    redirect(base_url() . "index.php/admin/booking");
                else if($this->session->userdata('table') == 'master')
                    redirect(base_url() . "index.php/masteradmin/Dashboard");
            } else {
                $loginerrormsg = "invalid email or password";
                $this->index($loginerrormsg);
            }
        }
        else
            redirect(base_url() . "index.php");
    }

    public function loadDashbord() {
        $sessionsetornot = $this->madmin->issessionset();
        if ($sessionsetornot) {
            $data['userinfo'] = $this->madmin->getuserinfo();
            $data['pagename'] = "Dashbord";
            $this->load->view("index", $data);
        } else {
            redirect(base_url() . "index.php");
        }
    }

    public function booking() {
        $sessionsetornot = $this->madmin->issessionset();
        if ($sessionsetornot) {
            $data['bookinlist'] = $this->madmin->getPassangerBooking();
            $data['pagename'] = "booking";
            $this->load->view("index", $data);
        } else {
            redirect(base_url() . "index.php");
        }
    }

    function Logout() {

        $this->session->sess_destroy();
        redirect(base_url() . "index.php");
    }

    function udpadedata($IdToChange = '', $databasename = '', $db_field_id_name = '') {

        $this->madmin->updateData($IdToChange, $databasename, $db_field_id_name);
        redirect(base_url() . "index.php/admin/loadDashbord");
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */