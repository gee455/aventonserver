<?php

if (!defined("BASEPATH"))
    exit("Direct access to this page is not allowed");

require_once 'StripeModule.php';

class Companymodal extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
//        $this->load->model('mastermodal');
        $this->load->database();
    }
  

    function validateSuperAdmin() {

        $email = $this->input->post("email");
        $password = $this->input->post("password");

        $queryforslave = $this->db->get_where('company_info', array('email' => $email, 'password' => $password));
        
       

        if ($queryforslave->num_rows > 0) {
                        
             $res = $queryforslave->row();
             
               if ($res->Status == '1')
                return array('Message' => 'Your profile is under verification, please wait for our representative to reach you.');
            else if ($res->Status == '5')
                return array('Message' => 'Your profile is suspended by admin, please contact your company for further queries');

            $tablename = 'company_info';
            $LoginId = 'company_id';
            $sessiondata = $this->setsessiondata($tablename, $LoginId, $res,$email,$password);
            $this->session->set_userdata($sessiondata);
            return true;
        }
        exit();
        return false;
    }

    function setsessiondata($tablename, $LoginId, $res,$email,$password) {
        $sessiondata = array(
            'emailid' => $email,
            'password' => $password,
            'LoginId' => $res->$LoginId,
            'profile_pic' => $res->logo,
            'first_name' => $res->companyname,
            'table' => $tablename,
            'validate' => true
        );
        return $sessiondata;
    }

      protected function _generateRandomString($length) {

        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';

        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $randomString;
    }
    
