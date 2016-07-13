<?php

if (!defined("BASEPATH"))
    exit("Direct access to this page is not allowed");

class Passengermodal extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->database();
    }

    function validateSuperAdmin() {

        $email = $this->input->post("email");
        $password = $this->input->post("password");

        $queryforslave = $this->db->get_where('slave', array('email' => $email, 'password' => md5($password)));
        $res = $queryforslave->row();


        if ($queryforslave->num_rows > 0) {
            $tablename = 'slave';
            $LoginId = 'slave_id';
            $sessiondata = $this->setsessiondata($tablename, $LoginId, $res, $email, $password);
            $this->session->set_userdata($sessiondata);
            return true;
        }

        return false;
    }

    function setsessiondata($tablename, $LoginId, $res, $email, $password) {
        $sessiondata = array(
            'emailid' => $email,
            'password' => $password,
            'LoginId' => $res->$LoginId,
            'profile_pic' => $res->profile_pic,
            'first_name' => $res->first_name,
            'last_name' => $res->last_name,
            'table' => $tablename,
            'validate' => true
        );
        return $sessiondata;
    }

    function getPassangerBooking() {
        $query = $this->db->query("select a.appointment_id,a.complete_dt,a.amount,a.inv_id,a.distance_in_mts,a.appointment_dt,"
                        . "a.drop_addr1,a.drop_addr2,a.mas_id,a.slave_id,d.first_name as doc_firstname,d.profile_pic as doc_profile,"
                        . "d.last_name as doc_lastname,p.first_name as patient_firstname,p.last_name as patient_lastname,"
                        . "a.address_line1,a.address_line2,a.status from appointment a,master d,"
                        . "slave p where a.slave_id=p.slave_id and d.mas_id = a.mas_id and a.status = 9 and a.slave_id='" . $this->session->userdata("LoginId") . "' order by a.appointment_id desc")->result(); //get_where('slave', array('email' => $email, 'password' => $password));
        return $query;
    }

    function getbooking_data() {

        $this->load->library('Datatables');
        $this->load->library('table');
//        return json_encode(array('test' => 'rtest'));

        $query = "a.slave_id = s.slave_id and a.slave_id = '" . $this->session->userdata("LoginId") . "' and a.mas_id = m.mas_id and a.status = 9";
        $this->datatables->select("a.appointment_id,m.first_name,m.profile_pic,a.address_line1,a.drop_addr1,DATE_FORMAT(a.appointment_dt,'%b %d %Y %h:%i %p'),DATE_FORMAT(a.complete_dt,'%b %d %Y %h:%i %p'),a.amount,a.type_id", false)
//                ->unset_column('m.profile_pic')
                ->edit_column('m.profile_pic', '<img src="' . base_url() . '../../pics/$1" width="50px" class="imageborder">', 'm.profile_pic')
                ->edit_column('a.type_id', '<a target="_blank" href="' . base_url() . '../../getPDF.php?apntId=$1"><button type="button" name="INVOICE"  width="50px">INVOICE</button></a>', 'a.appointment_id')
                ->from('appointment a,master m,slave s')
                ->where($query);

        echo $this->datatables->generate();
    }

    function updateData($IdToChange = '', $databasename = '', $db_field_id_name = '') {
        $formdataarray = $this->input->post('fdata');


        $this->db->update('slave', $formdataarray, array('slave_id' => $IdToChange));

        $this->session->set_userdata(array('profile_pic' => $formdataarray['profile_pic'],
            'first_name' => $formdataarray['first_name'],
            'last_name' => $formdataarray['last_name']));
    }

    function LoadAdminList() {
        $db = new MongoClient();
        $mongoDB = $db->db_Ryland_Insurence;
        $collection = $mongoDB->Col_Manage_Admin;
        $cursor = $collection->find(array('Role' => "SubAdmin"));
        $db->close();
        return $cursor;
    }

    function changeslavepassword() {


        $newpassword = $this->input->post('newpass');


        $data = $this->db->query("select password from slave where slave_id ='" . $this->session->userdata("LoginId") . "'")->row_array();

        if ($data['password'] == md5($newpassword)) {

            return json_encode(array('msg' => "you have entered the same password!, please enter the new password", "flag" => 1));
        } else {
            $this->db->update('slave', array('password' => md5($newpassword)), array('slave_id' => $this->session->userdata("LoginId")));

            return json_encode(array('msg' => "your new password updated successfully", "flag" => 0));
        }
    }

    function getuserinfo() {
        $query = $this->db->query("SELECT * FROM slave where slave_id='" . $this->session->userdata("LoginId") . "'")->row();
        return $query;
    }

    function AddNewAdmin() {
        $db = new MongoClient();
        $mongoDB = $db->db_Ryland_Insurence;
        $collection = $mongoDB->Col_Manage_Admin;

        $document = array(
            "Fname" => $this->input->post("Firstname"),
            "Lname" => $this->input->post("Lastname"),
            "Email" => $this->input->post("Email"),
            "Password" => md5($this->input->post("Password")),
            "Role" => "SubAdmin",
            "Parent" => "SuperAdmin",
            "Last_Login_Time" => NULL,
            "Last_Login_Ip" => NULL,
            "resetlink" => NULL
        );
        $collection->insert($document);

        $template = "<h3>you are added as SubAdmin  here is your login details</h3><br>"
                . "Emailid: " . $this->input->post("Email") . "<br>" .
                "Password: " . $this->input->post("Password") . "<br>";
        $to[] = array(
            'email' => $this->input->post("Email"),
            'name' => "prakash",
            'type' => "to");

        $from = "prakashjoshi9090@gmail.com";

        $subject = "Login Details";

        $this->sendMail($template, $to, $from, $subject);
        $db->close();
    }

    function ChangePassword($NewPassword, $EmailId) {
        $db = new MongoClient();
        $mongoDB = $db->db_Ryland_Insurence;
        $collection = $mongoDB->Col_Manage_Admin;
        $collection->update(array("Email" => $EmailId), array('$set' => array("Password" => md5($NewPassword))), array("multiple" => true));
        $this->session->set_userdata('password', $NewPassword);
        $db->close();
    }

    function ForgotPassword($useremail) {
        $db = new MongoClient();
        $mongoDB = $db->db_Ryland_Insurence;
        $collection = $mongoDB->Col_Manage_Admin;

        $cursor = $collection->findOne(array('Email' => $useremail));

        if ($cursor) {
            $rlink = md5(mt_rand());
            $resetlink = base_url() . "index.php/superadmin/VerifyResetLink/" . $rlink;
            $template = "<h3> Click below link to reset your password</h3><br>" . $resetlink;
            $to[] = array(
                'email' => $useremail,
                'name' => "prakash",
                'type' => "to");

            $from = "prakashjoshi9090@gmail.com";
            $subject = "Reset Password Link";
            $this->sendMail($template, $to, $from, $subject);
            $collection->update(array("Email" => $useremail), array('$set' => array("resetlink" => ($rlink))), array("multiple" => true));

            $db->close();

            return true;
        }
        return false;
    }

    function VerifyResetLink($vlink) {
        $db = new MongoClient();
        $mongoDB = $db->db_Ryland_Insurence;
        $collection = $mongoDB->Col_Manage_Admin;
        $cursor = $collection->findOne(array('resetlink' => $vlink));

        if ($cursor) {
            $password = md5("joshi");
            $collection->update(array("resetlink" => $vlink), array('$set' => array("Password" => $password)), array("multiple" => true));


            return true;
        }
        return false;
    }

    function sendMail($template, $to, $from, $subject) {
        require("src/Mandrill.php");

        try {

            $mandrill = new Mandrill('sHHx9KbktCU4idl6iechig');
            $message = array(
                'html' => ($template),
                'text' => 'Example text content',
                'subject' => $subject,
                'from_email' => $from,
                'from_name' => 'Ryland Insurence',
                'to' => $to,
                'headers' => array('Reply-To' => "prakashjoshi9090@gmail.com"),
                'important' => false,
                'track_opens' => null,
                'track_clicks' => null,
                'auto_text' => null,
                'auto_html' => null,
                'inline_css' => null,
                'url_strip_qs' => null,
                'preserve_recipients' => null,
                'view_content_link' => null,
                'bcc_address' => 'message.bcc_address@example.com',
                'tracking_domain' => null,
                'signing_domain' => null,
                'return_path_domain' => null,
                'merge' => true,
                'merge_language' => 'mailchimp',
                'metadata' => array('website' => 'www.RylandIncurence.com'),
            );

            $async = false;
            $ip_pool = 'Main Pool';
            $result = $mandrill->messages->send($message, $async, $ip_pool);
            $result['flag'] = 0;
            $result['message'] = $message;


            return true;
        } catch (Mandrill_Error $e) {
            return false;
        }
    }

    function issessionset() {

        if ($this->session->userdata('emailid') && $this->session->userdata('password')) {

            return true;
        }
        return false;
    }

}

?>
