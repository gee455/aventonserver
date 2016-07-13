<?php

if (!defined("BASEPATH"))
    exit("Direct access to this page is not allowed");

require_once 'StripeModule.php';

class Mastermodal extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
//        $this->load->model('mastermodal');
        $this->load->database();
    }

    
    
    // bank sectore start
    
     function GetStripeIdForDoctor(){
        
        $stripe = new StripeModule();
        $stripe_arr = $this->db->query("select * from master_bank where mas_id=" . $this->session->userdata("LoginId"))->result();
//      $stripe_arr = $this->db->query("select * from master_bank where mas_id=36")->result();
        $bank_arr = array();
        $i = 0;
            foreach ($stripe_arr as $strp)
            {
                $getResp = array('stripe_id' => $strp->stripe_id);
                $rep = $stripe->apiStripe('getRecipient', $getResp);
                $ret = json_decode($rep);
                $bank_arr[$i]['stripe_id'] = $strp->stripe_id;
                $bank_arr[$i]['bank_id'] = $strp->bank_id;
                $bank_arr[$i]['default_stripe'] = $strp->default_stripe;
                $bank_arr[$i]['name'] = $ret->name;
                $bank_arr[$i]['email'] = $ret->email;
                $bank_arr[$i]['bank_name'] = $ret->active_account->bank_name;
                $bank_arr[$i]['routing_number'] = $ret->active_account->routing_number;
                $bank_arr[$i]['country'] = $ret->active_account->country;
                $bank_arr[$i]['created'] = $ret->created;
                $bank_arr[$i]['description'] = $ret->description;
                $i++;
            }
            return $bank_arr;
    }
    
     function AddRecipient() 
    {
        $stripe = new StripeModule();
        $userData = $this->input->post('fdata');
        //$createRecipientArr = array('name' => $args['ent_first_name'] . ' ' . $args['ent_last_name'], 'type' => 'individual', 'email' => $args['ent_email'], 'tax_id' => '000000000', 'country' => 'US', 'account_number' => '000123456789', 'routing_number' => '110000000', 'description' => 'For ' . $args['ent_email']);
        $createRecipientArr = array('name' => $userData['name'], 'type' => 'individual', 'email' => $userData['email'], 'tax_id' => (string) $userData['tax_id'], 'bank_account' => (string) $userData['account_number'], 'routing_number' => (string) $userData['routing_number'], 'description' => 'For ' . $userData['email']);
        $recipient = $stripe->apiStripe('createRecipient', $createRecipientArr);
        
        if (isset($recipient['error'])) 
        {
            return array('flag' => 1, 'message' => $recipient['error']['message']);
        } 
        else if ($recipient['verified'] === FALSE) 
        {
            return array('flag' => 1, 'message' => "Unable to verify");
        } 
        else if ($recipient['verified'] === TRUE) 
        {
            $mid = $this->session->userdata("LoginId");
            $def = 0;
            $stripe_id = $recipient['id'];
            $totacc = $this->db->query("SELECT count(*) as totacc FROM master_bank WHERE mas_id = ".$mid)->result();
            if($totacc[0]->totacc == 0)
            {
                $def = 1;
                $this->db->query("update master set stripe_id = '" . $stripe_id ."'where mas_id = '" . $mid ."'");
            }
            $this->db->query("insert into master_bank(mas_id,stripe_id,default_stripe) values('".$mid."','".$stripe_id."','".$def."')");
            return array('flag' => 0, 'message' => "Bank Details Added successfully");
        }
    }
    
    function DeleteRecipient()
    {
            $stripe = new StripeModule();
            $bid = $this->input->post('bid');
            $stripeid = $this->db->query("select stripe_id from master_bank where bank_id=".$bid)->result();
            $getRep = array('stripe_id' => $stripeid[0]->stripe_id);
            $rep = $stripe->apiStripe('deleteRecipient', $getRep);
            if (isset($rep['error'])) 
            {
                return array('flag' => 1, 'message' => $rep['error']['message']);
            } 
            else
            {
                $this->db->query("delete from master_bank where bank_id = '". $bid."'");        
                return array('flag' => 1, 'message' => "Recipient Deleted");
            }
    }
    
    function MakeDefaultRecipient()
    {
            $bid = $this->input->post('bid');
            $mid = $this->session->userdata("LoginId");
            $this->db->query("update master_bank set default_stripe=0 where mas_id ='".$mid."'");
            $this->db->query("update master_bank set default_stripe=1 where bank_id ='".$bid."'");
            $stripeid = $this->db->query("select stripe_id from master_bank where default_stripe=1 and bank_id ='".$bid."'")->result();
            $this->db->query("update master set stripe_id = '" . $stripeid[0]->stripe_id ."'where mas_id = '" . $mid ."'");
            return array('flag' => 0, 'message' => "Default Bank Changed");            
    } 
    
    // end of bank sectore
    
    function Get_Driver_Details() {


        $this->load->library('Datatables');
        $this->load->library('table');
        
        $query = "p.slave_id = ap.slave_id and ap.status = 9 and ap.mas_id = doc.mas_id and doc.mas_id='" . $this->session->userdata("LoginId") . "'";

        $this->datatables->select('ap.appointment_id,p.first_name,ap.amount, ap.app_commission, ap.pg_commission,ap.mas_earning', false)
//                ->add_column('SHOW', '<a href="' . base_url("index.php/masteradmin/Driver_pay/$1") . '"><button class="btn btn-success btn-cons" style="min-width: 83px !important;">DETAILS</button></a>', 'masid')
                ->from(' master doc,appointment ap,slave p', false)
                ->where($query);


                $this->db->order_by('ap.appointment_id' ,'DESC');


                return $this->datatables->generate();
    }
    
    
    function validateSuperAdmin() {

        $email = $this->input->post("email");
        $password = $this->input->post("password");

        $queryforslave = $this->db->get_where('master', array('email' => $email, 'password' => md5($password)));

        if ($queryforslave->num_rows > 0) {

            $res = $queryforslave->row();

//            if ($res->status == '1' || $res->status == '2')
//                return array('Message' => 'Your profile is under verification, please wait for our representative to reach you.');
//            else
                if ($res->status == '4')
                return array('Message' => 'Your profile is suspended by admin, please contact your company for further queries');

            $tablename = 'master';
            $LoginId = 'mas_id';
            $sessiondata = $this->setsessiondata($tablename, $LoginId, $res, $email, $password);
            $this->session->set_userdata($sessiondata);
            return true;
        }

        return false;
    }

    function insert_payment($mas_id = '') {


//        $total_earningsdata = $this->db->query('select sum(mas_earning) as total_mas_earning,(select appointment_id from appointment where mas_id ="' . $mas_id . '" and mas_earning != 0 and settled_flag = 0 order by appointment_id DESC limit 0,1) as last_appointment_id from appointment where mas_earning != 0 and settled_flag = 0 and mas_id ="' . $mas_id . '"')->row_array();
//
//        $total_earning_upto = round($total_earningsdata['total_mas_earning'], 2);
//        $lat_appointment_id = $total_earningsdata['last_appointment_id'];

        $currunEarnigs = $this->input->post('currunEarnigs');
        $lastAppointmentId = $this->input->post('last_unsettled_appointment_id');

        $amoutpaid = $this->input->post('paid_amount');
        $curuntdate = $this->input->post('ctime');
        $closingamt = $currunEarnigs - $amoutpaid;

        $getOpeningBal = $this->db->query("select closing_balance from payroll where mas_id = '" . $mas_id . "' order by payroll_id DESC limit 0,1")->row_array();

        $query = "insert into payroll(mas_id,opening_balance,pay_date,pay_amount,closing_balance,due_amount) VALUES (
        '" . $mas_id . "',
        '" . (double) $getOpeningBal['closing_balance'] . "',
        '" . $curuntdate . "',
        '" . $amoutpaid . "',
        '" . $closingamt . "','" . $closingamt . "')";
        $this->db->query($query);

        if ($this->db->insert_id() > 0) {

            $this->db->query("update appointment set settled_flag = 1 where appointment_id <= '" . $lastAppointmentId . "' and mas_id = '" . $mas_id . "' and settled_flag = 0 and status = 9 and payment_status IN (1,3)");
            if ($this->db->affected_rows() > 0) {
                return "Success";
            } else {
                return "Error";
            }
        } else {
            return "Error";
        }