//    function ForgotPassword(){
//        $email = $this->input->post('resetemail');
//        
//        $query = $this->db->query("select company_id from company_info where email = '".$email."'");
//        if($query->num_rows() <= 0)
//        {
//            echo JSON_ENCODE(array('msg' => "this email doesnt exist",'flag' =>'1'));
//        }
//        else if($query->num_rows() > 0)
//            {
//              $randData = $this->_generateRandomString(20) . '_3';
//
//            $mail = new sendAMail($this->host);
//            $resetRes = $mail->forgotPassword($userData, $randData);
//
//            if ($resetRes['flag'] == 0) {
//                $updateResetDataQry = "update $table set resetData = '" . $randData . "', resetFlag = 1 where email = '" . $args['ent_email'] . "'";
//                mysql_query($updateResetDataQry, $this->db->conn);
////$resetRes['update'] = $updateResetDataQry;
//                echo JSON_ENCODE(array('msg' => "password reset instructions are sent to your mail", 'flag' => '0'));
//            } else {
//                echo JSON_ENCODE(array('msg' => "unable to reset the password please try again later", 'flag' => '1'));
//            }
//            
//            }
//    }
//    
    
    function datatable_drivers($for = '', $status = '') {
        
//        $today = getdate();
//        print_r($today);
//        exit();

        $this->load->library('Datatables');
        $this->load->library('table');
        $company = $this->session->userdata('LoginId');


        if ($for == 'my') {
           
                $whererc = "mas.status IN ('" . $status . "') and mas.company_id = '" . $this->session->userdata('LoginId'). "' ";

            if ($status == 1) {

                $this->datatables->select("mas.mas_id as rahul,mas.first_name ,mas.last_name ,mas.mobile, mas.email,mas.created_dt,mas.profile_pic as pp,"
                                . "(select companyname from company_info where company_id = mas.company_id) as companyname1,"
                                . "(select (case type when 2 then 'android_icon.png' when 1 then 'iphone-logo.png' END) from user_sessions where oid = mas.mas_id order by oid DESC limit 0,1) as type_img ,"
                            . "(select Vehicle_Image from workplace where workplace_id = mas.workplace_id ) as vehicleimage", false)
//                        ->unset_column('vehicleimage')
                        ->unset_column('type_img')
                        ->unset_column('pp')
                        ->add_column('PROFILE PIC', '<img src="' . base_url() . '../../pics/$1" width="50px" height="50px">', 'pp')
                        ->add_column('DEVICE TYPE', '<img src="' . base_url() . '../../admin/assets/$1" width="50px">', 'type_img')
                         ->add_column('VEHICLE IMAGE', '<img src="' . base_url() . '../../pics/$1" width="50px">', 'vehicleimage')
                        ->add_column('LATITUDE', "get_lat/$1", 'rahul')
                        ->add_column('LONGITUDE', "get_lon/$1", 'rahul')
                        ->add_column('SELECT', '<input type="checkbox" class="checkbox" name="checkbox" value="$1"/>', 'rahul')
                        ->from("master mas")
                        ->where("mas.status IN ('" . $status . "') and mas.company_id = '".$company."'");
            } else if ($status == 3 || $status == 4) {
                $this->datatables->select("mas.mas_id as rahul,mas.first_name ,mas.last_name ,mas.mobile, mas.email,mas.created_dt,mas.profile_pic as pp,"
                                . "(select companyname from company_info where company_id = mas.company_id ) as companyname1,"
                                . "(select (case type when 2 then 'android_icon.png' when 1 then 'iphone-logo.png' END) from user_sessions where oid = mas.mas_id order by oid DESC limit 0,1) as type_img,"
                                    . "(select Vehicle_Image from workplace where workplace_id = mas.workplace_id ) as vehicleimage", false)
//                           ->unset_column('vehicleimage')
                        ->unset_column('type_img')
                        ->unset_column('pp')
                        ->add_column('PROFILE PIC', '<img src="' . base_url() . '../../pics/$1" width="50px" height="50px">', 'pp')
                        ->add_column('DEVICE TYPE', '<img src="' . base_url() . '../../admin/assets/$1" width="50px">', 'type_img')
                        ->add_column('VEHICLE IMAGE', '<img src="' . base_url() . '../../pics/$1" width="50px">', 'vehicleimage')
                        ->add_column('LATITUDE', "get_lat/$1", 'rahul')
                        ->add_column('LONGITUDE', "get_lon/$1", 'rahul')
                        ->add_column('SELECT', '<input type="checkbox" class="checkbox" name="checkbox" value="$1"/>', 'rahul')
                        ->from("master mas")
                        ->where($whererc);
            }
        } else if ($for == 'mo') {

            $m = new MongoClient();
            $this->load->library('mongo_db');

            $db = $this->mongo_db->db;

            $selecttb = $db->location;
            $darray = $latlong = array();
            if ($status == 3) { //online or free
                $drivers = $selecttb->find(array('status' => (int) $status));

                foreach ($drivers as $mas_id) {
                    $darray[] = $mas_id['user'];
                    $latlong[$mas_id['user']] = array($mas_id['location']['latitude'], $mas_id['location']['longitude']);
//                    $latlong[$mas_id['user'].'lat']=$mas_id['location']['latitude'];
//                    $location[$mas_id['user']] = $mas_id['location'];
                }
            } elseif ($status == 567) {//booked
                $drivers = $selecttb->find(array('status' => array('$in' => array(5, 6, 7))));
                foreach ($drivers as $mas_id) {
                    $darray[] = $mas_id['user'];

                    $latlong[$mas_id['user']] = array($mas_id['location']['latitude'], $mas_id['location']['longitude']);
//                    $latlong[$mas_id['user'].'log']=$mas_id['location']['longitude'];
//                    $latlong[$mas_id['user'].'lat']=$mas_id['location']['latitude'];
//                     $location[$mas_id['user']] = $mas_id['location'];
                }
            } elseif ($status == 30) {//OFFLINE
                $drivers = $selecttb->find(array('status' => 4));
                foreach ($drivers as $mas_id) {
                    $darray[] = $mas_id['user'];
                    $latlong[$mas_id['user']] = array($mas_id['location']['latitude'], $mas_id['location']['longitude']);
//                    $latlong[$mas_id['user'].'log']=$mas_id['location']['longitude'];
//                    $latlong[$mas_id['user'].'lat']=$mas_id['location']['latitude'];
//                     $location[$mas_id['user']] = $mas_id['location'];
                }
            }

            $mas_ids = implode(',', $darray);
            if ($mas_ids == '')
                $mas_ids = 0;
            $companywhere = '';
//            if ($company != '0') {
                $companywhere = "and mas.company_id= '".$this->session->userdata('LoginId')."'";
//                $companywhere = "and mas.company_id= '".$company."'";
//            }

//            print_r($latlong);return false;

            $this->datatables->select("mas.mas_id as rahul,mas.first_name ,mas.last_name ,mas.mobile, mas.email,mas.created_dt,mas.profile_pic as pp,"
                            . "(select companyname from company_info where company_id = mas.company_id),"
                            . "(select (case type when 2 then 'android_icon.png' when 1 then 'iphone-logo.png' END) from user_sessions where oid = mas.mas_id order by oid DESC limit 0,1) as type_img,"
                            . "(select uniq_identity from workplace where workplace_id = mas.workplace_id) as vehicleid,"
                            . "(select Vehicle_Image from workplace where workplace_id = mas.workplace_id ) as vehicleimage", false)
                    ->unset_column('vehicleimage')
                    ->unset_column('type_img')
                    ->unset_column('pp')
                    ->add_column('PROFILE PIC', '<img src="' . base_url() . '../../pics/$1" width="50px">', 'pp')
                    ->add_column('DEVICE TYPE', '<img src="' . base_url() . '../../admin/assets/$1" width="50px">', 'type_img')
                    ->add_column('VEHICLE IMAGE', '<img src="' . base_url() . '../../pics/$1" width="50px">', 'vehicleimage')
                    ->add_column('LATITUDE', "get_lat/$1", 'rahul')
                        ->add_column('LONGITUDE', "get_lon/$1", 'rahul')
                    ->add_column('SELECT', '<input type="checkbox" class="checkbox" name="checkbox" value="$1"/>', 'rahul')
                    ->from("master mas")
                    ->where("mas.mas_id IN (" . $mas_ids . ")".$companywhere);


//        $quaery = $this->db->query("SELECT mas.mas_id, mas.first_name ,mas.zipcode, mas.profile_pic, mas.last_name, mas.email, mas.mobile, mas.status,mas.created_dt,(select type from user_sessions where oid = mas.mas_id order by oid DESC limit 0,1) as dev_type FROM master mas where  mas.mas_id IN (" . $mas_ids . ")  order by mas.mas_id DESC")->result();
//        return $quaery;
        }

        $this->db->order_by("rahul", "desc");
        echo $this->datatables->generate();
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


    function validateEmail(){

        $query = $this->db->query("select mas_id from master where email='".$this->input->post('email')."'");
        if($query->num_rows() > 0){

            echo json_encode(array('msg'=> '1'));
        }else{
            echo json_encode(array('msg'=> '0'));
        }

    }
    
    
     function get_payrolldata($id = '') {
        $quaery = $this->db->query("SELECT * from payroll WHERE  mas_id = '" . $id . "'")->result();
//        $quaery = $this->db->query("SELECT due_amount,closing_balance,pay_date,pay_date,opening_balance,mas_id,trasaction_id,payroll_id,sum(pay_amount) as totalpaid from payroll  WHERE  mas_id = '" . $id . "'")->result();
        return $quaery;
    }

    function Totalamountpaid($id = '') {
        $quaery = $this->db->query("SELECT sum(pay_amount) as totalamt from payroll WHERE  mas_id = '" . $id . "'")->result();
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
            $wereclousetocome = "a.mas_id = doc.mas_id and  doc.company_id ='" . $this->session->userdata('LoginId'). "'";


            
            
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
                    ->add_column('SHOW', '<a href="' . base_url("index.php/companyadmin/DriverDetails/$1") . '"><button class="btn btn-success btn-cons" style="min-width: 83px !important;">DETAILS</button></a>
            <a href="' . base_url("index.php/companyadmin/Driver_pay/$1") . '"><button class="btn btn-success btn-cons" style="min-width: 83px !important;">Pay</button>', 'masid')
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
//                    ->add_column('SHOW', '<a href="' . base_url("index.php/companyadmin/DriverDetails/$1") . '"><button class="btn btn-success btn-cons" style="min-width: 83px !important;">DETAILS</button></a>
//            <a href="' . base_url("index.php/companyadmin/Driver_pay/$1") . '"><button class="btn btn-success btn-cons" style="min-width: 83px !important;">Pay</button>', 'masid')
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
//            $query = 'a.mas_id = doc.mas_id and DATE(a.appointment_dt) BETWEEN "' . date('Y-m-d', strtotime($stdate)) . '" AND "' . date('Y-m-d', strtotime($enddate)) . '"';
//        else
            $query = 'a.mas_id = doc.mas_id and  DATE(a.appointment_dt) BETWEEN "' . date('Y-m-d', strtotime($stdate)) . '" AND "' . date('Y-m-d', strtotime($enddate)) . '" and doc.company_id ="' . $this->session->userdata("LoginId") . '"';

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
                ->add_column('SHOW', '<a href="' . base_url("index.php/companyadmin/DriverDetails/$1") . '"><button class="btn btn-success btn-cons" style="min-width: 83px !important;">DETAILS</button></a>
             <a href="' . base_url("index.php/companyadmin/Driver_pay/$1") . '"><button class="btn btn-success btn-cons" style="min-width: 83px !important;">Pay</button>', 'masid')
                ->from(' master doc,appointment a ', false)
                ->where($query);


//                $this->db->order_by('ap.appointment_id' ,'DESC');


        echo $this->datatables->generate();
    }
    function datatable(){

        $this->load->library('Datatables');
        $this->load->library('table');

        $explodeDateTime = explode(' ', date("Y-m-d H:s:i"));
        $explodeDate = explode('-', $explodeDateTime[0]);
        $weekData = $this->week_start_end_by_date(date("Y-m-d H:s:i"));

//        $this->datatables->query("select doc.mas_id,doc.first_name,doc.workplace_id, doc.last_name, doc.email, doc.license_num,doc.license_exp,
//                                          doc.board_certification_expiry_dt, doc.mobile, doc.status, doc.profile_pic,
//                                           (select count(appointment_id) from appointment where mas_id = doc.mas_id and status = 9) as cmpltApts,
//                                            (select sum(amount) from appointment where mas_id = doc.mas_id and DATE(appointment_dt) = '" . $explodeDateTime[0] . "' and status = 9) as today_earnings,
//                                             (select amount from appointment where mas_id = doc.mas_id and status = 9 order by appointment_id DESC limit 0, 1) as last_billed_amount,
//                                              (select sum(amount) from appointment where mas_id = doc.mas_id and status = 9 and DATE(appointment_dt) BETWEEN '" . $weekData['first_day_of_week'] . "' and '" . $weekData['last_day_of_week'] . "') as week_earnings,
//                                              (select sum(amount) from appointment where mas_id = doc.mas_id and status = 9 and DATE_FORMAT(appointment_dt, '%Y-%m') = '" . $explodeDate[0] . '-' . $explodeDate[1] . "') as month_earnings,
//                                               (select sum(amount) from appointment where mas_id = doc.mas_id and status = 9) as total_earnings
//                                               from master doc where doc.company_id = '" . $this->session->userdata("LoginId") . "'");
//        $this->datatables->query('select * from city');
        $this->datatables->select('doc.mas_id,doc.first_name,doc.workplace_id, doc.last_name, doc.email, doc.license_num,doc.license_exp,doc.board_certification_expiry_dt, doc.mobile, doc.status, doc.profile_pic')
            ->select('(select count(appointment_id) from appointment where mas_id = doc.mas_id and status = 9) as cmpltApts')
            ->select("(select sum(amount) from appointment where mas_id = doc.mas_id and DATE(appointment_dt) = '" . $explodeDateTime[0] . "' and status = 9) as today_earnings")
            ->select('(select amount from appointment where mas_id = doc.mas_id and status = 9 order by appointment_id DESC limit 0'.','.'1) as last_billed_amount',false)
            ->select("(select sum(amount) from appointment where mas_id = doc.mas_id and status = 9 and DATE(appointment_dt) BETWEEN '" . $weekData['first_day_of_week'] . "' and '" . $weekData['last_day_of_week'] . "') as week_earnings")
            ->select("(select sum(amount) from appointment where mas_id = doc.mas_id and status = 9 and DATE_FORMAT(appointment_dt,  '%Y-%m') = '" . $explodeDate[0] . '-' . $explodeDate[1] . "' ) as month_earnings",false)
            ->select("(select sum(amount) from appointment where mas_id = doc.mas_id and status = 9) as total_earnings")
            ->from('master doc');
//        $this->datatables->select('count(appointment_id) as cmpltApts')->from('appointment')->where('mas_id = doc.mas_id and status = 9');
//        $this->datatables->select('sum(amount) as today_earnings')->from('appointment')->where('mas_id = doc.mas_id DATE(appointment_dt) = "' . $explodeDateTime[0] . '"and status = 9');
        echo $this->datatables->generate();


    }

    
     function Driver_pay($masid = '') {

//      $query = "select * from payroll wehre company_id='".$this->session->userdata('LoginId')."'";

        $query = "select sum(a.mas_earning) as total,m.first_name,"
                . "(select count(settled_flag) from appointment where settled_flag = 0 and mas_id = a.mas_id and mas_earning != 0 and status = 9 and payment_status IN (1,3)) as unsettled_amount_count,"
                . "(select appointment_id from appointment where settled_flag = 0 and mas_id = a.mas_id and status = 9 and payment_status IN (1,3) order by appointment_id DESC limit 0,1) as last_unsettled_appointment_id from appointment a,master m where a.mas_id = '" . $masid . "' and a.mas_id = m.mas_id and settled_flag = 0 and a.status = 9 and a.payment_status in (1,3)";
        return $this->db->query($query)->result();
    }
    
   
      function addNewDriverData() {
   
          
          $datai['first_name'] = $this->input->post('firstname');
        $datai['last_name'] = $this->input->post('lastname');
        $pass = $this->input->post('password');
        $datai['password'] = md5($pass);
        $datai['created_dt'] = date('y/m/d h:i:s a', time());
        $datai['type_id'] = 1;
        $datai['status'] = 1;
        $datai['email'] = $this->input->post('email');
        $datai['mobile'] = $this->input->post('mobile');
        $datai['zipcode'] = $this->input->post('zipcode');
        $datai['company_id'] = $this->session->userdata('LoginId');
        $expirationrc = $this->input->post('expirationrc');
//        $datai['company_id'] = $this->session->userdata('LoginId');

        $name = $_FILES["certificate"]["name"];
        $ext = substr($name, strrpos($name, '.') + 1); //explode(".", $name); # extra () to prevent notice //1  doctype
        $cert_name = (rand(1000, 9999) * time()) . '.' . $ext;

        $insurname = $_FILES["photos"]["name"];
        $ext1 = substr($insurname, strrpos($insurname, '.') + 1); //explode(".", $insurname);
        $insurance_name = (rand(1000, 9999) * time()) . '.' . $ext1;

        $carriagecert = $_FILES["passbook"]["name"];
        $ext2 = substr($carriagecert, strrpos($carriagecert, '.') + 1); //explode(".", $carriagecert); 2 doctype
        $carriage_name = (rand(1000, 9999) * time()) . '.' . $ext2;



        $documentfolder = $_SERVER['DOCUMENT_ROOT'] . '/' . mainfolder . '/pics/';

        try {
            move_uploaded_file($_FILES['certificate']['tmp_name'], $documentfolder . $cert_name);
            if (move_uploaded_file($_FILES['photos']['tmp_name'], $documentfolder . $insurance_name)) {
                $this->uploadimage_diffrent_redulation($documentfolder . $insurance_name, $insurance_name, $_SERVER['DOCUMENT_ROOT'] . '/' . mainfolder . '/', $ext1);
            }
            move_uploaded_file($_FILES['passbook']['tmp_name'], $documentfolder . $carriage_name);
        } catch (Exception $ex) {
            print_r($ex);
            return false;
        }

        $datai['license_pic'] = $cert_name;
        $datai['profile_pic'] = $insurance_name;
//             $datai['profile_pic']=$carriage_name;


        $this->db->insert('master', $datai);
        $newdriverid = $this->db->insert_id();
        $docdetail = array('url' => $cert_name, 'expirydate' => date("Y-m-d", strtotime($expirationrc)), 'doctype' => 1, 'driverid' => $newdriverid);
        $this->db->insert('docdetail', $docdetail);
        $docdetail = array('url' => $carriage_name, 'expirydate' => '0000-00-00', 'doctype' => 2, 'driverid' => $newdriverid);
        $this->db->insert('docdetail', $docdetail);

//        print_r($datai);



        $this->load->library('mongo_db');
        $curr_date = time();
        $curr_gmt_dates = gmdate('Y-m-d H:i:s', $curr_date);
        $curr_gmt_date = new MongoDate(strtotime($curr_gmt_dates));
        $mongoArr = array("type" => 0, "user" => (int) $newdriverid, "name" => $datai['first_name'], "lname" => $datai['last_name'],
            "location" => array(
                "longitude" => 0,
                "latitude" => 0
            ), "image" => $carriage_name, "rating" => 0, 'status' => 1, 'email' => strtolower($datai['email']), 'dt' => $curr_gmt_date
        );

        $this->mongo_db->insert('location', $mongoArr);
//
//        $mail = new sendAMail($db1->host);
//        $err = $mail->sendMasWelcomeMail(strtolower($email), ucwords($firstname));


        return true;
    }
    
      function deletedriver() {
        $masterid = $this->input->post('masterid');

//        $result = $this->db->query("delete from master where mas_id ='" . $masterid . "'")->row_array();
        $this->load->library('mongo_db');
        $affectedRows = 0;

        foreach ($masterid as $row) {
            $getMasterDet = $this->db->query("select * from master where mas_id = '" . $row . "'")->row_array();

//        }
            if (!is_array($getMasterDet)) {

                echo json_encode(array('flag' => 1, 'affectedRows' => $affectedRows, 'msg' => 'Driver not available'));
                return false;
            }
            $location = $this->mongo_db->delete('location', array('user' => (int) $row));



            $updateCarQry = $this->db->query("update workplace set status = 2 where workplace_id = '" . $getMasterDet['car_id'] . "'");
            $affectedRows += $this->db->affected_rows();

            $updateMysqlDriverQry = $this->db->query("delete from master where mas_id = '" . $row . "'");
            $affectedRows += $this->db->affected_rows();

            $updateMysqlApptQry = $this->db->query("delete from appointment where mas_id = '" . $getMasterDet['item_list'] . "'");
            $affectedRows += $this->db->affected_rows();

            $updateMysqlReviewQry = $this->db->query("delete from passenger_rating where mas_id = '" . $getMasterDet['item_list'] . "'");
            $affectedRows += $this->db->affected_rows();

            $updateMysqlReviewQry = $this->db->query("delete from master_ratings where mas_id = '" . $getMasterDet['item_list'] . "'");
            $affectedRows += $this->db->affected_rows();

            $updateMysqlReviewQry = $this->db->query("delete from user_sessions where user_type = 1 and oid = '" . $getMasterDet['item_list'] . "'");
            $affectedRows += $this->db->affected_rows();
        }

        echo json_encode(array('flag' => 0, 'affectedRows' => $affectedRows, 'msg' => 'Process completed.'));
    }
    
    
     function editdriver($status = '') {
//        $driverid = $this->input->post('val');

        $data['masterdata'] = $this->db->query("select * from master where mas_id ='" . $status . "' ")->result();

        $data['masterdoc'] = $this->db->query("select * from docdetail where driverid ='" . $status . "' ")->result();

        return $data;
    }
    
    
     function editdriverdata() {

        $driverid = $this->input->post('driver_id');

        $first_name = $this->input->post('firstname');
        $last_name = $this->input->post('lastname');
        $password = $this->input->post('password');
        $created_dt = date('y/m/d h:i:s a', time());
        $type_id = 1;
       
        $email = $this->input->post('email');
        $mobile = $this->input->post('mobile');
        $zipcode = $this->input->post('zipcode');
        $expirationrc = $this->input->post('expirationrc');
//        $datai['company_id'] = $this->session->userdata('LoginId');

        $name = $_FILES["certificate"]["name"];
        $ext = substr($name, strrpos($name, '.') + 1); //explode(".", $name); # extra () to prevent notice //1  doctype
        $cert_name = (rand(1000, 9999) * time()) . '.' . $ext;

        $insurname = $_FILES["photos"]["name"];
        $ext1 = substr($insurname, strrpos($insurname, '.') + 1); //explode(".", $insurname);
        $profilepic = (rand(1000, 9999) * time()) . '.' . $ext1;

        $carriagecert = $_FILES["passbook"]["name"];
        $ext2 = substr($carriagecert, strrpos($carriagecert, '.') + 1); //explode(".", $carriagecert); 2 doctype
        $carriage_name = (rand(1000, 9999) * time()) . '.' . $ext2;



        $documentfolder = $_SERVER['DOCUMENT_ROOT'] . '/' . mainfolder . '/pics/';

        try {
            move_uploaded_file($_FILES['certificate']['tmp_name'], $documentfolder . $cert_name);
            if (move_uploaded_file($_FILES['photos']['tmp_name'], $documentfolder . $profilepic)) {
                $this->uploadimage_diffrent_redulation($documentfolder . $profilepic, $profilepic, $_SERVER['DOCUMENT_ROOT'] . '/' . mainfolder . '/', $ext1);
            }
            move_uploaded_file($_FILES['passbook']['tmp_name'], $documentfolder . $carriage_name);
        } catch (Exception $ex) {
            print_r($ex);
            return false;
        }

        $license_pic = $cert_name;
        $profile_pic = $profilepic;
//             $datai['profile_pic']=$carriage_name;

        if ($insurname != '')
            $driverdetails = array('first_name' => $first_name, 'last_name' => $last_name, 'profile_pic' => $profile_pic, 'license_pic' => $license_pic,
                'password' => $password, 'created_dt' => $created_dt, 'type_id' => $type_id, 'mobile' => $mobile, 'zipcode' => $zipcode);
        else
            $driverdetails = array('first_name' => $first_name, 'last_name' => $last_name, 'license_pic' => $license_pic,
                'password' => $password, 'created_dt' => $created_dt, 'type_id' => $type_id, 'mobile' => $mobile, 'zipcode' => $zipcode);

        $this->db->where('mas_id', $driverid);
        $this->db->update('master', $driverdetails);

         $data = $this->db->query("select * from docdetail where driverid = '" . $driverid . "' and doctype = 1");

        if ($data->num_rows() > 0) {

            $docdetail = array('url' => $license_pic, 'expirydate' => date("Y-m-d", strtotime($expirationrc)));
            $this->db->where('driverid', $driverid);
            $this->db->where('doctype', 1);
            $this->db->update('docdetail', $docdetail);
        } else {
            $this->db->insert('docdetail', array('doctype' => 1, 'driverid' => $driverid, 'url' => $license_pic, 'expirydate' => date("Y-m-d", strtotime($expirationrc))));
        }

        $data = $this->db->query("select * from docdetail where driverid = '" . $driverid . "' and doctype = 2");

        if ($data->num_rows > 0) {
            $docdet = array('url' => $carriage_name, 'expirydate' => '0000-00-00');
            $this->db->where('driverid', $driverid);
            $this->db->where('doctype', 2);
            $this->db->update('docdetail', $docdet);
        } else {
            $this->db->insert('docdetail', array('doctype' => 1, 'driverid' => $driverid, 'url' => $carriage_name, 'expirydate' => '0000-00-00'));
        }
//
//        $docdetail = array('url' => $license_pic, 'expirydate' => date("Y-m-d", strtotime($expirationrc)));
//        $this->db->where('driverid', $driverid);
//        $this->db->where('doctype', 1);
//        $this->db->update('docdetail', $docdetail);
//
//        $docdet = array('url' => $carriage_name, 'expirydate' => '0000-00-00');
//        $this->db->where('driverid', $driverid);
//        $this->db->where('doctype', 2);
//        $this->db->update('docdetail', $docdet);

//       
//
        $this->load->library('mongo_db');
        $curr_date = time();
        $curr_gmt_dates = gmdate('Y-m-d H:i:s', $curr_date);
        $curr_gmt_date = new MongoDate(strtotime($curr_gmt_dates));

        if ($insurname != '')
            $mongoArr = array("name" => $first_name, "lname" => $last_name, "image" => $insurname);
        else
            $mongoArr = array("name" => $first_name, "lname" => $last_name);

        $this->mongo_db->update('location', $mongoArr, array('user' => $driverid));

//        $mail = new sendAMail($db1->host);
//        $err = $mail->sendMasWelcomeMail(strtolower($email), ucwords($firstname));


        return true;
    }
    
    
    
    function documentgetdata() {
        $val = $this->input->post("val");
        /* \
         * [doc_ids] => 367
          [driverid] => 830
          [url] => 8204124114494.jpg
          [expirydate] => 2014-05-31
          [doctype] => 1
         */
        $return = array();
        foreach ($val as $row) {
            $data = $this->db->query("select * from docdetail where driverid = '" . $row . "'")->result();
        }
        foreach ($data as $doc) {
            $return[] = array('doctype' => $doc->doctype, 'url' => $doc->url, 'expirydate' => $doc->expirydate);
        }
        return $return;
    }
    
    function getvehicletype(){
      $res =  $this->db->query("select * from workplace_types")->result();
      return $res;
    }
    
    
    function editdriverpassword() {
        $newpass = $this->input->post('newpass');
        $val = $this->input->post('val');

        $pass = $this->db->query("select password from master where mas_id='" . $val . "' and password = md5('".$newpass."')")->result();

        if (!empty($pass)) {
            echo json_encode(array('msg' => "this password already exists. Enter new password", 'flag' => 1));
            return;
        } 
        else {
            $this->db->query("update master set password = md5('" . $newpass . "') where mas_id = '" . $val . "' ");

            if ($this->db->affected_rows() > 0) {
                echo json_encode(array('msg' => "your new password updated successfully", 'flag' => 0));
                return;
            }
        }
    }
    
    
     function loadcompany() {
        $cityid = $this->input->post('city');
        $Result = $this->db->query("select * from company_info where city=" . $cityid . " and status = 3 ")->result_array();
        return $Result;
    }
    
    
    function  uploadimage_diffrent_redulation($file_to_open,$imagename,$servername,$ext){


        list($width, $height) = getimagesize($file_to_open);

        $ratio = $height / $width;



        /* mdpi 36*36 */
        $mdpi_nw = 36;
        $mdpi_nh = $ratio * 36;

        $mtmp = imagecreatetruecolor($mdpi_nw, $mdpi_nh);

        if ($ext == "jpg" || $ext == "jpeg") {
            $new_image = imagecreatefromjpeg($file_to_open);
        } else if ($ext == "gif") {
            $new_image = imagecreatefromgif($file_to_open);
        } else if ($ext == "png") {
            $new_image = imagecreatefrompng($file_to_open);
        }
        imagecopyresampled($mtmp, $new_image, 0, 0, 0, 0, $mdpi_nw, $mdpi_nh, $width, $height);

        $mdpi_file = $servername.'pics/mdpi/' . $imagename;

        imagejpeg($mtmp, $mdpi_file, 100);

        /* HDPI Image creation 55*55 */
        $hdpi_nw = 55;
        $hdpi_nh = $ratio * 55;

        $tmp = imagecreatetruecolor($hdpi_nw, $hdpi_nh);

        if ($ext == "jpg" || $ext == "jpeg") {
            $new_image = imagecreatefromjpeg($file_to_open);
        } else if ($ext == "gif") {
            $new_image = imagecreatefromgif($file_to_open);
        } else if ($ext == "png") {
            $new_image = imagecreatefrompng($file_to_open);
        }
        imagecopyresampled($tmp, $new_image, 0, 0, 0, 0, $hdpi_nw, $hdpi_nh, $width, $height);

        $hdpi_file = $servername.'pics/hdpi/' . $imagename;

        imagejpeg($tmp, $hdpi_file, 100);

        /* XHDPI 84*84 */
        $xhdpi_nw = 84;
        $xhdpi_nh = $ratio * 84;

        $xtmp = imagecreatetruecolor($xhdpi_nw, $xhdpi_nh);

        if ($ext == "jpg" || $ext == "jpeg") {
            $new_image = imagecreatefromjpeg($file_to_open);
        } else if ($ext == "gif") {
            $new_image = imagecreatefromgif($file_to_open);
        } else if ($ext == "png") {
            $new_image = imagecreatefrompng($file_to_open);
        }
        imagecopyresampled($xtmp, $new_image, 0, 0, 0, 0, $xhdpi_nw, $xhdpi_nh, $width, $height);

        $xhdpi_file = $servername.'pics/xhdpi/' . $imagename;

        imagejpeg($xtmp, $xhdpi_file, 100);

        /* xXHDPI 125*125 */
        $xxhdpi_nw = 125;
        $xxhdpi_nh = $ratio * 125;

        $xxtmp = imagecreatetruecolor($xxhdpi_nw, $xxhdpi_nh);

        if ($ext == "jpg" || $ext == "jpeg") {
            $new_image = imagecreatefromjpeg($file_to_open);
        } else if ($ext == "gif") {
            $new_image = imagecreatefromgif($file_to_open);
        } else if ($ext == "png") {
            $new_image = imagecreatefrompng($file_to_open);
        }
        imagecopyresampled($xxtmp, $new_image, 0, 0, 0, 0, $xxhdpi_nw, $xxhdpi_nh, $width, $height);

        $xxhdpi_file = $servername.'pics/xxhdpi/' . $imagename;

        imagejpeg($xxtmp, $xxhdpi_file, 100);
    }
    
    
       function transection_data_form_date($stdate = '', $enddate = '', $status = '', $company_id = '') {


        $this->load->library('Datatables');
        $this->load->library('table');

//            if($status == '11' && $company_id == '0')
//                $query = 'a.slave_id = s.slave_id and a.mas_id = m.mas_id and a.status';
//            else
//        if($company_id == '0')
//            $query = 'a.slave_id = s.slave_id and a.mas_id = m.mas_id and a.status = "'.$status.'" and DATE(a.appointment_dt) BETWEEN "' . date('Y-m-d', strtotime($stdate)) . '" AND "' . date('Y-m-d', strtotime($enddate)).'"';
//        else
//            $query = 'a.slave_id = s.slave_id and a.mas_id = m.mas_id and a.status = "'.$status.'" and m.company_id = "'.$company_id.'" and DATE(a.appointment_dt) BETWEEN "' . date('Y-m-d', strtotime($stdate)) . '" AND "' . date('Y-m-d', strtotime($enddate)).'"';


        if ($status != 0 && $company_id != 0) {
            $query = "d.company_id = c.company_id and d.company_id = '" . $company_id . "' and ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and ap.status = 9 and payment_status in(1,3) and ap.payment_type = '" . $status . "' and DATE(ap.appointment_dt) BETWEEN '" . date('Y-m-d', strtotime($stdate)) . "' AND '" . date('Y-m-d', strtotime($enddate)) . "'";
        } else if ($status == 0 && $company_id != 0)
            $query = "d.company_id = c.company_id and d.company_id = '" . $company_id . "' and ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and ap.status = 9 and payment_status in(1,3) and DATE(ap.appointment_dt) BETWEEN '" . date('Y-m-d', strtotime($stdate)) . "' AND '" . date('Y-m-d', strtotime($enddate)) . "'";
        else if ($status != 0 && $company_id == 0)
            $query = "d.company_id = c.company_id and  ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and ap.status = 9 and payment_status in(1,3) and DATE(ap.appointment_dt) BETWEEN '" . date('Y-m-d', strtotime($stdate)) . "' AND '" . date('Y-m-d', strtotime($enddate)) . "' ";
        else
            $query = "d.company_id = c.company_id and ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and ap.status = 9 and payment_status in(1,3) and DATE(ap.appointment_dt) BETWEEN '" . date('Y-m-d', strtotime($stdate)) . "' AND '" . date('Y-m-d', strtotime($enddate)) . "' ";

//echo $query;
//        exit();

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
    
    
    
     function getDataSelected($selectdval = '') {

//        $query = $this->db->query("select ap.appointment_dt,ap.payment_type,ap.appointment_id,ap.inv_id,ap.txn_id as tr_id,ap.status,ap.amount,d.email as mas_email,d.first_name as mas_fname,d.last_name as mas_lname,p.email as slv_email,p.first_name as slv_fname,p.last_name as slv_lname,c.company_id from appointment ap,master d,slave p,company_info c where c.company_id = d.company_id and ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and ap.payment_type = '" . $selectdval . "' order by ap.appointment_id DESC LIMIT 200")->result(); //get_where('slave', array('email' => $email, 'password' => $password));
//        return $query;


        $this->load->library('Datatables');
        $this->load->library('table');
        if ($selectdval != '0' && $this->session->userdata('company_id') != '0') {
//        $query = 'c.company_id = d.company_id and ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and ap.payment_type = "'.$selectdval .'" order by ap.appointment_id';
            $query = 'd.company_id = c.company_id and ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and ap.status = 9 and payment_status in(1,3) and ap.payment_type = "' . $selectdval . '" and d.company_id="' . $this->session->userdata('LoginId') . '"';
        } else if ($selectdval == '0' && $this->session->userdata('company_id') == '0') {
            $query = 'd.company_id = c.company_id and ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and ap.status = 9 and payment_status in(1,3) and d.company_id="' . $this->session->userdata('LoginId') . '"';
        } else if ($selectdval != '0' && $this->session->userdata('company_id') == '0') {
            $query = 'd.company_id = c.company_id and ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and ap.status = 9 and payment_status in(1,3) and ap.payment_type = "' . $selectdval . '" and d.company_id="' . $this->session->userdata('LoginId') . '"';
        } else {
            $query = 'd.company_id = c.company_id and ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and ap.status = 9 and payment_status in(1,3) and d.company_id="' . $this->session->userdata('LoginId') . '"';
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
    
     function getTransectionData() {
        $this->load->library('Datatables');
        $this->load->library('table');

//        if ($this->session->userdata('company_id') == '0')
//            $query = 'd.company_id = c.company_id and ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and ap.status = 9 and payment_status in(1,3)';
//        else
            $query = 'd.company_id = c.company_id and ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and ap.status = 9 and payment_status in(1,3) and d.company_id = "' . $this->session->userdata('LoginId') . '"';
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
    
    
      function AddNewVehicleData(){

//        'expirydate' => date("Y-m-d", strtotime($expirationrc)),

        $title = $this->input->post('title');
        $vehiclemodel = $this->input->post('vehiclemodel');
        $vechileregno = $this->input->post('vechileregno');
        $licenceplaetno = $this->input->post('licenceplaetno');
        $vechilecolor = $this->input->post('vechilecolor');
        $type_id = $this->input->post('getvechiletype');
        $expirationrc = $this->input->post('expirationrc');

        $expirationinsurance = $this->input->post('expirationinsurance');
        $expirationpermit = $this->input->post('expirationpermit');
        $companyname = $this->session->userdata('LoginId');
        $vehicleid = $this->input->post('vehicleid'); //$this->session->userdata('LoginId');

        $insuranceno = $_REQUEST['Vehicle_Insurance_No'];


        $name = $_FILES["certificate"]["name"];
        $ext = substr($name, strrpos($name, '.') + 1); //explode(".", $name); # extra () to prevent notice
        $cert_name = (rand(1000, 9999) * time()) . '.' . $ext;

        $insurname = $_FILES["insurcertificate"]["name"];
        $ext1 = substr($insurname, strrpos($insurname, '.') + 1); //explode(".", $insurname);
        $insurance_name = (rand(1000, 9999) * time()) . '.' . $ext1;

        $carriagecert = $_FILES["carriagecertificate"]["name"];
        $ext2 = substr($carriagecert, strrpos($carriagecert, '.') + 1); //explode(".", $carriagecert);
        $carriage_name = (rand(1000, 9999) * time()) . '.' . $ext2;

        $vehicleimage = $_FILES["imagefile"]["name"];
        $text3 = substr($vehicleimage, strrpos($vehicleimage, '.') + 1);
        $image_name = (rand(1000, 999) * time()) . '.' . $text3;



        $documentfolder = $_SERVER['DOCUMENT_ROOT'] . '/' . mainfolder . '/pics/';

        try {
            move_uploaded_file($_FILES['certificate']['tmp_name'], $documentfolder . $cert_name);
            move_uploaded_file($_FILES['insurcertificate']['tmp_name'], $documentfolder . $insurance_name);
            move_uploaded_file($_FILES['carriagecertificate']['tmp_name'], $documentfolder . $carriage_name);
            move_uploaded_file($_FILES['imagefile']['tmp_name'], $documentfolder . $image_name);
        } catch (Exception $ex) {
            print_r($ex);
            return false;
        }

        $selectPrefixQry = $this->db->query("select (select LEFT(companyname,2) from company_info where company_id = '" . $companyname . "') as company_prefix,(select LEFT(type_name,2) from workplace_types where type_id = '" . $type_id . "') as type_prefix from dual")->result();

        $vehiclePrefix = strtoupper($selectPrefixQry->company_prefix) . strtoupper($selectPrefixQry->type_prefix);

        $get_last_inserted_id = $this->insertQuery($vehiclePrefix, $type_id, $title, $vehiclemodel, $vechileregno, $licenceplaetno, $vechilecolor, $companyname, $insuranceno, $image_name, $vehicleid);

//        if(!$get_last_inserted_id){
//            return false;
//        }

        $insert_doc = $this->db->query("INSERT INTO `vechiledoc`(`url`, `expirydate`, `doctype`,`vechileid`) VALUES ('" . $insurance_name . "','" . (date("Y-m-d", strtotime($expirationinsurance))) . "','2','" . $get_last_inserted_id . "'),
	('" . $cert_name . "','" . (date("Y-m-d", strtotime($expirationrc))) . "','1','" . $get_last_inserted_id . "'),
	('" . $carriage_name . "','" . (date("Y-m-d", strtotime($expirationpermit))) . "','3','" . $get_last_inserted_id . "')");



        return;
    }

    function insertQuery($vehiclePrefix, $type_id, $title, $vehiclemodel, $vechileregno, $licenceplaetno, $vechilecolor, $companyname, $insuranceno, $image_name, $vehicleid) {

        if ($vehicleid != '') {
            $uniq_id = $vehicleid;
        } else {
            $rand = rand(100000, 999999);
            $uniq_id = $vehiclePrefix . $rand; //str_pad($rand, 6, '0', STR_PAD_LEFT);
        }

        $this->db->query("INSERT INTO workplace(uniq_identity,type_id,Title,Vehicle_Model,Vehicle_Reg_No, License_Plate_No,Vehicle_Color,company,Status,Vehicle_Insurance_No,Vehicle_Image) VALUES ('" . $uniq_id . "','" . $type_id . "','" . $title . "','" . $vehiclemodel . "','" . $vechileregno . "','" . $licenceplaetno . "','" . $vechilecolor . "','" . $companyname . "','5','" . $insuranceno . "','" . $image_name . "')");

        if ($this->db->_error_number() == 1586) {
            if ($vehicleid != '') {
                return false;
            }
            return $this->insertQuery($uniq_id, $type_id, $title, $vehiclemodel, $vechileregno, $licenceplaetno, $vechilecolor, $companyname, $insuranceno, $vehicleid);
        } else {
            return $this->db->insert_id();
        }
    }

        function documentgetdatavehicles() {
        $val = $this->input->post("val");

        $vehicleImage = array();

        $return = $data = array();
        foreach ($val as $row) {
            $data = $this->db->query("select * from vechiledoc where vechileid = '" . $row . "'")->result();
//            return $data;
        }
        foreach ($data as $vehicle) {


            $return[] = array('doctype' => $vehicle->doctype, 'url' => $vehicle->url, 'expirydate' => $vehicle->expirydate);
        }
        $vehicleImage = $this->db->query("select Vehicle_Image from workplace where workplace_id = '".$val[0]."'")->row_array();
        
        $return[] = array('doctype' => '99', 'urls' => $vehicleImage['Vehicle_Image'], 'expirydate' => '' );

        return $return;
    }



//    function insertQuery($vehiclePrefix, $type_id, $title, $vehiclemodel, $vechileregno, $licenceplaetno, $vechilecolor, $companyname, $insuranceno) {
//        $rand = rand(100000, 999999);
//        $uniq_id = $vehiclePrefix . $rand;//str_pad($rand, 6, '0', STR_PAD_LEFT);
//        $this->db->query("INSERT INTO workplace(uniq_identity,type_id,Title,Vehicle_Model,Vehicle_Reg_No, License_Plate_No,Vehicle_Color,company,Status,Vehicle_Insurance_No) VALUES ('" . $uniq_id . "','" . $type_id . "','" . $title . "','" . $vehiclemodel . "','" . $vechileregno . "','" . $licenceplaetno . "','" . $vechilecolor . "','" . $companyname . "','5','" . $insuranceno . "')");
//
//        if ($this->db->_error_number() == 1586) {
//            return $this->insertQuery($vehiclePrefix, $type_id, $title, $vehiclemodel, $vechileregno, $licenceplaetno, $vechilecolor, $companyname, $insuranceno);
//        } else {
//            return $this->db->insert_id();
//        }
//    }

//    function getTransectionData() {
//        $query = $this->db->query("select ap.appointment_dt,ap.appointment_id,ap.inv_id,ap.txn_id as tr_id,ap.status,ap.amount,d.email as mas_email,d.first_name as mas_fname,d.last_name as mas_lname,p.email as slv_email,p.first_name as slv_fname,p.last_name as slv_lname,c.company_id from appointment ap,master d,slave p,company_info c where d.company_id = c.company_id and ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and c.company_id ='" . $this->session->userdata("LoginId") . "' and ap.status = 8 and ap.cancel_status not in(1,2,7) order by ap.appointment_id DESC LIMIT 200")->result(); //get_where('slave', array('email' => $email, 'password' => $password));
//        return $query;
//    }


    function DriverDetails($mas_id =''){
        $query = $this->db->query("select ap.appointment_dt,ap.appointment_id,ap.inv_id,ap.txn_id as tr_id,ap.status,"
                . "ap.amount,d.email as mas_email,d.first_name as mas_fname,d.last_name as mas_lname,"
                . "p.email as slv_email,p.first_name as slv_fname,p.last_name as slv_lname,c.company_id from appointment ap,"
                . "master d,slave p,company_info c where d.company_id = c.company_id and ap.mas_id = d.mas_id and ap.status = 9 and ap.slave_id = p.slave_id and d.mas_id ='" .$mas_id. "' and c.company_id ='" . $this->session->userdata("LoginId") . "' order by ap.appointment_id DESC")->result(); //get_where('slave', array('email' => $email, 'password' => $password));
        return $query;
    }


    function get_Drivers_from_mongo($status){

        $m = new MongoClient();
        $db = $m->roadyo_live;
        $selecttb = $db->selectCollection('location');
        $darray = array();
        if($status == 3) {//online or free
            $drivers = $selecttb->find(array('status' => (int)$status));

            foreach ($drivers as $mas_id) {
                $darray[] = $mas_id['user'];
            }
        }
        elseif($status == 4){//booked
            $drivers=$selecttb->find(array('status' => array('$in' => array(5,6,7))));
            foreach ($drivers as $mas_id) {
                $darray[] = $mas_id['user'];
            }

        } elseif($status == 5){//OFFLINE
            $drivers=$selecttb->find(array('status' => array('$nin' => array(3))));
            foreach ($drivers as $mas_id) {
                $darray[] = $mas_id['user'];
            }

        }

        $mas_ids = implode(', ',$darray);

        $quaery= $this->db->query("SELECT distinct mas.mas_id, mas.first_name ,mas.zipcode, mas.profile_pic, mas.last_name, mas.email, mas.mobile, mas.status,mas.created_dt,(select type from user_sessions where oid = mas.mas_id order by oid DESC limit 0,1) as dev_type FROM master mas where  mas.mas_id IN (" . $mas_ids . ") and mas.company_id IN (" . $this->session->userdata('LoginId') . ") order by mas.mas_id DESC")->result();
        return $quaery;

//        print_r($mas_ids);
    }

    function get_all_data($stdate, $enddate) {

        if ($stdate || $enddate) {
            $query = $this->db->query("select ap.drop_long,ap.complete_dt,ap.coupon_code,ap.discount,ap.tip_amount,ap.airport_fee,ap.toll_fee,ap.parking_fee,ap.arrive_dt,ap.waiting_mts,ap.drop_lat,ap.drop_addr1,ap.drop_addr2, ap.start_dt,ap.distance_in_mts,ap.appt_long,ap.appt_lat,ap.address_line1,ap.address_line2,ap.appointment_dt as appointment_date,ap.appointment_id as Bookin_Id,ap.inv_id,ap.txn_id as tr_id,ap.status,ap.amount,d.email as Driver_email,d.first_name as Driver_First_Name,d.last_name as Driver_Last_Name,p.email as Passenger_email,p.first_name as Passenger_fname,p.last_name as Passenger_lname,c.company_id from appointment ap,master d,slave p,company_info c where d.company_id = c.company_id and ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and DATE(ap.appointment_dt) BETWEEN '" . date('Y-m-d', strtotime($stdate)) . "' AND '" . date('Y-m-d', strtotime($enddate)) . "' and c.company_id='" . $this->session->userdata("LoginId") . "'order by ap.appointment_id DESC");
        } else {
            $query = $this->db->query("select ap.drop_long,ap.complete_dt,ap.coupon_code,ap.discount,ap.tip_amount,ap.airport_fee,ap.toll_fee,ap.parking_fee,ap.arrive_dt,ap.waiting_mts,ap.drop_lat,ap.drop_addr1,ap.drop_addr2, ap.start_dt,ap.distance_in_mts,ap.appt_long,ap.appt_lat,ap.address_line1,ap.address_line2,ap.appointment_dt as appointment_date,ap.appointment_id as Bookin_Id,ap.inv_id,ap.txn_id as tr_id,ap.status,ap.amount,d.email as Driver_email,d.first_name as Driver_First_Name,d.last_name as Driver_Last_Name,p.email as Passenger_email,p.first_name as Passenger_fname,p.last_name as Passenger_lname,c.company_id from appointment ap,master d,slave p ,company_info c where d.company_id = c.company_id and ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and c.company_id='" . $this->session->userdata("LoginId") . "'order by ap.appointment_id DESC");
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
        $query = $this->db->query("select ap.appointment_dt,ap.appointment_id,ap.inv_id,ap.txn_id as tr_id,ap.status,ap.amount,d.email as mas_email,d.first_name as mas_fname,d.last_name as mas_lname,p.email as slv_email,p.first_name as slv_fname,p.last_name as slv_lname,c.company_id from appointment ap,master d,slave p,company_info c where c.company_id = d.company_id and ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and DATE(ap.appointment_dt) BETWEEN '" . date('Y-m-d', strtotime($stdate)) . "' AND '" . date('Y-m-d', strtotime($enddate)) . "' and c.company_id='" . $this->session->userdata("LoginId") . "'order by ap.appointment_id DESC LIMIT 200")->result(); //get_where('slave', array('email' => $email, 'password' => $password));
        return $query;
    }

//    function getDataSelected($selectdval) {
//        $query = $this->db->query("select ap.appointment_dt,ap.payment_type,ap.appointment_id,ap.inv_id,ap.txn_id as tr_id,ap.status,ap.amount,d.email as mas_email,d.first_name as mas_fname,d.last_name as mas_lname,p.email as slv_email,p.first_name as slv_fname,p.last_name as slv_lname,c.company_id from appointment ap,master d,slave p,company_info c where c.company_id = d.company_id and ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and ap.payment_type = '".$selectdval."' and c.company_id='" . $this->session->userdata("LoginId") . "'order by ap.appointment_id DESC LIMIT 200")->result(); //get_where('slave', array('email' => $email, 'password' => $password));
//        return $query;
//    }

    function getuserinfo() {
        $query = $this->db->query("select * from company_info WHERE company_id='".$this->session->userdata("LoginId")."' ")->row();
        return $query;
    }

//    function getPassangerBooking() {
//        $query = $this->db->query("select a.appointment_id,a.complete_dt,a.amount,a.inv_id,a.distance_in_mts,a.appointment_dt,a.drop_addr1,a.drop_addr2,a.mas_id,a.slave_id,d.first_name as doc_firstname,d.profile_pic as doc_profile,d.last_name as doc_lastname,p.first_name as patient_firstname,p.last_name as patient_lastname,a.address_line1,a.address_line2,a.status from appointment a,master d,slave p where a.slave_id=p.slave_id and d.mas_id=a.mas_id and a.status IN (9) and a.slave_id='" . $this->session->userdata("LoginId") . "' order by a.appointment_id desc")->result(); //get_where('slave', array('email' => $email, 'password' => $password));
//        return $query;
//    }

//    function addservices() {
//        $data = $this->input->post('servicedata');
//        $this->db->insert('services', $data);
//    }
//
//    function updateservices($table = '') {
//        $formdataarray = $this->input->post('editservicedata');
//        $id = $this->input->post('id');
//        $this->db->update($table, $formdataarray, array('service_id' => $id));
//    }
//
//    function deleteservices($table = '') {
//        $id = $this->input->post('id');
//        $this->db->where('service_id', $id);
//        $this->db->delete($table);
//    }

//    function getActiveservicedata() {
//        $query = $this->db->query("select * from services")->result(); //get_where('slave', array('email' => $email, 'password' => $password));
//        return $query;
//    }

    function Vehicles($status = ''){
        $quaery= $this->db->query("select w.workplace_id,w.uniq_identity,w.Title,w.Vehicle_Model,w.type_id,w.Vehicle_Reg_No,w.License_Plate_No,w.Vehicle_Insurance_No,w.Vehicle_Color,vt.vehicletype,vm.vehiclemodel,wt.type_id,wt.type_name,ci.companyname FROM workplace w,vehicleType vt,vehiclemodel vm,workplace_types wt,company_info ci where vt.id=w.title and w.company = ci.company_id and vm.id=w.Vehicle_Model and wt.type_id =w.type_id and ci.company_id ='".$this->session->userdata("LoginId")."' and w.status ='".$status."' order by w.workplace_id desc")->result();
        return $quaery;

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

    function updateDataProfile() {

        $formdataarray = $this->input->post('fdata');
        $this->db->update('company_info', $formdataarray, array('company_id' => $this->session->userdata("LoginId")));

        $this->session->set_userdata(array('profile_pic' => $formdataarray['logo'],
            'first_name' => $formdataarray['first_name'],
            'last_name' => $formdataarray['last_name']));


    }
    
    
//    
//     function company_data() {
//        $result = $this->db->query("select * from company_info")->result();
//        return $result;
//    }

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
        $explodeDateTime = explode(' ', date("Y-m-d H:s:i"));
        $explodeDate = explode('-', $explodeDateTime[0]);
        
        $today = date('Y-m-d', $currTime);
        $todayone = $this->db->query("SELECT a.appointment_id,m.mas_id,c.company_id FROM appointment a,master m,company_info c  WHERE a.mas_id = m.mas_id and m.company_id = c.company_id and c.company_id ='" . $this->session->userdata("LoginId") . "' and a.appointment_dt like '" . date('Y-m-d') . "%' and a.status = 9 ");
//        $today
        //this week completed booking
        $weekArr = $this->week_start_end_by_date($currTime);
        $week = $this->db->query("SELECT  a.appointment_id,m.mas_id,c.company_id FROM appointment a,master m,company_info c  WHERE a.mas_id = m.mas_id and m.company_id = c.company_id and c.company_id ='" . $this->session->userdata("LoginId") . "' and a.status = 9 and DATE(a.appointment_dt) >= '" . $weekArr['first_day_of_week'] . "'");


        // this month completed booking

        $currMonth = date('n', $currTime);
        $month = $this->db->query("SELECT a.appointment_id,m.mas_id,c.company_id FROM appointment a,master m,company_info c  WHERE a.mas_id = m.mas_id and m.company_id = c.company_id and c.company_id ='" . $this->session->userdata("LoginId") . "' and a.status = 9  and  MONTH(a.appointment_dt) = '" . $currMonth . "' ");


        // lifetime completed booking
        $lifetime = $this->db->query("SELECT a.appointment_id,m.mas_id,c.company_id FROM appointment a,master m,company_info c  WHERE a.mas_id = m.mas_id and m.company_id = c.company_id and c.company_id ='" . $this->session->userdata("LoginId") . "' and a.status = 9 ");

        // total booking uptodate
        $totaluptodate = $this->db->query("SELECT  a.appointment_id,m.mas_id,c.company_id FROM appointment a,master m,company_info c  WHERE a.mas_id = m.mas_id and m.company_id = c.company_id and c.company_id ='" . $this->session->userdata("LoginId") . "'");



        //today earnings
//
        $todayearning = $this->db->query("SELECT sum(a.mas_earning) as totamount,a.appointment_id,m.mas_id,c.company_id FROM appointment a,master m,company_info c  WHERE a.mas_id = m.mas_id and m.company_id = c.company_id and c.company_id ='" . $this->session->userdata("LoginId") . "' and a.appointment_dt  like '" . date('Y-m-d') . "%' and a.status = 9 ");

//
//
//        //this week completed booking
//
        $weekearning = $this->db->query("SELECT sum(a.mas_earning) as totamount,a.appointment_id,m.mas_id,c.company_id FROM appointment a,master m,company_info c  WHERE a.mas_id = m.mas_id and m.company_id = c.company_id and c.company_id ='" . $this->session->userdata("LoginId") . "' and a.status = 9 and DATE(a.appointment_dt) >= '" . $weekArr['first_day_of_week'] . "'");
//
//
//        // this month completed booking
//
//
        $monthearning = $this->db->query("SELECT sum(a.mas_earning) as totamount,a.appointment_id,m.mas_id,c.company_id FROM appointment a,master m,company_info c  WHERE a.mas_id = m.mas_id and m.company_id = c.company_id and c.company_id ='" . $this->session->userdata("LoginId") . "' and a.status = 9  and  DATE_FORMAT(appointment_dt,  '%Y-%m') = '" . $explodeDate[0] . '-' . $explodeDate[1] . "'");
//
//
//        // lifetime completed booking
        $lifetimeearning = $this->db->query("SELECT sum(a.mas_earning) as totamount, a.appointment_id,m.mas_id,c.company_id FROM appointment a,master m,company_info c  WHERE a.mas_id = m.mas_id and m.company_id = c.company_id and c.company_id ='" . $this->session->userdata("LoginId") . "' and a.status = 9 ");
//
//        // total booking uptodate
        $totaluptodateearning = $this->db->query("SELECT  sum(a.mas_earning) as totalearning, a.appointment_id,m.mas_id,c.company_id FROM appointment a,master m,company_info c  WHERE a.mas_id = m.mas_id and m.company_id = c.company_id and c.company_id ='" . $this->session->userdata("LoginId") . "'");


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
//        $db->close();
        return $cursor;
    }
    
     function datatable_vehicles($status = '') {

        $this->load->library('Datatables');
        $this->load->library('table');

//        $city = $this->session->userdata('city_id');
//        $company = $this->session->userdata('company_id');
//        if (($city != '0') && ($company == '0'))
//            $citylist = ' and wt.city_id = "' . $city . '"';
//        else if (($city != '0') && ($company != '0'))
            $citylist = ' and w.company = "' . $this->session->userdata('LoginId') . '"';

//        $compCond = "";


        if ($status == '12')
            $status = '1,2';

        $this->datatables->select('w.workplace_id,w.uniq_identity,'
                        . '(select vehicletype from vehicleType where id = w.Title),'
                        . '(select vehiclemodel from vehiclemodel where id = w.Vehicle_Model),'
                        . '(select type_name from workplace_types  where type_id = w.type_id),'
                        . '(select companyname from company_info  where company_id = w.company),'
                        . 'w.Vehicle_Reg_No,w.License_Plate_No,w.Vehicle_Insurance_No,w.Vehicle_Color')
                ->unset_column('w.workplace_id')
                ->add_column('select', '<input type="checkbox" class="checkbox" name="checkbox" value= "$1"/>', 'w.workplace_id')
                ->from('workplace w,vehicleType vt,vehiclemodel vm,workplace_types wt,company_info ci ')
                ->where('vt.id = w.title and w.company = ci.company_id and vm.id=w.Vehicle_Model and wt.type_id = w.type_id  and w.status IN (' . $status . ')' . $citylist); //order by slave_id DESC ",false);
        $this->db->order_by("w.workplace_id", "desc");


        echo $this->datatables->generate();
    }
    

    function issessionset() {

        if ($this->session->userdata('emailid') && $this->session->userdata('password')) {

            return true;
        }
        return false;
    }
    
    
    
    ////naveena///////
    
//     function datatable_vehicles($status = '') {
//
//        $this->load->library('Datatables');
//        $this->load->library('table');
//
//        $city = $this->session->userdata('city_id');
//        $company = $this->session->userdata('company_id');
//        if (($city != '0') && ($company == '0'))
//            $citylist = ' and wt.city_id = "' . $city . '" ';
//        else if (($city != '0') && ($company != '0'))
//            $citylist = ' and w.company = "' . $company . '"';
//
////        $compCond = "";
//    
//        if ($status == '12')
//            $status = '1,2';
//
//        $this->datatables->select('w.workplace_id,w.uniq_identity,'
//                        . '(select vehicletype from vehicleType where id = w.Title),'
//                        . '(select vehiclemodel from vehiclemodel where id = w.Vehicle_Model),'
//                        . '(select type_name from workplace_types  where type_id = w.type_id),'
////                        . '(select companyname from company_info  where company_id = w.company),'
//                        . 'w.Vehicle_Reg_No,w.License_Plate_No,w.Vehicle_Insurance_No,w.Vehicle_Color')
//                ->unset_column('w.workplace_id')
//                ->add_column('select', '<input type="checkbox" class="checkbox" name="checkbox" value= "$1"/>', 'w.workplace_id')
//                ->from('workplace w,vehicleType vt,vehiclemodel vm,workplace_types wt,company_info ci ')
//                ->where('vt.id = w.title and vm.id=w.Vehicle_Model and wt.type_id = w.type_id  and w.status IN (' . $status . ')'); //order by slave_id DESC ",false);
//        $this->db->order_by("w.workplace_id", "desc");
////        $this->datatables->select('w.workplace_id,w.uniq_identity,vt.vehicletype,vm.vehiclemodel,wt.type_name,w.Vehicle_Reg_No,w.License_Plate_No,w.Vehicle_Insurance_No,w.Vehicle_Color',false)
////            ->unset_column('w.workplace_id')
////            ->add_column('select', '<input type="checkbox" class="checkbox" name="checkbox" value= "$1"/>', 'w.workplace_id')
////            ->from('workplace w,vehicleType vt,vehiclemodel vm,workplace_types wt ')
////            ->where('vt.id = w.title and vm.id=w.Vehicle_Model and wt.type_id = w.type_id  and w.status = "' . $status . '" ' . $compCond); //order by slave_id DESC ",false);
//
//
//        echo $this->datatables->generate();
//    }

}

?>