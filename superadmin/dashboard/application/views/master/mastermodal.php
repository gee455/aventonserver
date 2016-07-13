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

    function getTransectionData() {
        $query = $this->db->query("select ap.appointment_dt,ap.appointment_id,ap.inv_id,ap.txn_id as tr_id,ap.status,ap.amount,d.email as mas_email,d.first_name as mas_fname,d.last_name as mas_lname,p.email as slv_email,p.first_name as slv_fname,p.last_name as slv_lname from appointment ap,master d,slave p where ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and ap.mas_id='" . $this->session->userdata("LoginId") . "'order by ap.appointment_id DESC")->result(); //get_where('slave', array('email' => $email, 'password' => $password));
        return $query;
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

    function getDataSelected($selectdval) {
        $query = $this->db->query("select ap.appointment_dt,ap.payment_type,ap.appointment_id,ap.inv_id,ap.txn_id as tr_id,ap.status,ap.amount,d.email as mas_email,d.first_name as mas_fname,d.last_name as mas_lname,p.email as slv_email,p.first_name as slv_fname,p.last_name as slv_lname from appointment ap,master d,slave p where ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and ap.payment_type = '" . $selectdval . "' and ap.mas_id='" . $this->session->userdata("LoginId") . "'order by ap.appointment_id DESC")->result(); //get_where('slave', array('email' => $email, 'password' => $password));
        return $query;
    }

    function getuserinfo() {
        $query = $this->db->query("SELECT doc.profile_pic,doc.first_name,doc.last_name,doc.mobile,doc.email,doc.zipcode,doc.license_num,com.companyname FROM master doc,docdetail docd,company_info com where doc.mas_id='" . $this->session->userdata("LoginId") . "'  and doc.company_id = com.company_id ")->row();
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

    function updateDataProfile() {

        $formdataarray = $this->input->post('fdata');
        $this->db->update('master', $formdataarray, array('mas_id' => $this->session->userdata("LoginId")));
    }

    function updateMasterBank() {

        $stripe = new StripeModule();

        $checkStripeId = $this->db->query("SELECT stripe_id from master where mas_id = " . $this->session->userdata("LoginId"))->row();

        if (!is_array($checkStripeId)) {
            return array('flag' => 2);
        }

        $userData = $this->input->post('fdata');

        if ($checkStripeId['stripe_id'] == '') {
            $createRecipientArr = array('name' => $userData['name'], 'type' => 'individual', 'email' => $userData['email'], 'tax_id' => $userData['tax_id'], 'bank_account' => $userData['account_number'], 'routing_number' => $userData['routing_number'], 'description' => 'For ' . $userData['email']);
            $recipient = $stripe->apiStripe('createRecipient', $createRecipientArr);
        } else {
            $updateRecipientArr = array('name' => $userData['name'], 'email' => $userData['email'], 'tax_id' => $userData['tax_id'], 'bank_account' => $userData['account_number'], 'routing_number' => $userData['routing_number'], 'description' => 'For ' . $userData['email']);
            $recipient = $stripe->apiStripe('updateRecipient', $updateRecipientArr);
        }
        if (isset($recipient['error']))
            return array('flag' => 1, 'message' => $recipient['error']['message']);
        else if ($recipient['verified'] === FALSE)
            return array('flag' => 1, 'message' => "Need your full, legal name, you can check the details with the below link", 'link' => 'https://support.stripe.com/questions/how-do-i-verify-transfer-recipients');
        else if ($recipient['verified'] === TRUE)
            return array('flag' => 0, 'message' => "Updated bank details successfully");
    }

    function Getdashboarddata() {
        $currTime = time();
        // today completed booking count
        $today = date('Y-m-d', $currTime);
        $todayone = $this->db->query("SELECT * FROM appointment  WHERE mas_id='" . $this->session->userdata("LoginId") . "' and appointment_dt like '" . date('Y-m-d') . "%' and status = 9 ");
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
        $todayearning = $this->db->query("SELECT sum(amount) as totamount FROM appointment  WHERE mas_id='" . $this->session->userdata("LoginId") . "' and appointment_dt  like '" . date('Y-m-d') . "%' and status = 9 ");

//
//
//        //this week completed booking
//
        $weekearning = $this->db->query("SELECT sum(amount) as totamount  FROM appointment  WHERE mas_id='" . $this->session->userdata("LoginId") . "' and status = 9 and DATE(appointment_dt) >= '" . $weekArr['first_day_of_week'] . "'");
//
//
//        // this month completed booking
//
//
        $monthearning = $this->db->query("SELECT sum(amount) as totamount  FROM appointment  WHERE mas_id='" . $this->session->userdata("LoginId") . "' and status = 9  and  MONTH(appointment_dt) = '" . $currMonth . "' ");
//
//
//        // lifetime completed booking
        $lifetimeearning = $this->db->query("SELECT sum(amount) as totamount FROM appointment  WHERE mas_id='" . $this->session->userdata("LoginId") . "' and status = 9 ");
//
//        // total booking uptodate
        $totaluptodateearning = $this->db->query("SELECT  sum(amount)  FROM appointment  WHERE mas_id='" . $this->session->userdata("LoginId") . "'");


        $t = $todayearning->row();
        $w = $weekearning->row();
        $m = $monthearning->row();
        $l = $lifetimeearning->row();
        $te = $totaluptodateearning->row();


        $data = array('today' => $todayone->num_rows(), 'week' => $week->num_rows(), 'month' => $month->num_rows(), 'lifetime' => $lifetime->num_rows(), 'total' => $totaluptodate->num_rows(),
            'todayearning' => (float) (($t->totamount - ($t->totamount * (10 / 100)) - (float) (($t->totamount * (2.9 / 100)) + 0.3))), 'weekearning' => (float) (($w->totamount - ($w->totamount * (10 / 100)) - (float) (($w->totamount * (2.9 / 100)) + 0.3))), 'monthearning' => (float) (($m->totamount - ($m->totamount * (10 / 100)) - (float) (($m->totamount * (2.9 / 100)) + 0.3))), 'lifetimeearning' => (float) (($l->totamount - ($l->totamount * (10 / 100)) - (float) (($l->totamount * (2.9 / 100)) + 0.3))), 'totalearning' => $te
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