//        echo $query;
//        exit();
    }

    function get_payrolldata() {
        $quaery = $this->db->query("SELECT * from payroll WHERE  mas_id = '" . $this->session->userdata('LoginId') . "'")->result();
//        $quaery = $this->db->query("SELECT due_amount,closing_balance,pay_date,pay_date,opening_balance,mas_id,trasaction_id,payroll_id,sum(pay_amount) as totalpaid from payroll  WHERE  mas_id = '" . $id . "'")->result();
        return $quaery;
    }

    function Totalamountpaid() {
        $quaery = $this->db->query("SELECT sum(pay_amount) as totalamt from payroll WHERE  mas_id = '" . $this->session->userdata('LoginId') . "'")->result();
//        $quaery = $this->db->query("SELECT due_amount,closing_balance,pay_date,pay_date,opening_balance,mas_id,trasaction_id,payroll_id,sum(pay_amount) as totalpaid from payroll  WHERE  mas_id = '" . $id . "'")->result();
        return $quaery;
    }

    function payroll() {

        $explodeDateTime = explode(' ', date("Y-m-d H:s:i"));
        $explodeDate = explode('-', $explodeDateTime[0]);
        $weekData = $this->week_start_end_by_date(date("Y-m-d H:s:i"));

        $this->load->library('Datatables');
        $this->load->library('table');
        $wereclousetocome = ';';
//        if ($this->session->userdata('company_id') != '0') {
        $wereclousetocome = "doc.mas_id = '" . $this->session->userdata('LoginId') . "'"; //and  doc.company_id ='" . $this->session->userdata('company_id') . "'


        $this->datatables->select('distinct doc.mas_id as masid,doc.first_name,'
//                 .'(select count(appointment_id) from appointment where mas_id = doc.mas_id and status = 9) as cmpltApts ,'
                        . "(case  when TRUNCATE((select sum(mas_earning) from appointment where mas_id = doc.mas_id and DATE(appointment_dt) = '" . $explodeDateTime[0] . "' and status = 9),2)  IS NULL then '--'  else TRUNCATE((select sum(mas_earning) from appointment where mas_id = doc.mas_id and DATE(appointment_dt) = '" . $explodeDateTime[0] . "' and status = 9),2) END) as TODAY_EARNINGS ,"
//                 .'(select amount from appointment where mas_id = doc.mas_id and status = 9 order by appointment_id DESC limit 0'.','.'1) as last_billed_amount ,'
//                    TODAY EARNINGS','WEEK EARNINGS','MONTH EARNINGS','LIFE TIME EARNINGS'
                        . " (case  when TRUNCATE((select sum(mas_earning) from appointment where mas_id = doc.mas_id and status = 9 and DATE(appointment_dt) BETWEEN '" . $weekData['first_day_of_week'] . "' and '" . $weekData['last_day_of_week'] . "'),2)  IS NULL then '--' else TRUNCATE((select sum(mas_earning) from appointment where mas_id = doc.mas_id and status = 9 and DATE(appointment_dt) BETWEEN '" . $weekData['first_day_of_week'] . "' and '" . $weekData['last_day_of_week'] . "'),2) END) as WEEK_EARNINGS ,"
                        . " (case  when TRUNCATE((select sum(mas_earning) from appointment where mas_id = doc.mas_id and status = 9 and DATE_FORMAT(appointment_dt,  '%Y-%m') = '" . $explodeDate[0] . '-' . $explodeDate[1] . "' ),2)  IS NULL then '--' else TRUNCATE((select sum(mas_earning) from appointment where mas_id = doc.mas_id and status = 9 and DATE_FORMAT(appointment_dt,  '%Y-%m') = '" . $explodeDate[0] . '-' . $explodeDate[1] . "' ),2)  END) as MONTH_EARNINGS,"
                        . " (case  when TRUNCATE((select sum(mas_earning) from appointment where mas_id = doc.mas_id and status = 9),2)  IS NULL then '--'  else TRUNCATE((select sum(mas_earning) from appointment where mas_id = doc.mas_id and status = 9),2) END) as LIFE_TIME_EARNINGS,"
                        . "(case  when TRUNCATE((select sum(pay_amount) from payroll where doc.mas_id = mas_id),2)  IS NULL then '--' else TRUNCATE((select sum(pay_amount) from payroll where doc.mas_id = mas_id),2) END) as PAID,"
                        . "(case  when  TRUNCATE(((select sum(mas_earning) from appointment where mas_id = doc.mas_id and status = 9 and payment_status in (1,3))-(select sum(pay_amount) from payroll where doc.mas_id = mas_id)),2) IS NULL then '--' else TRUNCATE(((select sum(mas_earning) from appointment where mas_id = doc.mas_id and status = 9)-(select sum(pay_amount) from payroll where doc.mas_id = mas_id)),2)END) as DUE", false)
                ->add_column('SHOW', '<a href="' . base_url("index.php/masteradmin/DriverDetails/$1") . '"><button class="btn btn-success btn-cons" style="min-width: 83px !important;">DETAILS</button></a>
            <a href="' . base_url("index.php/masteradmin/Driver_pay/$1") . '">', 'masid')
                ->from('master doc,appointment a ', false)
                ->where($wereclousetocome);
//        } else {
//
//            $this->datatables->select('distinct doc.mas_id as masid,doc.first_name,'
////                 .'(select count(appointment_id) from appointment where mas_id = doc.mas_id and status = 9) as cmpltApts ,'
//                            . "(case  when TRUNCATE((select sum(mas_earning) from appointment where mas_id = doc.mas_id and DATE(appointment_dt) = '" . $explodeDateTime[0] . "' and status = 9),2)  IS NULL then '--'  else TRUNCATE((select sum(mas_earning) from appointment where mas_id = doc.mas_id and DATE(appointment_dt) = '" . $explodeDateTime[0] . "' and status = 9),2) END) as TODAY_EARNINGS ,"
////                 .'(select amount from appointment where mas_id = doc.mas_id and status = 9 order by appointment_id DESC limit 0'.','.'1) as last_billed_amount ,'
////                    TODAY EARNINGS','WEEK EARNINGS','MONTH EARNINGS','LIFE TIME EARNINGS'
//                            . " (case  when TRUNCATE((select sum(mas_earning) from appointment where mas_id = doc.mas_id and status = 9 and DATE(appointment_dt) BETWEEN '" . $weekData['first_day_of_week'] . "' and '" . $weekData['last_day_of_week'] . "'),2)  IS NULL then '--' else TRUNCATE((select sum(mas_earning) from appointment where mas_id = doc.mas_id and status = 9 and DATE(appointment_dt) BETWEEN '" . $weekData['first_day_of_week'] . "' and '" . $weekData['last_day_of_week'] . "'),2) END) as WEEK_EARNINGS ,"
//                            . " (case  when TRUNCATE((select sum(mas_earning) from appointment where mas_id = doc.mas_id and status = 9 and DATE_FORMAT(appointment_dt,  '%Y-%m') = '" . $explodeDate[0] . '-' . $explodeDate[1] . "' ),2)  IS NULL then '--' else TRUNCATE((select sum(mas_earning) from appointment where mas_id = doc.mas_id and status = 9 and DATE_FORMAT(appointment_dt,  '%Y-%m') = '" . $explodeDate[0] . '-' . $explodeDate[1] . "' ),2)  END) as MONTH_EARNINGS,"
//                            . " (case  when TRUNCATE((select sum(mas_earning) from appointment where mas_id = doc.mas_id and status = 9),2)  IS NULL then '--'  else TRUNCATE((select sum(mas_earning) from appointment where mas_id = doc.mas_id and status = 9),2) END) as LIFE_TIME_EARNINGS,"
//                            . "(case  when TRUNCATE((select sum(pay_amount) from payroll where doc.mas_id = mas_id),2)  IS NULL then '--' else TRUNCATE((select sum(pay_amount) from payroll where doc.mas_id = mas_id),2) END) as PAID,"
//                            . "(case  when  TRUNCATE(((select sum(mas_earning) from appointment where mas_id = doc.mas_id and status = 9 and payment_status in (1,3))-(select sum(pay_amount) from payroll where doc.mas_id = mas_id)),2) IS NULL then '--' else TRUNCATE(((select sum(mas_earning) from appointment where mas_id = doc.mas_id and status = 9)-(select sum(pay_amount) from payroll where doc.mas_id = mas_id)),2)END) as DUE", false)
//                    ->add_column('SHOW', '<a href="' . base_url("index.php/masteradmin/DriverDetails/$1") . '"><button class="btn btn-success btn-cons" style="min-width: 83px !important;">DETAILS</button></a>
//            <a href="' . base_url("index.php/masteradmin/Driver_pay/$1") . '"><button class="btn btn-success btn-cons" style="min-width: 83px !important;">Pay</button>', 'masid')
//                    ->from('master doc', false);
//        }
//                $this->db->order_by('ap.appointment_id' ,'DESC');


        echo $this->datatables->generate();
    }

    function payroll_data_form_date($stdate = '', $enddate = '', $company_id = '') {



        $explodeDateTime = explode(' ', date("Y-m-d H:s:i"));
        $explodeDate = explode('-', $explodeDateTime[0]);
        $weekData = $this->week_start_end_by_date(date("Y-m-d H:s:i"));

        $this->load->library('Datatables');
        $this->load->library('table');

//        if ($company_id == '0')
        $query = 'a.mas_id = ' . $this->session->userdata('$LoginId') . ' and DATE(a.appointment_dt) BETWEEN "' . date('Y-m-d', strtotime($stdate)) . '" AND "' . date('Y-m-d', strtotime($enddate)) . '"';
//        else
//            $query = 'a.mas_id = doc.mas_id and  DATE(a.appointment_dt) BETWEEN "' . date('Y-m-d', strtotime($stdate)) . '" AND "' . date('Y-m-d', strtotime($enddate)) . '" and doc.company_id ="' . $company_id . '"';

        $this->datatables->select('distinct doc.mas_id as masid,doc.first_name,'
//                 .'(select count(appointment_id) from appointment where mas_id = doc.mas_id and status = 9) as cmpltApts ,'
                        . "(case  when TRUNCATE((select sum(mas_earning) from appointment where mas_id = doc.mas_id and DATE(appointment_dt) = '" . $explodeDateTime[0] . "' and status = 9),2)  IS NULL then '--'  else TRUNCATE((select sum(mas_earning) from appointment where mas_id = doc.mas_id and DATE(appointment_dt) = '" . $explodeDateTime[0] . "' and status = 9),2) END) as TODAY_EARNINGS ,"
//                 .'(select amount from appointment where mas_id = doc.mas_id and status = 9 order by appointment_id DESC limit 0'.','.'1) as last_billed_amount ,'
//                    TODAY EARNINGS','WEEK EARNINGS','MONTH EARNINGS','LIFE TIME EARNINGS'
                        . " (case  when TRUNCATE((select sum(mas_earning) from appointment where mas_id = doc.mas_id and status = 9 and DATE(appointment_dt) BETWEEN '" . $weekData['first_day_of_week'] . "' and '" . $weekData['last_day_of_week'] . "'),2)  IS NULL then '--' else TRUNCATE((select sum(mas_earning) from appointment where mas_id = doc.mas_id and status = 9 and DATE(appointment_dt) BETWEEN '" . $weekData['first_day_of_week'] . "' and '" . $weekData['last_day_of_week'] . "'),2) END) as WEEK_EARNINGS ,"
                        . " (case  when TRUNCATE((select sum(mas_earning) from appointment where mas_id = doc.mas_id and status = 9 and DATE_FORMAT(appointment_dt,  '%Y-%m') = '" . $explodeDate[0] . '-' . $explodeDate[1] . "' ),2)  IS NULL then '--' else TRUNCATE((select sum(mas_earning) from appointment where mas_id = doc.mas_id and status = 9 and DATE_FORMAT(appointment_dt,  '%Y-%m') = '" . $explodeDate[0] . '-' . $explodeDate[1] . "' ),2)  END) as MONTH_EARNINGS,"
                        . " (case  when TRUNCATE((select sum(mas_earning) from appointment where mas_id = doc.mas_id and status = 9),2)  IS NULL then '--'  else TRUNCATE((select sum(mas_earning) from appointment where mas_id = doc.mas_id and status = 9),2) END) as LIFE_TIME_EARNINGS,"
                        . "(case  when TRUNCATE((select sum(pay_amount) from payroll where doc.mas_id = mas_id),2)  IS NULL then '--' else TRUNCATE((select sum(pay_amount) from payroll where doc.mas_id = mas_id),2) END) as PAID,"
                        . "(case  when  TRUNCATE(((select sum(mas_earning) from appointment where mas_id = doc.mas_id and status = 9 and payment_status in (1,3))-(select sum(pay_amount) from payroll where doc.mas_id = mas_id)),2) IS NULL then '--' else TRUNCATE(((select sum(mas_earning) from appointment where mas_id = doc.mas_id and status = 9)-(select sum(pay_amount) from payroll where doc.mas_id = mas_id)),2)END) as DUE", false)
                ->add_column('SHOW', '<a href="' . base_url("index.php/masteradmin/DriverDetails/$1") . '"><button class="btn btn-success btn-cons" style="min-width: 83px !important;">DETAILS</button></a>
             <a href="' . base_url("index.php/masteradmin/Driver_pay/$1") . '">', 'a.mas_id')
                ->from(' master doc,appointment a ', false)
                ->where($query);


//                $this->db->order_by('ap.appointment_id' ,'DESC');


        echo $this->datatables->generate();
    }

    function DriverDetails() {

//        $this->load->library('Datatables');
//        $this->load->library('table');

//        $query = $this->db->query("select ap.appointment_dt,ap.appointment_id,ap.inv_id,ap.txn_id as tr_id,ap.status,ap.amount,d.email as mas_email,d.first_name as mas_fname,d.last_name as mas_lname,p.email as slv_email,p.first_name as slv_fname,p.last_name as slv_lname,c.company_id from appointment ap,master d,slave p,company_info c where d.company_id = c.company_id and ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and d.mas_id ='" . $this->session->userdata("LoginId") . "'  order by ap.appointment_id DESC")->result(); //get_where('slave', array('email' => $email, 'password' => $password));
//        return $query;



        $query = " p.slave_id = ap.slave_id and ap.mas_id = doc.mas_id and doc.mas_id='" . $this->session->userdata("LoginId") . "'";

        $this->datatables->select('ap.appointment_id,p.first_name,ap.amount, ap.app_commission, ap.pg_commission,ap.mas_earning', false)
//                ->add_column('SHOW', '<a href="' . base_url("index.php/masteradmin/Driver_pay/$1") . '"><button class="btn btn-success btn-cons" style="min-width: 83px !important;">DETAILS</button></a>', 'masid')
                ->from(' master doc,appointment ap,slave p', false)
                ->where($query);


                $this->db->order_by('ap.appointment_id' ,'DESC');


        echo $this->datatables->generate();
    }
    
    

    function DriverDetails_form_Date($stdate = '', $enddate = '') {

        $this->load->library('Datatables');
        $this->load->library('table');

        $query = 'ap.status = 9 and ap.payment_status in(1,3) and p.slave_id = ap.slave_id and ap.mas_id="' . $this->session->userdata("LoginId") . '" and DATE(ap.appointment_dt) BETWEEN "' . date('Y-m-d', strtotime($stdate)) . '" AND "' . date('Y-m-d', strtotime($enddate)) . '"';

        $this->datatables->select('ap.appointment_id,p.first_name,ap.amount, ap.app_commission, ap.pg_commission,ap.mas_earning', false)
//                ->add_column('SHOW', '<a href="' . base_url("index.php/masteradmin/Driver_pay/$1") . '"><button class="btn btn-success btn-cons" style="min-width: 83px !important;">DETAILS</button></a>', 'masid')
                ->from(' master doc,appointment ap,slave p', false)
                ->where($query);


//                $this->db->order_by('ap.appointment_id' ,'DESC');


        echo $this->datatables->generate();
    }

    function Driver_pay() {

//      $query = "select * from payroll wehre mas_id='".$this->session->userdata('LoginId')."'";

        $query = "select sum(a.mas_earning) as total,m.first_name,"
                . "(select count(settled_flag) from appointment where settled_flag = 0 and mas_id = a.mas_id and mas_earning != 0 and status = 9 and payment_status IN (1,3)) as unsettled_amount_count,"
                . "(select appointment_id from appointment where settled_flag = 0 and mas_id = a.mas_id and status = 9 and payment_status IN (1,3) order by appointment_id DESC limit 0,1) as last_unsettled_appointment_id from appointment a,master m where a.mas_id = '" . $this->session->userdata('LoginId') . "' and a.mas_id = m.mas_id and settled_flag = 0 and a.status = 9 and a.payment_status in (1,3)";

        return $this->db->query($query)->result();
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

    function transection_data_form_date($stdate = '', $enddate = '', $status = '', $company_id = '') {


        $this->load->library('Datatables');
        $this->load->library('table');


        if ($status != 0 && $this->session->userdata('LoginId') != 0) {
            $query = "d.company_id = c.company_id and ap.mas_id = d.mas_id and "
                    . "ap.slave_id = p.slave_id and ap.status = 9 and payment_status in(1,3) and ap.payment_type = '" . $status . "' and"
                    . " DATE(ap.appointment_dt) BETWEEN '" . date('Y-m-d', strtotime($stdate)) . "' AND '" . date('Y-m-d', strtotime($enddate)) . "'"
                    . " and d.mas_id = '".$this->session->userdata('LoginId')."'";
        } else if ($status == 0 && $this->session->userdata('LoginId') != 0)
            $query = "d.company_id = c.company_id and ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and ap.status = 9 and payment_status in (1,3) and DATE(ap.appointment_dt) BETWEEN '" . date('Y-m-d', strtotime($stdate)) . "' AND '" . date('Y-m-d', strtotime($enddate)) . "' and d.mas_id = '" . $this->session->userdata('LoginId') . "'";
        else
            $query = "d.company_id = c.company_id and ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and ap.status = 9 and payment_status in (1,3) and DATE(ap.appointment_dt) BETWEEN '" . date('Y-m-d', strtotime($stdate)) . "' AND '" . date('Y-m-d', strtotime($enddate)) . "' ";

        $this->datatables->select("ap.appointment_id,ap.mas_id,DATE_FORMAT(ap.appointment_dt, '%b %d %Y %h:%i %p'),round((ap.discount + ap.amount),2),ap.discount,ap.amount, ap.app_commission, ap.pg_commission,ap.mas_earning, ap.txn_id,(
    case ap.status when 1 then 'Request'
     when 2   then
    'Driver accepted.'
     when 3  then
     'Driver rejected.'
     when 4  then
    'Passenger has cancelled.'
     when 5   then
    'Driver has cancelled.'
     when 6   then
    'Driver is on the way.'
     when 7  then
    'Appointment started.'
     when 8   then
    'Driver arrived.'
     when 9   then
    'Appointment completed.'
    when 10 then
    'Appointment timed out.'
    else
    'Status Unavailable.'
    END) as status_result, (case ap.payment_type  when 1 then 'card' when 2 then 'cash' END)", false)
                ->add_column('Download', '<a href="' . base_url() . '../../getPDF.php?apntId=$1" target="_blank"> <button class="btn btn-primary btn-cons">Download </button></a>', 'ap.appointment_id')
                ->from('appointment ap,master d,slave p,company_info c')
                ->where($query);
//        if($status == '2')
//            $this->datatables->unset_columns('Payment Gateway commission ('.currency.')');
        $this->db->order_by('ap.appointment_id', 'DESC');

//            ->add_column('Actions', '<img src="asdf">', 'City_id')
        echo $this->datatables->generate();
    }

//    function getTransectionData() {
//        $query = $this->db->query("select ap.appointment_dt,ap.appointment_id,ap.inv_id,ap.txn_id as tr_id,ap.status,ap.amount,d.email as mas_email,d.first_name as mas_fname,d.last_name as mas_lname,p.email as slv_email,p.first_name as slv_fname,p.last_name as slv_lname from appointment ap,master d,slave p where ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and ap.mas_id='" . $this->session->userdata("LoginId") . "'order by ap.appointment_id DESC")->result(); //get_where('slave', array('email' => $email, 'password' => $password));
//        return $query;
//    }

     function getbooking_data($status = '') {

//        return $this->db->query("select a.*,m.first_name,m.last_name,s.first_name as sfirst_name,s.last_name as slast_name from appointment a,master m,slave s where a.slave_id = s.slave_id and a.mas_id = m.mas_id ")->result();
        $this->load->library('Datatables');
        $this->load->library('table');

//        $companyid = $this->session->userdata('company_id');
//        if ($status == '11' && $this->session->userdata('company_id') == '0')
//            $query = 'a.slave_id = s.slave_id and a.mas_id = m.mas_id';
//        else if ($this->session->userdata('company_id') != '0' && $status != '11')
//            $query = 'a.slave_id = s.slave_id and a.mas_id = m.mas_id and a.status = "' . $status . '" and m.company_id = "' . $companyid . '" ';
//        else if ($this->session->userdata('company_id') == '0' && $status != '11')
//            $query = 'a.slave_id = s.slave_id and a.mas_id = m.mas_id and a.status = "' . $status . '" ';
//        else if ($status == '11' && $this->session->userdata('company_id') != '0')
            $query = 'a.slave_id = s.slave_id and a.mas_id = m.mas_id and  m.mas_id = "' . $this->session->userdata('LoginId') . '" ';

        $this->datatables->select("a.appointment_id,m.mas_id,m.first_name,s.first_name as name,a.address_line1,a.drop_addr1,DATE_FORMAT(a.appointment_dt,'%b %d %Y %h:%i %p'),a.distance_in_mts,
        (
    case a.status when 1 then 'Request'
     when 2   then
    'Driver accepted.'
     when 3  then
     'Driver rejected.'
     when 4  then
    'Passenger has cancelled.'
     when 5   then
    'Driver has cancelled.'
     when 6   then
    'Driver is on the way.'
     when 7  then
    'Appointment started.'
     when 8   then
    'Driver arrived.'
     when 9   then
    'Appointment completed.'
    when 10 then
    'Appointment timed out.'
    else
    'Status Unavailable.'
    END) as status_result", false)
                ->add_column('case a.status when 9 then', '<button class="btn btn-success btn-cons route_map" id="bookingid"  onclick="route_map($1)" style="min-width: 83px !important;" data="$1">Map</button>', 'a.appointment_id')
                ->from('appointment a,master m,slave s')
                ->where($query);

        $this->db->order_by('a.appointment_id', 'DESC');
//            ->add_column('Actions', '<img src="asdf">', 'City_id')
        echo $this->datatables->generate();
    }
    
     function datatable_bookings($status = '') {

        $this->load->library('Datatables');
        $this->load->library('table');

        $this->datatables->select("a.appointment_id,m.mas_id,m.first_name,s.first_name,a.address_line1,a.drop_addr1,a.appointment_dt,a.distance_in_mts")->from("appointment a,master m,slave s")->where("a.slave_id = s.slave_id and a.mas_id = m.mas_id"); //order by slave_id DESC ",false);

        echo $this->datatables->generate();
    }
    
    
     public function getDatafromdate_for_all_bookings($stdate = '', $enddate = '', $status = '', $company_id = '') {



        $this->load->library('Datatables');
        $this->load->library('table');

//            if($status == '11' && $company_id == '0')
//                $query = 'a.slave_id = s.slave_id and a.mas_id = m.mas_id and a.status';
//            else
//        if ($company_id == '0' && $status == '11')
//            $query = 'a.slave_id = s.slave_id and a.mas_id = m.mas_id  and DATE(a.appointment_dt) BETWEEN "' . date('Y-m-d', strtotime($stdate)) . '" AND "' . date('Y-m-d', strtotime($enddate)) . '"';
//        else if ($company_id != '0' && $status != '11')
//            $query = 'a.slave_id = s.slave_id and a.mas_id = m.mas_id and a.status = "' . $status . '" and m.company_id = "' . $company_id . '" and DATE(a.appointment_dt) BETWEEN "' . date('Y-m-d', strtotime($stdate)) . '" AND "' . date('Y-m-d', strtotime($enddate)) . '"';
//        else if ($status == '11' && $company_id != '0')
//            $query = 'a.slave_id = s.slave_id and a.mas_id = m.mas_id and m.company_id = "' . $company_id . '" and DATE(a.appointment_dt) BETWEEN "' . date('Y-m-d', strtotime($stdate)) . '" AND "' . date('Y-m-d', strtotime($enddate)) . '"';
//        else if ($status != '11' && $company_id == '0')
//            $query = 'a.slave_id = s.slave_id and a.mas_id = m.mas_id and a.status = "' . $status . '" and DATE(a.appointment_dt) BETWEEN "' . date('Y-m-d', strtotime($stdate)) . '" AND "' . date('Y-m-d', strtotime($enddate)) . '"';
//        else
            $query = 'a.slave_id = s.slave_id and a.mas_id = m.mas_id and a.status = "' . $status . '" and  m.mas_id = "' . $this->session->userdata('LoginId') . '"  and DATE(a.appointment_dt) BETWEEN "' . date('Y-m-d', strtotime($stdate)) . '" AND "' . date('Y-m-d', strtotime($enddate)) . '"';

        $this->datatables->select("a.appointment_id,m.mas_id,m.first_name,s.first_name as name,a.address_line1,a.drop_addr1,DATE_FORMAT(a.appointment_dt,'%b %d %Y %h:%i %p'),a.distance_in_mts,
        (
    case a.status when 1 then 'Request'
     when 2   then
    'Driver accepted.'
     when 3  then
     'Driver rejected.'
     when 4  then
    'Passenger has cancelled.'
     when 5   then
    'Driver has cancelled.'
     when 6   then
    'Driver is on the way.'
     when 7  then
    'Appointment started.'
     when 8   then
    'Driver arrived.'
     when 9   then
    'Appointment completed.'
    when 10 then
    'Appointment timed out.'
    else
    'Status Unavailable.'
    END) as status_result", false)
                 ->add_column('case a.status when 9 then', '<button class="btn btn-success btn-cons route_map" onclick="route_map()" style="min-width: 83px !important;" data="$1">Map</button>', 'a.appointment_id')
               
                ->from('appointment a,master m,slave s')
                ->where($query);

        $this->db->order_by('a.appointment_id', 'DESC');
//            ->add_column('Actions', '<img src="asdf">', 'City_id')
        echo $this->datatables->generate();
    }

    function getTransectionData() {
        $this->load->library('Datatables');
        $this->load->library('table');

//        if ($this->session->userdata('company_id') == '0')
        $query = 'd.company_id = c.company_id and d.mas_id = "' . $this->session->userdata('LoginId') . '" and ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and payment_status in(1,3)';
//        else
//            $query = 'd.company_id = c.company_id and ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and ap.status = 9 and payment_status in(1,3) and d.company_id = "' . $this->session->userdata('company_id') . '"';
//            $query = 'ap.status = 9 and ap.payment_status in(1,3)';

        $this->datatables->select("ap.appointment_id,ap.mas_id, DATE_FORMAT(ap.appointment_dt, '%b %d %Y %h:%i %p'),round((ap.discount + ap.amount),2),ap.discount,ap.amount, ap.app_commission, ap.pg_commission,ap.mas_earning, ap.txn_id,(
    case ap.status when 1 then 'Request'
     when 2   then
    'Driver accepted.'
     when 3  then
     'Driver rejected.'
     when 4  then
    'Passenger has cancelled.'
     when 5   then
    'Driver has cancelled.'
     when 6   then
    'Driver is on the way.'
     when 7  then
    'Appointment started.'
     when 8   then
    'Driver arrived.'
     when 9   then
    'Appointment completed.'
    when 10 then
    'Appointment timed out.'
    else
    'Status Unavailable.'
    END) as status_result, (case ap.payment_type  when 1 then 'card' when 2 then 'cash' END)", false)
                ->add_column('Download', '<a href="' . base_url() . '../../getPDF.php?apntId=$1" target="_blank"> <button class="btn btn-primary btn-cons">Download </button></a>', 'ap.appointment_id')
                ->from('appointment ap,master d,slave p,company_info c')
                ->where($query);

        $this->db->order_by('ap.appointment_id', 'DESC');

//            ->add_column('Actions', '<img src="asdf">', 'City_id')
        echo $this->datatables->generate();
    }

    function get_all_data($stdate, $enddate) {

        if ($stdate || $enddate) {
            $query = $this->db->query("select ap.drop_long,ap.complete_dt,ap.coupon_code,ap.discount,ap.tip_amount,ap.airport_fee,ap.toll_fee,ap.parking_fee,ap.arrive_dt,ap.waiting_mts,ap.drop_lat,ap.drop_addr1,ap.drop_addr2, ap.start_dt,ap.distance_in_mts,ap.appt_long,ap.appt_lat,ap.address_line1,ap.address_line2,ap.appointment_dt as appointment_date,ap.appointment_id as Bookin_Id,ap.inv_id,ap.txn_id as tr_id,ap.status,ap.amount,d.email as Driver_email,d.first_name as Driver_First_Name,d.last_name as Driver_Last_Name,p.email as Passenger_email,p.first_name as Passenger_fname,p.last_name as Passenger_lname from appointment ap,master d,slave p where ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and DATE(ap.appointment_dt) BETWEEN '" . date('Y-m-d', strtotime($stdate)) . "' AND '" . date('Y-m-d', strtotime($enddate)) . "' and ap.mas_id='" . $this->session->userdata("LoginId") . "'order by ap.appointment_id DESC");
        } else {
            $query = $this->db->query("select ap.drop_long,ap.complete_dt,ap.coupon_code,ap.discount,ap.tip_amount,ap.airport_fee,ap.toll_fee,ap.parking_fee,ap.arrive_dt,ap.waiting_mts,ap.drop_lat,ap.drop_addr1,ap.drop_addr2, ap.start_dt,ap.distance_in_mts,ap.appt_long,ap.appt_lat,ap.address_line1,ap.address_line2,ap.appointment_dt as appointment_date,ap.appointment_id as Bookin_Id,ap.inv_id,ap.txn_id as tr_id,ap.status,ap.amount,d.email as Driver_email,d.first_name as Driver_First_Name,d.last_name as Driver_Last_Name,p.email as Passenger_email,p.first_name as Passenger_fname,p.last_name as Passenger_lname from appointment ap,master d,slave p where ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and ap.mas_id='" . $this->session->userdata("LoginId") . "'order by ap.appointment_id DESC");
        }


        foreach ($query->result_array() as $row) {

            if ($row['status'] == '1')
                $status = 'Appointment requested';
            else if ($row['status'] == '2')
                $status = 'Driver accepted.';
            else if ($row['status'] == '3')
                $status = 'Driver rejected.';
            else if ($row['status'] == '4')
                $status = 'Passenger has cancelled.';
            else if ($row['status'] == '5')
                $status = 'You have cancelled.';
            else if ($row['status'] == '6')
                $status = 'Driver is on the way.';
            else if ($row['status'] == '7')
                $status = 'Appointment started.';
            else if ($row['status'] == '8')
                $status = 'Driver Arrived';
            else if ($row['status'] == '9')
                $status = 'Appointment completed.';
            else if ($row['status'] == '10')
                $status = 'Appointment Timed out.';
            else
                $status = 'Status unavailable.';

            $now = new DateTime($row['complete_dt']);
            $ref = new DateTime($row['start_dt']);
            $diff = $now->diff($ref);



            $data[] = array(
                'Booking_Id' => $row['Bookin_Id'],
                'appointment_Date' => $row['appointment_date'],
                'Amount' => '$' . $row['amount'],
                'App_Commission' => '$' . $row['amount'] * (10 / 100),
                'Payment_Gateway_Commission' => '$' . ((float) ($row['amount'] * (2.9 / 100)) + 0.3),
                'Driver_Earning' => '$' . (float) (($row['amount'] - ($row['amount'] * (10 / 100)) - (float) (($row['amount'] * (2.9 / 100)) + 0.3))),
                'Booking_Status' => $status,
                'Driver_Name' => $row['Driver_First_Name'],
                'Pickup_address' => $row['address_line1'] . $row['address_line2'],
                'Appointment_latitude' => $row['appt_lat'],
                'Appointment_longitude' => $row['appt_long'],
                'Pickup_date_time' => $row['start_dt'],
                'Destination' => $row['drop_addr1'] . $row['drop_addr2'],
                'Drop_latitude' => $row['drop_lat'],
                'Drop_longitude' => $row['drop_long'],
                'Drop_date_time' => $row['complete_dt'],
                'Passenger_Name' => $row['Passenger_fname'],
                'Date_time_to_pickup_point' => $row['arrive_dt'],
                'Waiting_time_minute' => $row['waiting_mts'],
                'Journey_Duration' => $diff->h . 'hours,' . $diff->i . 'minutes',
                'Toll_Fee' => $row['toll_fee'],
                'parking_fee' => $row['parking_fee'],
                'airport_fee' => $row['airport_fee'],
                'tip_amount' => $row['tip_amount'],
                'discount' => $row['discount'],
                'discount_code' => $row['coupon_code'],
            );
        }

        return $data;
    }

    function getDatafromdate($stdate, $enddate) {
        $query = $this->db->query("select ap.appointment_dt,ap.appointment_id,ap.inv_id,ap.txn_id as tr_id,ap.status,ap.amount,d.email as mas_email,d.first_name as mas_fname,d.last_name as mas_lname,p.email as slv_email,p.first_name as slv_fname,p.last_name as slv_lname from appointment ap,master d,slave p where ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and DATE(ap.appointment_dt) BETWEEN '" . date('Y-m-d', strtotime($stdate)) . "' AND '" . date('Y-m-d', strtotime($enddate)) . "' and ap.mas_id='" . $this->session->userdata("LoginId") . "'order by ap.appointment_id DESC")->result(); //get_where('slave', array('email' => $email, 'password' => $password));
        return $query;
    }

//    function getDataSelected($selectdval) {
//        $query = $this->db->query("select ap.appointment_dt,ap.payment_type,ap.appointment_id,ap.inv_id,ap.txn_id as tr_id,ap.status,ap.amount,d.email as mas_email,d.first_name as mas_fname,d.last_name as mas_lname,p.email as slv_email,p.first_name as slv_fname,p.last_name as slv_lname from appointment ap,master d,slave p where ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and ap.payment_type = '" . $selectdval . "' and ap.mas_id='" . $this->session->userdata("LoginId") . "'order by ap.appointment_id DESC")->result(); //get_where('slave', array('email' => $email, 'password' => $password));
//        return $query;
//    }



    function getDataSelected($selectdval = '') {

//        $query = $this->db->query("select ap.appointment_dt,ap.payment_type,ap.appointment_id,ap.inv_id,ap.txn_id as tr_id,ap.status,ap.amount,d.email as mas_email,d.first_name as mas_fname,d.last_name as mas_lname,p.email as slv_email,p.first_name as slv_fname,p.last_name as slv_lname,c.company_id from appointment ap,master d,slave p,company_info c where c.company_id = d.company_id and ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and ap.payment_type = '" . $selectdval . "' order by ap.appointment_id DESC LIMIT 200")->result(); //get_where('slave', array('email' => $email, 'password' => $password));
//        return $query;


        $this->load->library('Datatables');
        $this->load->library('table');
        if ($selectdval != '0' && $this->session->userdata('LoginId') != '0') {
//        $query = 'c.company_id = d.company_id and ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and ap.payment_type = "'.$selectdval .'" order by ap.appointment_id';
            $query = 'd.company_id = c.company_id and ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and ap.status = 9 and payment_status in(1,3) and ap.payment_type = "' . $selectdval . '" and d.mas_id="' . $this->session->userdata('LoginId') . '"';
        } else if ($selectdval == '0' && $this->session->userdata('LoginId') == '0') {
            $query = 'd.company_id = c.company_id and ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and ap.status = 9 and payment_status in(1,3) and d.mas_id="' . $this->session->userdata('LoginId') . '"';
        } else if ($selectdval != '0' && $this->session->userdata('LoginId') == '0') {
            $query = 'd.company_id = c.company_id and ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and ap.status = 9 and payment_status in(1,3) and ap.payment_type = "' . $selectdval . '" and d.mas_id="' . $this->session->userdata('LoginId') . '"';
        } else {
            $query = 'd.company_id = c.company_id and ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and payment_status in(1,3) and d.mas_id="' . $this->session->userdata('LoginId') . '"';
        }


        $this->datatables->select("ap.appointment_id,ap.mas_id,DATE_FORMAT(ap.appointment_dt, '%b %d %Y %h:%i %p'),round((ap.discount + ap.amount),2),ap.discount,ap.amount, ap.app_commission, ap.pg_commission,ap.mas_earning, ap.txn_id,(
    case ap.status when 1 then 'Request'
     when 2   then
    'Driver accepted.'
     when 3  then
     'Driver rejected.'
     when 4  then
    'Passenger has cancelled.'
     when 5   then
    'Driver has cancelled.'
     when 6   then
    'Driver is on the way.'
     when 7  then
    'Appointment started.'
     when 8   then
    'Driver arrived.'
     when 9   then
    'Appointment completed.'
    when 10 then
    'Appointment timed out.'
    else
    'Status Unavailable.'
    END) as status_result, (case ap.payment_type  when 1 then 'card' when 2 then 'cash' END)", false)
                ->add_column('Download', '<a href="' . base_url() . '../../getPDF.php?apntId=$1" target="_blank"> <button class="btn btn-primary btn-cons">Download </button></a>', 'ap.appointment_id')
                ->from('appointment ap,master d,slave p,company_info c')
                ->where($query);
        $this->db->order_by('ap.appointment_id', 'DESC');

//            ->add_column('Actions', '<img src="asdf">', 'City_id')
        echo $this->datatables->generate();
    }

    function getuserinfo() {
        $query = $this->db->query("SELECT docd.*,doc.license_pic,doc.profile_pic,doc.first_name,doc.last_name,doc.mobile,doc.email,doc.zipcode,doc.license_num,com.companyname FROM master doc,docdetail docd,company_info com where doc.mas_id='" . $this->session->userdata("LoginId") . "'  and doc.company_id = com.company_id ")->row();
        return $query;
    }

    function getPassangerBooking() {
        $query = $this->db->query("select a.appointment_id,a.complete_dt,a.amount,a.inv_id,a.distance_in_mts,a.appointment_dt,a.drop_addr1,a.drop_addr2,a.mas_id,a.slave_id,d.first_name as doc_firstname,d.profile_pic as doc_profile,d.last_name as doc_lastname,p.first_name as patient_firstname,p.last_name as patient_lastname,a.address_line1,a.address_line2,a.status from appointment a,master d,slave p where a.slave_id=p.slave_id and d.mas_id=a.mas_id and a.status IN (9) and a.slave_id='" . $this->session->userdata("LoginId") . "' order by a.appointment_id desc")->result(); //get_where('slave', array('email' => $email, 'password' => $password));
        return $query;
    }

    function addservices() {
        $data = $this->input->post('servicedata');
        $this->db->insert('services', $data);
    }

    function updateservices($table = '') {
        $formdataarray = $this->input->post('editservicedata');
        $id = $this->input->post('id');
        $this->db->update($table, $formdataarray, array('service_id' => $id));
    }

    function deleteservices($table = '') {
        $id = $this->input->post('id');
        $this->db->where('service_id', $id);
        $this->db->delete($table);
    }

    function getActiveservicedata() {
        $query = $this->db->query("select * from services")->result(); //get_where('slave', array('email' => $email, 'password' => $password));
        return $query;
    }

    function week_start_end_by_date($date, $format = 'Y-m-d') {

        //Is $date timestamp or date?
        if (is_numeric($date) AND strlen($date) == 10) {
            $time = $date;
        } else {
            $time = strtotime($date);
        }

        $week['week'] = date('W', $time);
        $week['year'] = date('o', $time);
        $week['year_week'] = date('oW', $time);
        $first_day_of_week_timestamp = strtotime($week['year'] . "W" . str_pad($week['week'], 2, "0", STR_PAD_LEFT));
        $week['first_day_of_week'] = date($format, $first_day_of_week_timestamp);
        $week['first_day_of_week_timestamp'] = $first_day_of_week_timestamp;
        $last_day_of_week_timestamp = strtotime($week['first_day_of_week'] . " +6 days");
        $week['last_day_of_week'] = date($format, $last_day_of_week_timestamp);
        $week['last_day_of_week_timestamp'] = $last_day_of_week_timestamp;

        return $week;
    }

    function changemasterpassword() {
        $newpassword = $this->input->post('newpass');



        $data = $this->db->query("select password from master where mas_id ='" . $this->session->userdata("LoginId") . "'")->row_array();
        ;

        if ($data['password'] == md5($newpassword)) {

            return json_encode(array('msg' => "you have entered the same password!, please enter the new password", "flag" => 1));
        } else {
            $this->db->update('master', array('password' => md5($newpassword)), array('mas_id' => $this->session->userdata("LoginId")));

            return json_encode(array('msg' => "your new password updated successfully", "flag" => 0));
        }
    }

    function updateDataProfile() {

        $formdataarray = $this->input->post('fdata');

        if (isset($formdataarray['license_pic'])) {
            $data = $this->db->query("select url from docdetail where driverid = '" . $this->session->userdata("LoginId") . "' and doctype = 1");

            if ($data->num_rows() > 0)
                $this->db->update('docdetail', array('url' => $formdataarray['license_pic']), array('driverid' => $this->session->userdata("LoginId"), 'doctype' => 1));
            else
                $this->db->insert('docdetail', array('url' => $formdataarray['license_pic']), array('driverid' => $this->session->userdata("LoginId"), 'doctype' => 1));
        }

        $this->db->update('master', $formdataarray, array('mas_id' => $this->session->userdata("LoginId")));

        $this->session->set_userdata(array('profile_pic' => $formdataarray['profile_pic'],
            'first_name' => $formdataarray['first_name'],
            'last_name' => $formdataarray['last_name']));
    }

    function updateMasterBank() {

        $stripe = new StripeModule();

        $checkStripeId = $this->db->query("SELECT stripe_id from master where mas_id = " . $this->session->userdata("LoginId"))->row();

//        if (!is_array($checkStripeId)) {
//            return array('flag' => 2);
//        }

        $userData = $this->input->post('fdata');

        if ($checkStripeId->stripe_id == '') {
            $createRecipientArr = array('name' => $userData['name'], 'type' => 'individual', 'email' => $userData['email'], 'tax_id' => $userData['tax_id'], 'bank_account' => $userData['account_number'], 'routing_number' => $userData['routing_number'], 'description' => 'For ' . $userData['email']);
            $recipient = $stripe->apiStripe('createRecipient', $createRecipientArr);
        } else {
            $updateRecipientArr = array('name' => $userData['name'], 'email' => $userData['email'], 'tax_id' => $userData['tax_id'], 'bank_account' => $userData['account_number'], 'routing_number' => $userData['routing_number'], 'description' => 'For ' . $userData['email']);
            $recipient = $stripe->apiStripe('updateRecipient', $updateRecipientArr);
        }
        if (isset($recipient['error']))
            return array('flag' => 1, 'message' => $recipient['err']['error']['message'], 'data' => $userData); //, 'args' => $recipient);
        else if ($recipient['verified'] === FALSE)
            return array('flag' => 1, 'message' => "Need your full, legal name, you can check the details with the below link<br>https://support.stripe.com/questions/how-do-i-verify-transfer-recipients", 'link' => 'https://support.stripe.com/questions/how-do-i-verify-transfer-recipients', 'data' => $userData);
        else if ($recipient['verified'] === TRUE)
            return array('flag' => 0, 'message' => "Updated bank details successfully", 'data' => $userData);
    }

    function Getdashboarddata() {
        $currTime = time();
        // today completed booking count
        $today = date('Y-m-d', $currTime);
        $todayone = $this->db->query("SELECT * FROM appointment  WHERE mas_id='" . $this->session->userdata("LoginId") . "' and date(appointment_dt) = '" . date('Y-m-d') . "' and status = 9 ");
//        $today
        //this week completed booking
        $weekArr = $this->week_start_end_by_date($currTime);
        $week = $this->db->query("SELECT *  FROM appointment  WHERE mas_id='" . $this->session->userdata("LoginId") . "' and status = 9 and DATE(appointment_dt) >= '" . $weekArr['first_day_of_week'] . "'");


        // this month completed booking

        $currMonth = date('n', $currTime);
        $month = $this->db->query("SELECT *  FROM appointment  WHERE mas_id='" . $this->session->userdata("LoginId") . "' and status = 9  and  MONTH(appointment_dt) = '" . $currMonth . "' ");


        // lifetime completed booking
        $lifetime = $this->db->query("SELECT *  FROM appointment  WHERE mas_id='" . $this->session->userdata("LoginId") . "' and status = 9 ");

        // total booking uptodate
        $totaluptodate = $this->db->query("SELECT  *  FROM appointment  WHERE mas_id='" . $this->session->userdata("LoginId") . "'");



        //today earnings
//
        $todayearning = $this->db->query("SELECT sum(mas_earning) as totamount FROM appointment  WHERE mas_id='" . $this->session->userdata("LoginId") . "' and date(appointment_dt)  = '" . date('Y-m-d') . "' and status = 9 ");

//
//
//        //this week completed booking
//
        $weekearning = $this->db->query("SELECT sum(mas_earning) as totamount  FROM appointment  WHERE mas_id='" . $this->session->userdata("LoginId") . "' and status = 9 and DATE(appointment_dt) >= '" . $weekArr['first_day_of_week'] . "'");
//
//
//        // this month completed booking
//
//
        $monthearning = $this->db->query("SELECT sum(mas_earning) as totamount  FROM appointment  WHERE mas_id='" . $this->session->userdata("LoginId") . "' and status = 9  and  MONTH(appointment_dt) = '" . $currMonth . "' ");
//
//
//        // lifetime completed booking
        $lifetimeearning = $this->db->query("SELECT sum(mas_earning) as totamount FROM appointment  WHERE mas_id='" . $this->session->userdata("LoginId") . "' and status = 9 ");
//
//        // total booking uptodate
        $totaluptodateearning = $this->db->query("SELECT  sum(mas_earning)  FROM appointment  WHERE mas_id='" . $this->session->userdata("LoginId") . "'");


        $t = $todayearning->row();
        $w = $weekearning->row();
        $m = $monthearning->row();
        $l = $lifetimeearning->row();
        $te = $totaluptodateearning->row();


          $data = array('today' => $todayone->num_rows(), 'week' => $week->num_rows(), 'month' => $month->num_rows(), 'lifetime' => $lifetime->num_rows(), 'total' => $totaluptodate->num_rows(),
            'todayearning' => $t->totamount,//(float) (($t->totamount - ($t->totamount * (10 / 100)) - (float) (($t->totamount * (2.9 / 100)) + 0.3))),
            'weekearning' => $w->totamount,//(float) (($w->totamount - ($w->totamount * (10 / 100)) - (float) (($w->totamount * (2.9 / 100)) + 0.3))), 
            'monthearning' => $m->totamount,//(float) (($m->totamount - ($m->totamount * (10 / 100)) - (float) (($m->totamount * (2.9 / 100)) + 0.3))), 
            'lifetimeearning' => $l->totamount//(float) (($l->totamount - ($l->totamount * (10 / 100)) - (float) (($l->totamount * (2.9 / 100)) + 0.3))), 'totalearning' => $te->totalearning
        );
        return $data;
//        $data = array('today' => $todayone->num_rows(), 'week' => $week->num_rows(), 'month' => $month->num_rows(), 'lifetime' => $lifetime->num_rows(), 'total' => $totaluptodate->num_rows(),
//            'todayearning' => (float) (($t->totamount - ($t->totamount * (10 / 100)) - (float) (($t->totamount * (2.9 / 100)) + 0.3))), 'weekearning' => (float) (($w->totamount - ($w->totamount * (10 / 100)) - (float) (($w->totamount * (2.9 / 100)) + 0.3))), 'monthearning' => (float) (($m->totamount - ($m->totamount * (10 / 100)) - (float) (($m->totamount * (2.9 / 100)) + 0.3))), 'lifetimeearning' => (float) (($l->totamount - ($l->totamount * (10 / 100)) - (float) (($l->totamount * (2.9 / 100)) + 0.3))), 'totalearning' => $te
//        );
//        return $data;
    }

    function updateData($IdToChange = '', $databasename = '', $db_field_id_name = '') {
        $formdataarray = $this->input->post('fdata');
        $this->db->update($databasename, $formdataarray, array($db_field_id_name => $IdToChange));
    }

    function LoadAdminList() {
        $db = new MongoClient();
        $mongoDB = $db->db_Ryland_Insurence;
        $collection = $mongoDB->Col_Manage_Admin;
        $cursor = $collection->find(array('Role' => "SubAdmin"));
        $db->close();
        return $cursor;
    }

    function issessionset() {

        if ($this->session->userdata('emailid') && $this->session->userdata('password')) {

            return true;
        }
        return false;
    }

}

?>
