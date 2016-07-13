<?php

if (!defined("BASEPATH"))
    exit("Direct access to this page is not allowed");

require_once 'StripeModule.php';
require 'aws.phar';
require_once 'AwsPush.php';

class Superadminmodal extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
//        $this->load->model('mastermodal');
        $this->load->database();
    }

    function validateSuperAdmin() {

        $email = $this->input->post("email");
        $password = $this->input->post("password");


        $queryforslave = $this->db->get_where('superadmin', array('username' => $email, 'password' => md5($password)));
        $res = $queryforslave->row();


        if ($queryforslave->num_rows > 0) {
            $tablename = 'company_info';
            $LoginId = 'company_id';
            //$sessigetZoneCitiesondata = $this->setsessiondata($tablename, $LoginId, $res, $email, $password);
            $sessiondata = $this->setsessiondata($tablename, $LoginId, $res, $email, $password);
            $this->session->set_userdata($sessiondata);
            return true;
        }

        return false;
    }

    function get_appointment_details() {


        $city = $this->session->userdata('city_id');
        $company = $this->session->userdata('company_id');
        $query_new = " appointment a,slave p where  a.slave_id=p.slave_id   and a.appointment_id ='" . $this->input->post('app_id') . "'";
        if ($city != '0')
            $query_new = '  appointment a,slave p,master m,company_info ci where ci.company_id = m.company_id and  m.company_id = "' . $company . '" and a.mas_id = m.mas_id and a.slave_id=p.slave_id  and a.appointment_id ="' . $this->input->post('app_id') . '" LIMIT 200 ';


        $query = $this->db->query("select (select type_name from workplace_types where type_id = a.type_id)as vt_name,a.appointment_id,a.complete_dt,a.appointment_dt,a.B_type,a.address_line1,a.address_line2,a.apprxAmt,a.drop_addr1,a.drop_addr2,a.mas_id,a.slave_id,p.first_name as pessanger_fname,p.last_name as pessanger_lname,p.phone,a.status,(select first_name from master where mas_id = a.mas_id) as first_name,(select mobile from master where mas_id = a.mas_id) as mobile from  " . $query_new)->result();

        echo json_encode(array('data' => $query));
    }

    function GetRechargedata_ajax() {

        $this->load->library('Datatables');
        $this->load->library('table');
        $this->datatables->select("m.last_name,m.mas_id,m.first_name,ROUND(((select sum(RechargeAmount) from DriverRecharge where m.mas_id = mas_id) - (select COALESCE(sum(app_owner_pl),0) from appointment where status = 9  and mas_id = m.mas_id)),2),(select RechargeDate from DriverRecharge where m.mas_id = mas_id order by id desc limit 1)", false)
                ->edit_column('m.last_name', 'counter/$1', 'm.mas_id')
                ->add_column('OPERATION', '<a href="' . base_url("index.php/superadmin/DriverRechargeStatement/$1") . '"><button class="btn btn-success btn-cons" style="min-width: 83px !important;">STATEMENT</button></a>
            <a href="' . base_url("index.php/superadmin/Recharge/$1") . '"><button class="btn btn-success btn-cons" style="min-width: 83px !important;">RECHARGE</button>', 'm.mas_id')
                ->from('master m');

        echo $this->datatables->generate();
    }

    function DriverRechargeStatement($id) {

        $this->load->library('Datatables');
        $this->load->library('table');
        $this->datatables->select("ap.OpeningBal,ap.appointment_id,ROUND(ap.app_owner_pl,2),ap.ClosingBal", false)
                ->from('appointment ap')
                ->where('ap.mas_id', $id);
        echo $this->datatables->generate();
    }

    function RechargeOperation($for, $id, $masid) {

        $message = "something went wrong try after some time.";

        if ($for == 0) {
            $amount = $id; // here id is nothing but amount
            $query = "insert into DriverRecharge(mas_id,RechargeDate,RechargeAmount)values('" . $masid . "',now(),'" . $amount . "')";
            $flag = $this->db->query($query);
            if ($flag)
                $message = 'Added Amount to wallet.';
        }
        else if ($for == 1) {
            // $id is nothing but amount
            $query = "update DriverRecharge set RechargeAmount = '" . $id . "' where  id ='" . $masid . "'";
            $flag = $this->db->query($query);
            if ($flag)
                $message = 'Updated Amount.';
        }
        else if ($for == 2) {
            // here id is nothing but amount
            $query = "delete from DriverRecharge where id ='" . $id . "'";
            $flag = $this->db->query($query);
            if ($flag)
                $message = 'Deleted Amount.';
            return 44;
        }

        echo json_encode(array('error' => $message));
    }

    function GetDriverDetils($id) {

        $mas = $this->db->query("select * from master where mas_id = '" . $id . "'")->row();
        return $mas;
    }

    function zones_data() {

        $this->load->library('mongo_db');
        $res = $this->mongo_db->get('zones');
        return $res;
    }

    function getZoneCities() {
        $city_id = $this->input->post('city_id');
        $this->load->library('mongo_db');

        $res = $this->mongo_db->get('zones', array('city' => $city_id));

        $data = array();
        foreach ($res as $r) {
            $data [] = $r;
        }

        echo '<pre>';
        echo json_encode($data);
        echo '</pre>';
        return $data;
    }

    function NotificationData() {
        $city_id = $this->input->post('city_id');
        $this->load->library('mongo_db');

        $dbinstance = $this->mongo_db->get_where('AdminNotifications', array('city' => $city_id));

        foreach ($dbinstance as $res)
            $dataInprocess[] = $res;



        $datatosend = array();
        $Mas_ids = array();

        foreach ($dataInprocess as $res) {

            $City_name = $this->db->query("select * from city_available where City_Id ='" . $res['city'] . "'")->row_array();
            foreach ($res['user_ids'] as $a)
                $Mas_ids [] = $a;


            $mas_ids = implode(',', array_filter(array_unique($Mas_ids)));

            if ($res['user_type'] == 1) {
                $d = $this->db->query("select * from master where mas_id in (" . $mas_ids . ")")->result();
                foreach ($d as $row) {
                    $datatosend[] = array('city_name' => $City_name['City_Name'], 'user_type' => $res['user_type'], 'dname' => $row->first_name, 'demail' => $row->email, 'dmobile' => $row->mobile, 'ddate' => $res['DateTime'], 'msg' => $res['msg'], 'city_id' => $res['city'], 'd_id' => $row->mas_id);
                }
            } else {
                $d = $this->db->query("select * from slave where slave_id in (" . $mas_ids . ")")->result();
                foreach ($d as $row) {
                    $datatosend[] = array('city_name' => $City_name['City_Name'], 'user_type' => $res['user_type'], 'dname' => $row->first_name, 'pemail' => $row->email, 'pmobile' => $row->phone, 'pdate' => $res['DateTime'], 'msg' => $res['msg'], 'city_id' => $res['city'], 'p_id' => $row->slave_id);
                }
            }
        }

        function compareByName($a, $b) {
            return strcmp($a["dname"], $b["dname"]);
        }

        usort($datatosend, 'compareByName');

        return $datatosend;
    }

    function NotificationDataAll() {
//        $city_id = $this->input->post('city_id');
        $this->load->library('mongo_db');

        $dbinstance = $this->mongo_db->get_where('AdminNotifications');

        foreach ($dbinstance as $res)
            $dataInprocess[] = $res;



        $datatosend = array();
        $Mas_ids = array();

        foreach ($dataInprocess as $res) {

            $City_name = $this->db->query("select * from city_available where City_Id ='" . $res['city'] . "'")->row_array();
            foreach ($res['user_ids'] as $a)
                $Mas_ids [] = $a;


            $mas_ids = implode(',', array_filter(array_unique($Mas_ids)));

            if ($res['user_type'] == 1) {
                $d = $this->db->query("select * from master where mas_id in (" . $mas_ids . ")")->result();
                foreach ($d as $row) {
                    $datatosend[] = array('city_name' => $City_name['City_Name'], 'user_type' => $res['user_type'], 'dname' => $row->first_name, 'demail' => $row->email, 'dmobile' => $row->mobile, 'ddate' => $res['DateTime'], 'msg' => $res['msg'], 'city_id' => $res['city'], 'd_id' => $row->mas_id);
                }
            } else {
                $d = $this->db->query("select * from slave where slave_id in (" . $mas_ids . ")")->result();
                foreach ($d as $row) {
                    $datatosend[] = array('city_name' => $City_name['City_Name'], 'user_type' => $res['user_type'], 'dname' => $row->first_name, 'pemail' => $row->email, 'pmobile' => $row->phone, 'pdate' => $res['DateTime'], 'msg' => $res['msg'], 'city_id' => $res['city'], 'p_id' => $row->slave_id);
                }
            }
        }

        function compareByName($a, $b) {
            return strcmp($a["dname"], $b["dname"]);
        }

        usort($datatosend, 'compareByName');


        return $datatosend;
    }

    function cityForZones() {
        return $this->db->query("select City_Name,City_Id,City_Lat,City_Long from city_available ORDER BY City_Name ASC ")->result();
    }

    function tripDetails($param) {
        $this->load->library('mongo_db');
        $data['res'] = $this->mongo_db->get_one('booking_route', array('bid' => (int) $param));
        $data['appt_data'] = $this->db->query("select * from appointment where appointment_id = '" . $param . "' and status = '9'")->row();

        $driver_id = $data['appt_data']->mas_id;
        $appointment_id = $data['appt_data']->appointment_id;
        $data['driver_data'] = $this->db->query("select * from master where mas_id = '" . $driver_id . "'")->row();

        $slave_id = $data['appt_data']->slave_id;
        $data['customer_data'] = $this->db->query("select * from slave where slave_id = '" . $slave_id . "'")->row();

        $type_id = $data['appt_data']->type_id;
        $data['car_data'] = $this->db->query("select * from workplace_types where type_id = '" . $type_id . "'")->row();


        $data['master_rating_data'] = $this->db->query("select * from master_ratings where mas_id = '" . $driver_id . "' and appointment_id = '" . $appointment_id . "'")->row();

        return $data;
    }

    function DriverRechargeDetails($id) {

        $this->load->library('Datatables');
        $this->load->library('table');
        $this->datatables->select("id,RechargeAmount,DATE_FORMAT(RechargeDate, '%b %d %Y %h:%i %p') as rdat,mas_id", false)
                ->add_column('OPERATION', '<button class="btn btn-success btn-cons-onclick" style="min-width: 83px !important;" id="$1">EDIT</button>
            <a href="' . base_url("index.php/superadmin/RechargeOperation/2/$1/$2") . '"><button class="btn btn-success btn-cons" style="min-width: 83px !important;">DELETE</button>', 'id,mas_id')
                ->unset_column('mas_id')
                ->from('DriverRecharge')
                ->where('mas_id', $id);

        echo $this->datatables->generate();
    }

//    function compareByName($a, $b) {
//            return strcmp($a["dname"], $b["dname"]);
//          } 
    function get_notifieduser($usertype) {

        $this->load->library('Datatables');
        $this->load->library('table');
        $this->load->library('mongo_db');
        $db = $this->mongo_db->db;
        $dbinstance = $db->selectCollection('AdminNotifications')->find(array('user_type' => (int) $usertype));



        foreach ($dbinstance as $res)
            $dataInprocess[] = $res;

//        print_r($dataInprocess);
//         $this->db->query("select * from city_available where City_Id = )->result();


        $datatosend = array();
        $Mas_ids = array();

        foreach ($dataInprocess as $res) {

            $City_name = $this->db->query("select * from city_available where City_Id ='" . $res['city'] . "'")->row_array();
            foreach ($res['user_ids'] as $a)
                $Mas_ids [] = $a;


            $mas_ids = implode(',', array_filter(array_unique($Mas_ids)));

            if ($res['user_type'] == 1) {
                $d = $this->db->query("select * from master where mas_id in (" . $mas_ids . ")")->result();
                foreach ($d as $row) {
                    $datatosend[] = array('city_name' => $City_name['City_Name'], 'user_type' => $res['user_type'], 'dname' => $row->first_name, 'demail' => $row->email, 'dmobile' => $row->mobile, 'msg' => $res['msg'], 'ddate' => $res['DateTime'], 'city_id' => $res['city'], 'd_id' => $row->mas_id);
                }
            } else {
                $d = $this->db->query("select * from slave where slave_id in (" . $mas_ids . ")")->result();
                foreach ($d as $row) {

                    $datatosend[] = array('city_name' => $City_name['City_Name'], 'user_type' => $res['user_type'], 'dname' => $row->first_name, 'pemail' => $row->email, 'pmobile' => $row->phone, 'msg' => $res['msg'], 'pdate' => $res['DateTime'], 'city_id' => $res['city'], 'p_id' => $row->slave_id);
                }
            }
        }


//          usort($datatosend, 'compareByName');


        return $datatosend;
    }

    //Get the email ids
    function show_allEmails() {
        $userType = $this->input->post('userType');
        if ($userType == 1) {
            $Result = $this->db->query("select email from master")->result();
            return $Result;
        } else {
            $Result = $this->db->query("select email from slave")->result();
            return $Result;
        }
    }

    function ForgotPassword() {

        $useremail = $this->input->post('resetemail');
    }

    //* naveena models *//


    function dt_passenger($status = '') {

        $this->load->library('Datatables');
        $this->load->library('table');



        $this->datatables->select("s.slave_id as rahul,s.first_name,s.last_name,s.phone,s.email,s.created_dt,s.profile_pic,"
                        . "(select (case type when 2 then 'android_icon.png' when 1 then 'iphone-logo.png' END) from user_sessions where oid = rahul and user_type = 2 order by oid DESC limit 0,1) as dtype", FALSE)
                ->unset_column('dtype')
                ->unset_column('s.profile_pic')
                ->add_column('PROFILE PIC', '<img src="' . base_url() . '../../pics/$1" width="50px" class="imageborder">', 's.profile_pic')
                ->add_column('DEVICE TYPE', '<img src="' . base_url() . '../../admin/assets/$1" width="50px" >', 'dtype')
                ->add_column('select', '<input type="checkbox" class="checkbox" name="checkbox" value= "$1"/>', 'rahul')
                ->from('slave s')
                ->where('s.status', $status);
        $this->db->order_by("rahul", "desc");

        echo $this->datatables->generate();
    }

    function get_joblogsdata($value = '') {

//        
        $m = new MongoClient();
        $this->load->library('mongo_db');

        $db1 = $this->mongo_db->db;
        $logs = $db1->selectCollection('driver_log');

//        $masId = $_REQUEST['MasId'];

        $mas = $this->db->query("select email from master where mas_id = '" . $value . "'")->row();

//        $getMasEmail = "select email from master where mas_id=" . $masId;
//        $result1 = mysql_query($getMasEmail, $db1->conn);
//        $mas = mysql_fetch_assoc($result1);



        $getAllLogs = $logs->find(array('mas_email' => $mas->email))->sort(array("on_time" => 1));
        foreach ($getAllLogs as $l) {
            $minimumTimeStamp = $l['on_time'];
            break;
        }
        $currentDate = date('Y-m-d');
        $startDate = date('Y-m-d', $minimumTimeStamp);

//                $date1 = date_create($currentDate);
//                $date2 = date_create($startDate);
//                $diff = date_diff($currentDate, $startDate);
        $diff = abs(strtotime($currentDate) - strtotime($startDate));
        $days = floor($diff / (60 * 60 * 24));
//                echo $startDate . '-' . $currentDate . '-' . $days;

        $NextDay = $startDate;
        $totalData = 0;
        $dataByDay = array();
        for ($i = 0; $i <= $days; $i++) {


            $startTime = strtotime($NextDay . ' 00:00:01');
            $endTime = strtotime($NextDay . ' 23:23:59');
//                    echo $startTime . '-' . $endTime . '-' . $NextDay . '-----';
            $getAllTodayLogs = $logs->find(array('mas_email' => $mas->email,
                'on_time' => array('$gte' => $startTime),
                'off_time' => array('$lte' => $endTime)));
            $dataByDay[$i]['Date'] = $NextDay;
            $getData = array();
            $ii = 0;

            $lat1 = 0;
            $long1 = 0;
            $lat2 = 0;
            $long2 = 0;
            $dictance = 0;
            foreach ($getAllTodayLogs as $oneDay) {

                foreach ($oneDay['location'] as $latlngs) {
                    if ($lat1 == 0 && $long1 == 0) {
                        $lat1 = $latlngs['latitude'];
                        $long1 = $latlngs['longitude'];
                    } else {
                        $lat2 = $latlngs['latitude'];
                        $long2 = $latlngs['longitude'];
                        $dictance+=(double) $this->distance($lat1, $long1, $lat2, $long2, 'M');
                    }
                }
//                        $oneDay['Distance'] = $dictance;
//                        $getData[] = $oneDay;
                $ii++;
            }
            $dataByDay[$i]['Distance'] = $dictance;
            $dataByDay[$i]['total'] = $ii;
            $totalData = $i;
            $date1 = str_replace('-', '/', $NextDay);
            $NextDay = date('Y-m-d', strtotime($date1 . "+1 days"));
        }
//                print_r($dataByDay);


        $getLogs = $logs->find(array('mas_email' => $mas->email))->sort(array("on_time" => -1));
        $count = 1;

        $data = array();

        for ($Count = $totalData; $Count >= 0; $Count--) {
            if ($dataByDay[$Count]['total'] > 0) {


//                echo '<tr>';
                $sr = $Count + 1;

                $data[] = array('sr' => $sr, 'Date' => $dataByDay[$Count]['Date'], 'total' => $dataByDay[$Count]['total'], 'distance' => number_format($dataByDay[$Count]['Distance'], 2, '.', ','), 'view' => '<input type="button" value="Log" id="' . $value . '!' . $dataByDay[$Count]['Date'] . '" onclick="viewLog(this);">');


//                echo '<td>' . $sr . '</td>';
//                echo '<td>' . $dataByDay[$Count]['Date'] . '</td>';
//                echo '<td>' . $dataByDay[$Count]['total'] . '</td>';
//                echo '<td>' . number_format($dataByDay[$Count]['Distance'], 2, '.', ',') . '</td>';
//                echo '<td><input type="button" value="Log" id="' . $masId . '!' . $dataByDay[$Count]['Date'] . '" onclick="viewLog(this);"></td>';
//                echo '</tr>';
            }
        }

        return $data;
    }

    function distance($lat1, $lon1, $lat2, $lon2, $unit) {

        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }

    function get_sessiondetails($value = '') {

        $db1 = new ConDB();
        $logs = $db1->mongo->selectCollection('driver_log');
        $exp = explode('!', $_REQUEST['MasId']);
        $MasId = $exp[0];
        $Date = $exp[1];



        $startTime = strtotime($Date . ' 00:00:01');
        $endTime = strtotime($Date . ' 23:23:59');
        $getAllTodayLogs = $logs->find(array('mas_email' => $mas['email'],
            'on_time' => array('$gte' => $startTime),
            'off_time' => array('$lte' => $endTime)));

        $getLogs = $logs->find(array('mas_email' => $mas['email']))->sort(array("on_time" => -1));
        $count = 1;


        foreach ($getAllTodayLogs as $log) {
            $lat1 = 0;
            $long1 = 0;
            $lat2 = 0;
            $long2 = 0;
            $dictance = 0;
            echo '<tr>';
            echo '<td>' . $count . '</td>';
//                    echo '<td>' . $log['mas_email'] . '</td>';
            echo '<td>' . date('Y, M dS g:i a', $log['on_time']) . '</td>';
            if ($log['off_time'] == '') {
                echo '<td>Online Now</td>';
                echo '<td>-</td>';
            } else {
                echo '<td>' . date('Y, M dS g:i a', $log['off_time']) . '</td>';
                $diff = $log['off_time'] - $log['on_time'];
                $tm = explode('.', number_format(($diff / 60), 2, '.', ','));
                if ($tm[1] > 60) {
                    $tm[0] ++;
                    $tm[1] = $tm[1] - 60;
                }
                if (strlen($tm[1]) == 1) {
                    $tm[1] = $tm[1] . '0';
                }
                echo '<td>' . $tm[0] . ':' . $tm[1] . ' Mins' . '</td>';
            }

            //calculate distnce
            foreach ($log['location'] as $latlngs) {
                if ($lat1 == 0 && $long1 == 0) {
                    $lat1 = $latlngs['latitude'];
                    $long1 = $latlngs['longitude'];
                } else {
                    $lat2 = $latlngs['latitude'];
                    $long2 = $latlngs['longitude'];
//                            $gotDis = GetDrivingDistance($lat1, $lat2, $long1, $long2);
//                            $dictance+=(double) $gotDis['distance'];
                    $dictance+=(double) distance($lat1, $long1, $lat2, $long2, 'M');
                }
            }
            echo '<td>' . number_format($dictance, 2, '.', ',') . ' Miles</td>';
            echo '</tr>';
            $count++;
        }
    }

    function get_city_available() {
        return $this->db->query("select *from city_available ORDER BY City_Name ASC")->result();
    }

    function deletepassengers() {

        $pass_ids = $this->input->post('val');
        foreach ($pass_ids as $id) {
            $this->db->query("delete from slave where slave_id = '" . $id . "'");
            $this->db->query("delete from user_sessions where oid = '" . $id . "' and user_type = 2");
            $this->db->query("delete from passenger_rating where slave_id = '" . $id . "'");
        }
        return;
    }

    function addcountry() {

        $var = $this->input->post('data2');
        $string = strtoupper($var);

        $query = $this->db->query("select * from country where Country_Name= '" . $string . "'");
        if ($query->num_rows() > 0) {
            echo json_encode(array('msg' => "country already exists", 'flag' => 0));
            return;
        } else {


            $data2 = $this->input->post('data2');
            $string = strtoupper($data2);

            $this->db->query("insert into country(Country_Name)  values('" . $string . "')");

            $countryId = $this->db->insert_id();

            if ($countryId > 0) {
                echo json_encode(array('msg' => "country added successfully", 'flag' => 0, 'id' => $countryId));
                return;
            } else {
                echo json_encode(array('msg' => "Unable to add country", 'flag' => 1));
                return;
            }
        }
    }

    function deletecity() {

        $query = $this->input->post('val');



        foreach ($query as $rowid) {

            $this->db->query("select * from city_available where City_Id ='" . $rowid . "'");



            if ($this->db->affected_rows() > 0) {



                $this->db->query("delete from city_available where City_Id ='" . $rowid . "'");
                echo json_encode(array("msg" => "Your selected city has been deleted successfully", "flag" => 0));
                return;
            } else {
                echo json_encode(array("msg" => "your selected cities not deleted,retry!", "flag" => 1));
                return;
            }
        }
    }

    function get_vehivletype() {
        $query = $this->db->query("select * from workplace_types order by type_name")->result();
        return $query;
    }

    function get_company() {
        $query = $this->db->query("select * from company_info where Status = 3 order by companyname")->result();
        return $query;
    }

//    function insert_payment($mas_id = '') {
//        $currunEarnigs = $this->input->post('currunEarnigs');
//        $amoutpaid = $this->input->post('paid_amount');
//        $curuntdate = $this->input->post('ctime');
//        $closingamt = $currunEarnigs - $amoutpaid;
//
//        $query = "insert into payroll(mas_id,opening_balance,pay_date,pay_amount,closing_balance,due_amount) VALUES (
//        '" . $mas_id . "',
//        '" . $currunEarnigs . "',
//        '" . $curuntdate . "',
//        '" . $amoutpaid . "',
//        '" . $closingamt . "','" . $closingamt . "')";
//        $this->db->query($query);
////        echo $query;
////        exit();
//    }



    function insert_payment($mas_id = '') {
        $currunEarnigs = $this->input->post('currunEarnigs');
        $amoutpaid = $this->input->post('paid_amount');
        $curuntdate = $this->input->post('ctime');
        $closingamt = $currunEarnigs - $amoutpaid;
        $lastAppointmentId = $this->input->post('last_unsettled_appointment_id');


        $getWhere = $this->db->get_where("master", array('mas_id' => $mas_id))->result_array();



        if ($getWhere[0]['stripe_id'] == '') {

            return array("error" => "Please update the account details for the driver to transfer");
        }


        $stripe = new StripeModule();

        $transfer = $stripe->apiStripe('createTransfer', array('amount' => ((int) $amoutpaid * 100), 'currency' => 'USD', 'recipient' => $getWhere[0]['stripe_id'], 'statement_descriptor' => Appname . ' PAYROLL'));


        if ($transfer['error']) {

            return array("error" => $transfer['error']['message'], "stripeerror" => $transfer['error']['message']);
        }


        $query = "insert into payroll(mas_id,opening_balance,pay_date,pay_amount,closing_balance,due_amount,trasaction_id) VALUES (
        '" . $mas_id . "',
        '" . $currunEarnigs . "',
        '" . $curuntdate . "',
        '" . $amoutpaid . "',
        '" . $closingamt . "','" . $closingamt . "','" . $transfer['id'] . "')";
        $this->db->query($query);




        if ($this->db->insert_id() > 0) {


            $this->db->query("update appointment set settled_flag = 1 where appointment_id <= '" . $lastAppointmentId . "' and mas_id = '" . $mas_id . "' and settled_flag = 0 and status = 9 ");
            if ($this->db->affected_rows() > 0) {
                return array("msg" => "Success");
            } else {
                return array("error" => "Error1");
            }
        } else {
            return array("error" => "Error2");
        }


        return array("error" => "");
    }

    function addcity() {
        $countryid = $this->input->post('countryid');

        $data3 = $this->input->post('data3');
        $data = $this->input->post('data');
        $existcity = '';
        $getcityname = $this->db->query("select * from city where  City_Name = '" . $data3 . "' and Country_Id='" . $countryid . "'");

        if ($getcityname->num_rows() > 0) {

//            $this->db->query("insert into city(Country_Id,City_Name,Currency) values('$countryid','$data3','$data')");
            echo json_encode(array('msg' => "city already exists", 'flag' => 1));
            return;
        } else {
            $this->db->query("insert into city(Country_Id,City_Name,Currency) values('$countryid','$data3','$data')");
            if ($this->db->affected_rows() > 0) {
                echo json_encode(array('msg' => "city added successfully", 'flag' => 0));
                return;
            }
//            else {
//                echo json_encode(array('msg' => "city already exists", 'flag' => 1));
//                return;
//            }
        }

        // }
    }

    function activate_company() {
        $val = $this->input->post('val');
        foreach ($val as $result) {
            $this->db->query("update company_info set status=3  where company_id='" . $result . "'");
        }
        if ($this->db->affected_rows() > 0) {
            echo json_encode(array('msg' => "your selected company/companies activated succesfully", 'flag' => 1));
            return;
        }
    }

    function activate_vehicle() {
        $val = $this->input->post('val');
        foreach ($val as $result) {
            $this->db->query("update workplace set status=2  where workplace_id='" . $result . "'");
        }
        if ($this->db->affected_rows() > 0) {
            echo json_encode(array('msg' => "your selected vehicle/vehicles activated succesfully", 'flag' => 1));
            return;
        }
    }

    function reject_vehicle() {
        $val = $this->input->post('val');


        foreach ($val as $result) {
            $this->db->query("update workplace set Status = 4 where workplace_id ='" . $result . "'");

            if ($this->db->affected_rows() > 0) {
                $getTokensQry = $this->db->query("select * from user_sessions where oid IN (select mas_id from master where workplace_id = '" . $result . "') and loggedIn = 1 and user_type = 1 and LENGTH(push_token) > 63")->result();
                $this->load->library('mongo_db');
                foreach ($getTokensQry as $token) {

                    $query = "update appointment set status = '5',extra_notes = 'Admin rejected vehicle, so cancelled the booking',cancel_status = '8' where mas_id = '" . $token->oid . "' and status IN (6,7,8)";
                    $this->db->query($query);

                    $query_mas = "update master set workplace_id = '0' where mas_id = '" . $token->oid . "'";
                    $this->db->query($query_mas);

                    $this->mongo_db->update('location', array("status" => 4, 'carId' => 0, 'type' => 0), array('user' => (int) $token->oid));

                    $this->db->query("update user_sessions set loggedIn = 2 where oid = '" . $token->oid . "' and loggedIn = 1 and user_type = 1");
                }
            }
        }
        if ($this->db->affected_rows() > 0) {
            echo json_encode(array('msg' => "your selected Vehicle rejected Successfully", 'flag' => 1, 'res' => $res));
            return;
        }
    }

    function acceptdrivers() {
        $val = $this->input->post('val');
        $company_id = $this->input->post('company_id');
        $data = array();

        foreach ($val as $val1) {
            $data = $this->db->query('select * from  master where mas_id = "' . $val1 . '" and status = 1')->result();
        }

        foreach ($data as $t) {
            if ($t->vehicle_id != '') {
                $this->db->query("update workplace set status = 2  where uniq_identity='" . $t->vehicle_id . "' ");
            }
        }

        foreach ($val as $result) {
            $this->db->query("update master set status = 3 , company_id = '" . $company_id . "'  where mas_id='" . $result . "' ");
        }
        if ($this->db->affected_rows() > 0) {
            echo json_encode(array('msg' => "your selected driver/drivers accepted succesfully", 'flag' => 1, 'l' => $l));
            $email = '';
            $firstname = '';
            foreach ($data as $rec) {
                $email = $rec->email;
                $firstname = $rec->first_name;
            }
//          
            $this->sendMailToDriverAfterAccept($email, $firstname);
            return;
        }



        return $data;
    }

    //Manually logout the driver from admin panel
    function driver_logout() {
        $val = $this->input->post('val');
        $this->load->library('mongo_db');


        foreach ($val as $mas_ids) {

            $getTokensQry = $this->db->query("select us.*,(select workplace_id from master where mas_id = '" . $mas_ids . "') as workplace_id from user_sessions us where oid = '" . $mas_ids . "' and loggedIn = 1 and user_type = 1 and LENGTH(push_token) > 63")->result();
            $this->load->library('mongo_db');
            foreach ($getTokensQry as $token) {

                $query = "update appointment set status = '5',extra_notes = 'Admin rejected vehicle, so cancelled the booking',cancel_status = '8' where mas_id = '" . $token->oid . "' and status IN (6,7,8)";
                $this->db->query($query);

                $query_mas = "update master set workplace_id = '0' where mas_id = '" . $token->oid . "'";
                $this->db->query($query_mas);

                $this->mongo_db->update('location', array("status" => 4, 'carId' => 0, 'type' => 0), array('user' => (int) $token->oid));

                $this->db->query("update workplace set Status= 2   where workplace_id='" . $token->workplace_id . "'");
                $this->db->query("update user_sessions set loggedIn = 2 where oid = '" . $token->oid . "' and loggedIn = 1 and user_type = 1");
            }
        }

        if ($this->db->affected_rows() > 0) {
            echo json_encode(array('msg' => "your selected driver/drivers loggedout succesfully", 'flag' => 1));
            return;
        }

        return;
    }

    function getdrivervehicle() {
        $val = $this->input->post('masterid');
        foreach ($val as $val1) {
            $master = $val1;
        }

        $qu = "select * from  master where mas_id = '" . $master . "'";


        $data = $this->db->query("select vehicle_id from  master where mas_id = '" . $master . "' and status=1")->result();
        echo json_encode(array('data' => $qu, 'mas' => $val[0], 'vehicle' => $data['vehicle_id']));
        foreach ($data as $data1) {
            if ($data1->vehicle_id != '') {
                $data1 = $this->db->query('select * from  workplace where uniq_identity = "' . $data1->vehicle_id . '" and status = 5')->result();
                echo json_encode(array('data' => $data1, 'flag' => 0));
            } else {

                echo json_encode(array('data' => $data1, 'flag' => 1));
            }
        }
    }

    function rejectdrivers() {
        $val = $this->input->post('val');
        $this->load->library('mongo_db');


        foreach ($val as $mas_ids) {

            $getTokensQry = $this->db->query("select us.*,(select workplace_id from master where mas_id = '" . $mas_ids . "') as workplace_id from user_sessions us where oid = '" . $mas_ids . "' and loggedIn = 1 and user_type = 1 and LENGTH(push_token) > 63")->result();
            $this->load->library('mongo_db');


            if (!empty($getTokensQry)) {
                foreach ($getTokensQry as $token) {
                    $query = "update appointment set status = '5',extra_notes = 'Admin rejected vehicle, so cancelled the booking',cancel_status = '8' where mas_id = '" . $token->oid . "' and status IN (6,7,8)";
                    $this->db->query($query);

                    $query_mas = "update master set workplace_id = '0' where mas_id = '" . $token->oid . "'";
                    $this->db->query($query_mas);

                    $this->mongo_db->update('location', array("status" => 4, 'carId' => 0, 'type' => 0), array('user' => (int) $token->oid));

                    $this->db->query("update workplace set Status= 2   where workplace_id='" . $token->workplace_id . "'");
                    $this->db->query("update user_sessions set loggedIn = 2 where oid = '" . $token->oid . "' and loggedIn = 1 and user_type = 1");
                }
            } else {

                foreach ($val as $user) {
                    $userList[] = (int) $user;
                    $masterstring = $user . ",";
                }

                $this->db->query("update master set status = '4' where mas_id IN (" . rtrim($masterstring, ',') . ")");
                if ($this->db->affected_rows() > 0) {

                    $db = $this->mongo_db->db;

                    $selecttb = $db->location;

                    $selecttb->update(array('user' => array('$in' => $userList)), array('$set' => array('status' => 4, 'carId' => 0, 'type' => 0)));

                    $this->db->query("update user_sessions set loggedIn = 2 where oid = '" . rtrim($masterstring, ',') . "' and loggedIn = 1 and user_type = 1");
                }
            }
        }

        if ($this->db->affected_rows() > 0) {
            echo json_encode(array('msg' => "your selected driver/drivers rejected Successfully", 'flag' => 1, 'res' => $res));
            return;
        }
    }

    function get_ongoing_bookings() {

        $city = $this->session->userdata('city_id');
        $company = $this->session->userdata('company_id');
        $query_new = " appointment a,slave p,master m where a.mas_id = m.mas_id and a.slave_id=p.slave_id  and a.status IN (6,7,8) order by a.appointment_id desc LIMIT 200";
        if (($city != '0' && $company != '0'))
            $query_new = '  appointment a,slave p,master m,company_info ci,city_available ca where ca.City_Id = ci.city  and ci.company_id = m.company_id and  m.company_id = "' . $company . '" and a.mas_id = m.mas_id and a.slave_id=p.slave_id  and a.status IN (6,7,8) order by a.appointment_id desc LIMIT 200 ';
        else if ($city != '0')
            $query_new = '  appointment a,slave p,master m,company_info ci,city_available ca where ca.City_Id = "' . $city . '" AND ci.city = "' . $city . '" and ci.company_id = m.company_id and a.mas_id = m.mas_id and a.slave_id=p.slave_id  and a.status IN (6,7,8) order by a.appointment_id desc LIMIT 200 ';

        $query = $this->db->query("select m.first_name,m.last_name,a.mas_id,m.mobile as dphone,a.appointment_id,a.complete_dt,a.appointment_dt,a.B_type,a.address_line1,a.address_line2,a.apprxAmt,a.drop_addr1,a.drop_addr2,a.mas_id,a.slave_id,p.first_name as pessanger_fname,p.last_name as pessanger_lname,p.phone,a.status from " . $query_new)->result();
        return $query;
    }

    function sendAndroidPush($tokenArr, $andrContent, $apiKey) {
        $fields = array(
            'registration_ids' => $tokenArr,
            'data' => $andrContent,
        );

        $headers = array(
            'Authorization: key=' . $apiKey,
            'Content-Type: application/json'
        );
// Open connection
        $ch = curl_init();

// Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, 'http://android.googleapis.com/gcm/send');

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

// Execute post
        $result = curl_exec($ch);

        curl_close($ch);
//        echo 'Result from google:' . $result . '---';
        $res_dec = json_decode($result);

        if ($res_dec->success >= 1)
            return array('errorNo' => 44, 'result' => $result);
        else
            return array('errorNo' => 46, 'result' => $result);
    }

    function editdriverpassword() {
        $newpass = $this->input->post('newpass');
        $val = $this->input->post('val');

        $pass = $this->db->query("select password from master where mas_id='" . $val . "' ")->result();

        if ($pass['password'] == md5($newpass)) {
            echo json_encode(array('msg' => "this password already exists. Enter new password", 'flag' => 1));
            return;
        } else {
            $this->db->query("update master set password = md5('" . $newpass . "') where mas_id = '" . $val . "' ");

            if ($this->db->affected_rows() > 0) {
                echo json_encode(array('msg' => "your new password updated successfully", 'flag' => 0));
                return;
            }
        }
    }

    function editsuperpassword() {
        $newpass = $this->input->post('newpass');
//        $currentpassword = $this->input->post('currentpassword');
        $ids = $this->input->post('val');
        foreach ($ids as $id) {
            $pass = $this->db->query("select * from dispatcher where id = '" . $id . "'")->result()->dis_pass;

            if ($pass == $newpass) {
                echo json_encode(array('msg' => "this password already exists. Enter new password", 'flag' => 1));
                return;
            } else {
                $this->db->query("update dispatcher set password = '" . $newpass . "' where dis_id = '" . $id . "'");

                if ($this->db->affected_rows() > 0) {
                    echo json_encode(array('msg' => "your new password updated successfully", 'flag' => 0));
                    return;
                }
            }
//    
        }
    }

    function editvehicle($status) {

        $data['vehicle'] = $this->db->query("select w.*,wt.city_id,v.id,v.vehiclemodel from  workplace w ,workplace_types wt,vehiclemodel v where workplace_id='" . $status . "' and w.type_id = wt.type_id and v.id = w.Vehicle_Model ")->result();

        $cityId = $data['vehicle'][0]->city_id;

        if ($cityId == '')
            return array('flag' => 1);

        $data['company'] = $this->db->query("select companyname,company_id from company_info")->result();

        $data['cityList'] = $this->db->query("select City_Name,City_Id from city_available")->result();

        $data['workplaceTypes'] = $this->db->query("select * from workplace_types where city_id = '" . $cityId . "'")->result();

        $data['vehicleTypes'] = $this->db->query("select * from vehicleType")->result();




        $data['vehicleDoc'] = $this->db->query("select * from vechiledoc where vechileid = '" . $status . "'")->result();

        $this->load->library('mongo_db');


//        print_r($data['company']);
//        echo $cityId;
//        exit();


        return $data;
    }

    function deactivate_company() {
        $val = $this->input->post('val');
        foreach ($val as $result) {
            $this->db->query("update company_info set status=5  where company_id='" . $result . "'");
        }
        if ($this->db->affected_rows() > 0) {
            echo json_encode(array('msg' => "your selected company/companies deactivated succesfully", 'flag' => 1));
            return;
        }
    }

    function suspend_company() {
        $val = $this->input->post('val');
        foreach ($val as $result) {
            $this->db->query("update company_info set status=6  where company_id='" . $result . "'");
        }
        if ($this->db->affected_rows() > 0) {
            echo json_encode(array('msg' => "your selected company/companies suspended succesfully", 'flag' => 1));
            return;
        }
    }

    function get_vehicle_data() {
        $query = $this->db->query("select w.*,cty.City_Name from workplace_types w, city_available cty where w.city_id = cty.City_Id")->result();

        return $query;
    }

    function logoutdriver() {
        $driverid = $this->input->post('driverid');
        $this->load->library('mongo_db');
        $this->db->query("update user_sessions  set loggedIn = 2 where user_type = '1' and oid = '" . $driverid . "' and loggedIn = 1");

        $this->mongo_db->update('location', array('status' => 4), array('user' => (int) $driverid));
    }

    function insert_vehicletype() {
        $vehicletype = $this->input->post('vehicletypename');
        $seating = $this->input->post('seating');
        $minimumfare = $this->input->post('minimumfare');
        $basefare = $this->input->post('basefare');
        $priceperminute = $this->input->post('priceperminute');
        $priceperkm = $this->input->post('priceperkm');
        $discription = $this->input->post('descrption');
        $city = $this->input->post('country_select');
        $cancilationfee = $this->input->post('cancilationfee');
        $waiting_charge_per_min = $this->input->post('waiting_charge');



        //$type_on_img = $this->input->post('type_on_image');
        $type_on_img = $_FILES['type_on_image']['name'];
        $ext1 = substr($type_on_img, strrpos($type_on_img, '.') + 1); //explode(".", $insurname);
        $type_on_image = (rand(1000, 9999) * time()) . '.' . $ext1;


        //$type_off_img = $this->input->post('type_off_image');
        $type_off_img = $_FILES['type_off_image']['name'];
        $ext2 = substr($type_off_img, strrpos($type_off_img, '.') + 1); //explode(".", $insurname);
        $type_off_image = (rand(1000, 9999) * time()) . '.' . $ext2;


        //$type_map_img = $this->input->post('type_map_image');
        $type_map_img = $_FILES['type_map_image']['name'];
        $ext3 = substr($type_map_img, strrpos($type_map_img, '.') + 1); //explode(".", $insurname);
        $type_map_image = (rand(1000, 9999) * time()) . '.' . $ext3;



        $documentfolder = $_SERVER['DOCUMENT_ROOT'] . '/' . mainfolder . '/pics/';

        try {
            move_uploaded_file($_FILES['type_on_image']['tmp_name'], $documentfolder . $type_on_image);
            move_uploaded_file($_FILES['type_off_image']['tmp_name'], $documentfolder . $type_off_image);
            move_uploaded_file($_FILES['type_map_image']['tmp_name'], $documentfolder . $type_map_image);
        } catch (Exception $ex) {
            print_r($ex);
            exit();
            return false;
        }


        $resulrt = $this->db->query("insert into workplace_types(type_name,max_size,basefare,min_fare,price_per_min,
                     price_per_km,type_desc,city_id,vehicle_img,vehicle_img_off,MapIcon,waiting_charge_per_min,cancilation_fee,vehicle_order) values('" . $vehicletype . "',

                                                                    '" . $seating . "',

                                                                        '" . $basefare . "',
                                                                            '" . $minimumfare . "',
                                                                                '" . $priceperminute . "',
                                                                                    '" . $priceperkm . "',
                                                                                        '" . $discription . "',
                                                                                            '" . $city . "','" . $type_on_image . "','" . $type_off_image . "','" . $type_map_image . "','" . $waiting_charge_per_min . "','" . $cancilationfee . "',$v_order+1)");

        $type_id = $this->db->insert_id();




        $this->load->database();
        $cityData = $this->db->query("select * from city_available where city_id =  '" . $city . "'")->row_array();



        $this->load->library('mongo_db');

        
            
        $insertArr = array('type' => (int) $type_id, 'type_name' => $vehicletype, 'max_size' => (int) $seating, 'basefare' => (float) $basefare, 'min_fare' => (float) $minimumfare, 'price_per_min' => (float) $priceperminute, 'price_per_km' => (float) $priceperkm, "cancilation_fee" => (float) $cancilationfee, "order" => (int) $type_id, "waiting_charge_per_min" => (float) $waiting_charge_per_min, 'type_desc' => $discription, 'city_id' => (int) $city,
            "vehicle_img" => $type_on_image, "vehicle_img_off" => $type_off_image, "MapIcon" => $type_map_image, 
            "location" => array("longitude" => (double) $cityData['City_Long'], "latitude" => (double) $cityData['City_Lat']));
        $this->mongo_db->insert('vehicleTypes', $insertArr);

        return;
//        if ($this->db->affected_rows() > 0) {
//            echo json_encode(array('msg' => "your vehicle type added succesfully", 'flag' => 1));
//            return;
//        }
    }

    function edit_vehicletype($param) {
        //   $city_id = $this->input->post('');

        $result = $this->db->query("select * from workplace_types where type_id='" . $param . "'")->result();
        //     $result = $this->db->query("select City_Id from city  where City_Name ='" . $param . "'")->result();
        //    $result = $this->db->query("update workplace_types set  where type_id='" . $param . "'")->result();
        return $result;
    }

    function update_vehicletype($param) {
//        $vehicletype = $this->input->post('vehicletype');
//        $seating = $this->input->post('seating');
//        $minimumfare = $this->input->post('minimumfare');
//        $basefare = $this->input->post('basefare');
//        $priceperminute = $this->input->post('priceperminute');
//        $priceperkm = $this->input->post('priceperkm');
//        $discription = $this->input->post('discription');
//        $city = $this->input->post('city');
//        $cancilationfee = $this->input->post('cancilationfee');
//
//        $fdata = array('type_name' => $vehicletype,
//            'max_size' => (int) $seating,
//            'basefare' => (float) $basefare, 'min_fare' => (float) $minimumfare,
//            'price_per_min' => (float) $priceperminute,
//            'price_per_km' => (float) $priceperkm, 'type_desc' => $discription,
//            'city_id' => (int) $city,
//        );
//        //   $city_name = $this->db->query("select City_Name from city_available where City_Id = '" . $city . "'")->result();
//
//        $result = $this->db->query("update workplace_types set type_name='" . $vehicletype . "',
//                       max_size='" . $seating . "',
//                       basefare='" . $basefare . "',
//                       min_fare='" . $minimumfare . "',
//                       price_per_min='" . $priceperminute . "',
//                       price_per_km='" . $priceperkm . "',
//                       type_desc='" . $discription . "',
//                      city_id='" . $city . "',cancilation_fee = '" . $cancilationfee . "' where type_id='" . $param . "' ");
//
//        $this->load->library('mongo_db');
//
//        $this->mongo_db->update("vehicleTypes", $fdata, array("type" => (int) $param));
//
//  return ;
//        if ($this->db->affected_rows() > 0) {
//            echo json_encode(array('msg' => "your vehicle type updated succesfully", 'flag' => 1));
//            return;
//        } else {
//            echo json_encode(array('msg' => "your vehicle type not updated try again!", 'flag' => 0));
//            return;
//        }

        $vehicletype = $this->input->post('vehicletypename_s');
        $seating = $this->input->post('seating_s');
        $minimumfare = $this->input->post('minimumfare_s');
        $basefare = $this->input->post('basefare_s');
        $waiting_charge_per_min = $this->input->post('waiting_charge_edit');

        $priceperminute = $this->input->post('priceperminute_s');
        $priceperkm = $this->input->post('priceperkm_s');
        $discription = $this->input->post('discrption_s');
        $city = $this->input->post('city_select_s');
        $cancilationfee = $this->input->post('cancilationfee_s');

        //$type_on_img = $this->input->post('type_on_image');
        $type_on_img = $_FILES['type_on_image_edit']['name'];
        $ext1 = substr($type_on_img, strrpos($type_on_img, '.') + 1); //explode(".", $insurname);
        $type_on_image = (rand(1000, 9999) * time()) . '.' . $ext1;


        //$type_off_img = $this->input->post('type_off_image');
        $type_off_img = $_FILES['type_off_image_edit']['name'];
        $ext2 = substr($type_off_img, strrpos($type_off_img, '.') + 1); //explode(".", $insurname);
        $type_off_image = (rand(1000, 9999) * time()) . '.' . $ext2;


        //$type_map_img = $this->input->post('type_map_image');
        $type_map_img = $_FILES['type_map_image_edit']['name'];
        $ext3 = substr($type_map_img, strrpos($type_map_img, '.') + 1); //explode(".", $insurname);
        $type_map_image = (rand(1000, 9999) * time()) . '.' . $ext3;



        $documentfolder = $_SERVER['DOCUMENT_ROOT'] . '/' . mainfolder . '/pics/';

        try {
            move_uploaded_file($_FILES['type_on_image_edit']['tmp_name'], $documentfolder . $type_on_image);
            move_uploaded_file($_FILES['type_off_image_edit']['tmp_name'], $documentfolder . $type_off_image);
            move_uploaded_file($_FILES['type_map_image_edit']['tmp_name'], $documentfolder . $type_map_image);
        } catch (Exception $ex) {
            print_r($ex);
            return false;
        }




        $city_name = $this->db->query("select * from city_available where City_Id = '" . $city . "'")->row_array();

        if ($type_on_img != '' && $type_off_img != '' && $type_map_img != '') {

            $result = $this->db->query("update workplace_types set type_name='" . $vehicletype . "',
                       max_size='" . $seating . "',
                       basefare='" . $basefare . "',
                       min_fare='" . $minimumfare . "',
                       price_per_min='" . $priceperminute . "',
                       waiting_charge_per_min='" . $waiting_charge_per_min . "',
                       cancilation_fee='" . $cancilationfee . "',
                           
                       vehicle_img='" . $type_on_image . "',
                       vehicle_img_off='" . $type_off_image . "',
                       MapIcon='" . $type_map_image . "',
                           
                       price_per_km='" . $priceperkm . "',
                       type_desc='" . $discription . "',
                      city_id='" . $city . "',cancilation_fee = '" . $cancilationfee . "' where type_id='" . $param . "' ");


//            "type_on_image" : "7828506006530.png",
//    "type_off_image" : "13141911864008.png",
//    "type_map_image" : "5272319040574.png",
                    
            $fdata = array('type_name' => $vehicletype,
                'max_size' => (int) $seating,
                'basefare' => (float) $basefare, 'min_fare' => (float) $minimumfare,
                'price_per_min' => (float) $priceperminute,
                'vehicle_img_off' => $type_off_image,
                'vehicle_img' => $type_on_image,
                'MapIcon' => $type_map_image,
                'price_per_km' => (float) $priceperkm,
                'type_desc' => $discription,
                "cancilation_fee" => (float) $cancilationfee,
                "waiting_charge_per_min" => (float) $waiting_charge_per_min,
                'city_id' => (int) $city,
                "location" => array("longitude" => (double) $city_name['City_Long'], "latitude" => (double) $city_name['City_Lat']));
        } else if ($type_on_img == '' && ($type_off_img != '' && $type_map_img != '')) {

            $result = $this->db->query("update workplace_types set type_name='" . $vehicletype . "',max_size='" . $seating . "',
                       basefare='" . $basefare . "',
                       min_fare='" . $minimumfare . "',
                       price_per_min='" . $priceperminute . "',
                       
                        vehicle_img_off='" . $type_off_image . "',
                       MapIcon='" . $type_map_image . "',
                           
                         waiting_charge_per_min='" . $waiting_charge_per_min . "',
                       cancilation_fee='" . $cancilationfee . "',
                           
                       price_per_km='" . $priceperkm . "',
                           
                       type_desc='" . $discription . "',
                      city_id='" . $city . "',cancilation_fee = '" . $cancilationfee . "' where type_id='" . $param . "' ");

            $fdata = array('type_name' => $vehicletype,
                'max_size' => (int) $seating,
                'basefare' => (float) $basefare, 'min_fare' => (float) $minimumfare,
                'price_per_min' => (float) $priceperminute,
                'vehicle_img_off' => $type_off_image,
                'MapIcon' => $type_map_image,
                "cancilation_fee" => (float) $cancilationfee,
                "waiting_charge_per_min" => (float) $waiting_charge_per_min,
                'price_per_km' => (float) $priceperkm, 'type_desc' => $discription,
                'city_id' => (int) $city,
                "location" => array("longitude" => (double) $city_name['City_Long'], "latitude" => (double) $city_name['City_Lat']));
        } else if ($type_off_img == '' && ($type_on_img != '' && $type_map_img != '')) {

            $result = $this->db->query("update workplace_types set type_name='" . $vehicletype . "',max_size='" . $seating . "',
                       basefare='" . $basefare . "',
                       min_fare='" . $minimumfare . "',
                       price_per_min='" . $priceperminute . "',

                       vehicle_img='" . $type_on_image . "',
                       MapIcon='" . $type_map_image . "',
                       waiting_charge_per_min='" . $waiting_charge_per_min . "',
                       cancilation_fee='" . $cancilationfee . "',
                           
                       price_per_km='" . $priceperkm . "',
                       type_desc='" . $discription . "',
                      city_id='" . $city . "',cancilation_fee = '" . $cancilationfee . "' where type_id='" . $param . "' ");


            $fdata = array('type_name' => $vehicletype,
                'max_size' => (int) $seating,
                'basefare' => (float) $basefare, 'min_fare' => (float) $minimumfare,
                'price_per_min' => (float) $priceperminute,
                'vehicle_img' => $type_on_image,
                'MapIcon' => $type_map_image,
                "cancilation_fee" => (float) $cancilationfee,
                "waiting_charge_per_min" => (float) $waiting_charge_per_min,
                'price_per_km' => (float) $priceperkm, 'type_desc' => $discription,
                'city_id' => (int) $city,
                "location" => array("longitude" => (double) $city_name['City_Long'], "latitude" => (double) $city_name['City_Lat']));
        } else if ($type_map_img == '' && ($type_on_img != '' && $type_off_img != '')) {

            $result = $this->db->query("update workplace_types set type_name='" . $vehicletype . "',max_size='" . $seating . "',
                       basefare='" . $basefare . "',
                       min_fare='" . $minimumfare . "',
                       price_per_min='" . $priceperminute . "',
                       waiting_charge_per_min='" . $waiting_charge_per_min . "',
                       cancilation_fee='" . $cancilationfee . "',

                       
                           
                       vehicle_img='" . $type_on_image . "',
                       vehicle_img_off='" . $type_off_image . "',
                       price_per_km='" . $priceperkm . "',
                       type_desc='" . $discription . "',
                      city_id='" . $city . "',cancilation_fee = '" . $cancilationfee . "' where type_id='" . $param . "' ");


            $fdata = array('type_name' => $vehicletype,
                'max_size' => (int) $seating,
                'basefare' => (float) $basefare, 'min_fare' => (float) $minimumfare,
                'price_per_min' => (float) $priceperminute,
                'vehicle_img_off' => $type_off_image,
                'vehicle_img' => $type_on_image,
                "cancilation_fee" => (float) $cancilationfee,
                "waiting_charge_per_min" => (float) $waiting_charge_per_min,
                'price_per_km' => (float) $priceperkm, 'type_desc' => $discription,
                'city_id' => (int) $city,
                "location" => array("longitude" => (double) $city_name['City_Long'], "latitude" => (double) $city_name['City_Lat']));
        } else if (($type_on_img == '' && $type_off_img == '') && $type_map_img != '') {

            $result = $this->db->query("update workplace_types set type_name='" . $vehicletype . "',max_size='" . $seating . "',
                       basefare='" . $basefare . "',
                       min_fare='" . $minimumfare . "',
                       price_per_min='" . $priceperminute . "',
                           
                        waiting_charge_per_min='" . $waiting_charge_per_min . "',
                       cancilation_fee='" . $cancilationfee . "',
                      
                       MapIcon='" . $type_map_image . "',
                       price_per_km='" . $priceperkm . "',
                       type_desc='" . $discription . "',
                      city_id='" . $city . "',cancilation_fee = '" . $cancilationfee . "' where type_id='" . $param . "' ");


            $fdata = array('type_name' => $vehicletype,
                'max_size' => (int) $seating,
                'basefare' => (float) $basefare, 'min_fare' => (float) $minimumfare,
                'price_per_min' => (float) $priceperminute,
                'MapIcon' => $type_map_image,
                "cancilation_fee" => (float) $cancilationfee,
                "waiting_charge_per_min" => (float) $waiting_charge_per_min,
                'price_per_km' => (float) $priceperkm, 'type_desc' => $discription,
                'city_id' => (int) $city,
                "location" => array("longitude" => (double) $city_name['City_Long'], "latitude" => (double) $city_name['City_Lat']));
        } else if (($type_map_img == '' && $type_on_img == '') && $type_off_img != '') {

            $result = $this->db->query("update workplace_types set type_name='" . $vehicletype . "',max_size='" . $seating . "',
                       basefare='" . $basefare . "',
                       min_fare='" . $minimumfare . "',
                       price_per_min='" . $priceperminute . "',
                           
                           waiting_charge_per_min='" . $waiting_charge_per_min . "',
                       cancilation_fee='" . $cancilationfee . "',

                      
                       vehicle_img_off='" . $type_off_image . "',
                       price_per_km='" . $priceperkm . "',
                       type_desc='" . $discription . "',
                      city_id='" . $city . "',cancilation_fee = '" . $cancilationfee . "' where type_id='" . $param . "' ");


            $fdata = array('type_name' => $vehicletype,
                'max_size' => (int) $seating,
                'basefare' => (float) $basefare, 'min_fare' => (float) $minimumfare,
                'price_per_min' => (float) $priceperminute,
                'vehicle_img_off' => $type_off_image,
                "cancilation_fee" => (float) $cancilationfee,
                "waiting_charge_per_min" => (float) $waiting_charge_per_min,
                'price_per_km' => (float) $priceperkm, 'type_desc' => $discription,
                'city_id' => (int) $city,
                "location" => array("longitude" => (double) $city_name['City_Long'], "latitude" => (double) $city_name['City_Lat']));
        } else if (($type_map_img == '' && $type_off_img == '') && $type_on_img != '') {

            $result = $this->db->query("update workplace_types set type_name='" . $vehicletype . "',max_size='" . $seating . "',
                       basefare='" . $basefare . "',
                       min_fare='" . $minimumfare . "',
                       price_per_min='" . $priceperminute . "',
                           
                        waiting_charge_per_min='" . $waiting_charge_per_min . "',
                       cancilation_fee='" . $cancilationfee . "',
                      
                       vehicle_img='" . $type_on_image . "',
                       price_per_km='" . $priceperkm . "',
                       type_desc='" . $discription . "',
                      city_id='" . $city . "',cancilation_fee = '" . $cancilationfee . "' where type_id='" . $param . "' ");


            $fdata = array('type_name' => $vehicletype,
                'max_size' => (int) $seating,
                'basefare' => (float) $basefare, 'min_fare' => (float) $minimumfare,
                'price_per_min' => (float) $priceperminute,
                'vehicle_img' => $type_on_image,
                "cancilation_fee" => (float) $cancilationfee,
                "waiting_charge_per_min" => (float) $waiting_charge_per_min,
                'price_per_km' => (float) $priceperkm, 'type_desc' => $discription,
                'city_id' => (int) $city,
                "location" => array("longitude" => (double) $city_name['City_Long'], "latitude" => (double) $city_name['City_Lat']));
        } else if ($type_on_img == '' && $type_off_img == '' && $type_map_img == '') {

            $result = $this->db->query("update workplace_types set type_name='" . $vehicletype . "',
                       max_size='" . $seating . "',
                       basefare='" . $basefare . "',
                       min_fare='" . $minimumfare . "',
                       price_per_min='" . $priceperminute . "',
                           
                        waiting_charge_per_min='" . $waiting_charge_per_min . "',
                       cancilation_fee='" . $cancilationfee . "',
                           
                       price_per_km='" . $priceperkm . "',
                       type_desc='" . $discription . "',
                      city_id='" . $city . "',cancilation_fee = '" . $cancilationfee . "' where type_id='" . $param . "' ");

            $fdata = array('type_name' => $vehicletype,
                'max_size' => (int) $seating,
                'basefare' => (float) $basefare, 'min_fare' => (float) $minimumfare,
                'price_per_min' => (float) $priceperminute,
                "cancilation_fee" => (float) $cancilationfee,
                "waiting_charge_per_min" => (float) $waiting_charge_per_min,
                'price_per_km' => (float) $priceperkm, 'type_desc' => $discription,
                'city_id' => (int) $city,
                "location" => array("longitude" => (double) $city_name['City_Long'], "latitude" => (double) $city_name['City_Lat']));
        }


        $this->load->library('mongo_db');
        $this->mongo_db->update("vehicleTypes", $fdata, array("type" => (int) $param));

        return;
    }

    function get_vehiclemake() {
        return $this->db->query("select * from vehicleType")->result();
    }

    function get_vehiclemodal() {
        return $this->db->query("select vm.*,vt.vehicletype from vehiclemodel vm,vehicleType vt where vm.vehicletypeid= vt.id")->result();
    }

    function vehiclemodal() {
        return $this->db->query("select *  from vehiclemodel order by vehiclemodel")->result();
    }

    function insert_typename() {
        $typename = $this->input->post('typename');

        $result = $this->db->query("insert into vehicleType(vehicletype) values('" . $typename . "')");
        if ($this->db->affected_rows() > 0) {
            echo json_encode(array('msg' => "your  type name added succesfully", 'flag' => 1));
            return;
        }
    }

    function deletetype() {
        $vehicleid = $this->input->post('vehicletypeid');

        $result = $this->db->query("delete from workplace_types where type_id ='" . $vehicleid . "'");
    }

    function deletecompany() {
        $companyid = $this->input->post('companyid');

//        $result = $this->db->query("delete from company_info where company_id ='" . $companyid . "' ");

        $affectedRows = 0;

        $deleteVehicleTypes = $this->db->query("delete from company_info where company_id = " . $companyid)->row_array();
        $affectedRows += $this->db->affected_rows();

        if ($affectedRows <= 0) {

            echo json_encode(array('flag' => 1, 'affectedRows' => $affectedRows, 'msg' => 'Failed to delete'));
            return false;
        }

        $selectType = $this->db->query("select type_id from workplace where company_id = '" . $companyid . "'")->result();


        foreach ($selectType as $type) {
            $type_ids[] = (int) $type['type_id'];
        }

        $deleteAllVehicles = $this->db->query("delete from workplace_types where type_id  in (" . implode(',', $type_ids) . ")");
        $affectedRows += $this->db->affected_rows();

        $deleteAllVehicles = $this->db->query("delete from workplace where type_id  in (" . implode(',', $type_ids) . ")");
        $affectedRows += $this->db->affected_rows();


        $this->load->library('mongo_db');

        $return[] = $this->mongo_db->delete('vehicleTypes', array('type' => array('$in' => $type_ids)));

        $getAllDriversCursor = $this->mongo_db->get('vehicleTypes', array('type' => array('$in' => $type_ids)));

        $mas_id = array();

        foreach ($getAllDriversCursor as $driver) {
            $mas_id[] = (int) $driver['user'];
        }

        $return[] = $this->mongo_db->delete('location', array('user' => array('$in' => $mas_id)));

        $updateMysqlDriverQry = $this->db->query("delete from master where mas_id in (" . implode(',', $mas_id) . ")");
        $affectedRows += $this->db->affected_rows();

        $updateMysqlApptQry = $this->db->query("delete from appointment where mas_id in (" . implode(',', $mas_id) . ")");
        $affectedRows += $this->db->affected_rows();

        $updateMysqlReviewQry = $this->db->query("delete from passenger_rating where mas_id in (" . implode(',', $mas_id) . ")");
        $affectedRows += $this->db->affected_rows();

        $updateMysqlReviewQry = $this->db->query("delete from master_ratings where mas_id in (" . implode(',', $mas_id) . ")");
        $affectedRows += $this->db->affected_rows();

        $updateMysqlReviewQry = $this->db->query("delete from user_sessions where user_type = 1 and oid in (" . implode(',', $mas_id) . ")");
        $affectedRows += $this->db->affected_rows();

        echo json_encode(array('flag' => 0, 'affectedRows' => $deleteAllVehicles . $deleteVehicleTypes . $updateMysqlDriverQry));
    }

    function deletecountry() {
        $countryid = $this->input->post('countryid');

        $result = $this->db->query("delete from country where Country_Id ='" . $countryid . "'");
    }

    function deletepagecity() {
        $cityid = $this->input->post('cityid');
        $this->load->library('mongo_db');


//        $result = $this->db->query("delete from city_available where City_Id ='" . $cityid . "'");

        $result = $this->db->query("select * from company_info where city = '" . $cityid . "'")->result();
        $City_Name = $this->db->query("select * from city_available where City_Id = '" . $cityid . "'")->result();



        $companies = array();
        foreach ($result as $company) {

            $companies[] = $company->company_id;
        }

        $cities = '';
        foreach ($City_Name as $c) {
            $cities = $c->City_Name;
        }

        $result1 = $this->db->query("select type_id from workplace_types where city_id = '" . $cityid . "'")->result();

        $vehicleTypes = array();

        foreach ($result1 as $company) {
            $vehicleTypes[] = $company->type_id;
        }
        $this->db->query("delete from city_available where City_Id = '" . $cityid . "'");

        if (!empty($companies))
            $this->db->query("delete from company_info where company_id in (" . implode(',', $companies) . ")");

        if (!empty($cityid))
            $this->db->query("delete from dispatcher where city ='" . $cityid . "'");

        if (!empty($vehicleTypes))
            $this->db->query("delete from workplace where type_id in (" . implode(',', $vehicleTypes) . ")");

        if (!empty($vehicleTypes))
            $this->db->query("delete from workplace_types where type_id in (" . implode(',', $vehicleTypes) . ")");

        //$this->db->query("delete from coupons where city_id ='" . $cityid . "'");

        $this->mongo_db->delete('coupons', array('city_id' => $cityid));
        $this->mongo_db->delete('zones', array('city' => $City_Name));

        if (!empty($companies))
            $this->db->query("delete from master where company_id  in (" . implode(',', $companies) . ")");
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

            $updateCarQry = $this->db->query("update workplace set status = 2 where workplace_id = '" . $getMasterDet['workplace_id'] . "'");
            $affectedRows += $this->db->affected_rows();

            $updateMysqlDriverQry = $this->db->query("delete from master where mas_id = '" . $row . "'");
            $affectedRows += $this->db->affected_rows();

            $updateMysqlApptQry = $this->db->query("delete from appointment where mas_id = '" . $row . "'");
            $affectedRows += $this->db->affected_rows();

            $updateMysqlReviewQry = $this->db->query("delete from passenger_rating where mas_id = '" . $row . "'");
            $affectedRows += $this->db->affected_rows();

            $updateMysqlReviewQry = $this->db->query("delete from master_ratings where mas_id = '" . $row . "'");
            $affectedRows += $this->db->affected_rows();

            $updateMysqlReviewQry = $this->db->query("delete from user_sessions where user_type = 1 and oid = '" . $row . "'");
            $affectedRows += $this->db->affected_rows();
        }

        echo json_encode(array('flag' => 0, 'affectedRows' => $affectedRows, 'msg' => 'Process completed.'));
    }

    function deletemodal() {
        $modalid = $this->input->post('modalid');

        $result = $this->db->query("delete from vehiclemodel where id ='" . $modalid . "'");
    }

    function insert_modal() {
        $typeid = $this->input->post('typeid');

        $modal = $this->input->post('modal');

        $res = $this->db->query("insert into vehiclemodel(vehiclemodel,vehicletypeid) values('" . $modal . "','" . $typeid . "')");
        if ($this->db->affected_rows() > 0) {
            echo json_encode(array('msg' => "your  modal name added succesfully", 'flag' => 1));
            return;
        }
    }

    function deletevehicletype() {
        $val = $this->input->post('val');
        $this->load->library('mongo_db');
        foreach ($val as $row) {
//            $this->db->query("delete  from vehicleType where id = '" . $row . "' ");

            $affectedRows = 0;


            $deleteAllVehicles = $this->db->query("delete from workplace_types where type_id  = '" . $row . "'");

            $affectedRows += $this->db->affected_rows();

            if ($affectedRows <= 0) {

                echo json_encode(array('flag' => 1, 'affectedRows' => $affectedRows, 'msg' => 'Failed to delete'));
                return false;
            }

            $deleteAllVehicles = $this->db->query("delete from workplace where type_id = '" . $row . "'");
            $affectedRows += $this->db->affected_rows();




            $return[] = $this->mongo_db->delete('vehicleTypes', array('type' => (int) $row));

            $getAllDriversCursor = $this->mongo_db->get('location', array('type' => (int) $row));

            $mas_id = array();

            foreach ($getAllDriversCursor as $driver) {
                $mas_id[] = (int) $driver['user'];
            }

            $return[] = $this->mongo_db->delete('location', array('user' => array('$in' => $mas_id)));

            $updateMysqlDriverQry = $this->db->query("delete from master where mas_id in (" . implode(',', $mas_id) . ")");
            $affectedRows += $this->db->affected_rows();

            $updateMysqlApptQry = $this->db->query("delete from appointment where mas_id in (" . implode(',', $mas_id) . ")");
            $affectedRows += $this->db->affected_rows();

            $updateMysqlReviewQry = $this->db->query("delete from passenger_rating where mas_id in (" . implode(',', $mas_id) . ")");
            $affectedRows += $this->db->affected_rows();

            $updateMysqlReviewQry = $this->db->query("delete from master_ratings where mas_id in (" . implode(',', $mas_id) . ")");
            $affectedRows += $this->db->affected_rows();

            $updateMysqlReviewQry = $this->db->query("delete from user_sessions where user_type = 1 and oid in (" . implode(',', $mas_id) . ")");
            $affectedRows += $this->db->affected_rows();
        }

        echo json_encode(array('flag' => 0, 'affectedRows' => $affectedRows, 'msg' => 'Process completed.'));
    }

    function deletevehiclemodal() {
        $val = $this->input->post('val');
        foreach ($val as $row) {
            $this->db->query("delete  from vehiclemodel where id = '" . $row . "' ");
        }
    }

    function deletevehicletypemodel() {
        $val = $this->input->post('val');
        foreach ($val as $row) {
//            $this->db->query("delete  from vehiclemodel where id = '" . $row . "' ");
            $this->db->query("delete from vehicleType where id = '" . $row . "' ");
        }
//        if($this->db->num_rows() > 0){
//             echo json_encode(array('msg' => 'your vehicletype deleted', 'flag' => 0));
//            return;
//        } else {
//            echo json_encode(array('msg' => 'your not deleted', 'flag' => 1));
//            return;
//        }
//        
    }

    function editlonglat() {
        $val = $this->input->post('val');
        $lat = $this->input->post('lat');
        $lon = $this->input->post('lon');
//        foreach ($val as $rowid) {
        $this->db->query("update city_available set City_Lat = '" . $lat . "',City_Long = '" . $lon . "' where City_Id ='" . $val . "' ");
//        }
        if ($this->db->affected_rows() > 0) {
            echo json_encode(array('msg' => 'your latlong added successfully', 'flag' => 0));
            return;
        } else {
            echo json_encode(array('msg' => 'your latlong update failed', 'flag' => 1));
            return;
        }
    }

    function insert_city_available() {
        $lat = $this->input->post('lat');
        $lng = $this->input->post('lng');
        $country = $this->input->post('country');
        $city = $this->input->post('city');

        $query = $this->db->query("select * from city_available where City_Id ='" . $city . "' ");

        if ($query->num_rows() > 0) {
            echo json_encode(array('msg' => "city  already exists", 'flag' => 0));
            return;
        } else {

            $selectCity = "select City_Name from city where City_Id = '" . $city . "'";

            $Result = $this->db->query($selectCity)->result_array();

            $this->db->query("insert into city_available(City_Id,Country_Id,City_Name,City_Lat,City_Long) values('" . $city . "','" . $country . "','" . $Result[0]['City_Name'] . "','" . $lat . "','" . $lng . "')");

            if ($this->db->affected_rows() > 0) {
                echo json_encode(array('msg' => "city added successfully", 'flag' => 1));
                return;
            }
        }
    }

    function city() {
        return $this->db->query("select City_Name,City_Id from city_available ORDER BY City_Name ASC ")->result();
    }

    function city_sorted() {
        return $this->db->query("select City_Name,City_Id from city_available ORDER BY City_Name ASC ")->result();
    }

    function get_driver() {
        return $this->db->query("select * from master ORDER BY last_name")->result();
    }

    function insert_company() {
        $companyname = $this->input->post('companyname');
        $firstname = $this->input->post('firstname');
        $lastname = $this->input->post('lastname');
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $email = $this->input->post('email');
        $address = $this->input->post('address');
        $mobile = $this->input->post('mobilenumber');
        $city = $this->input->post('cityname');
        $state = $this->input->post('state');
        $postcode = $this->input->post('pincode');
        $vatnumber = $this->input->post('vatnumber');

        $status = 1;
//        $logo = "0";

        $companylogo = $_FILES["companylogo"]["name"];
        $extra = substr($companylogo, strrpos($companylogo, '.') + 1); //explode(".", $insurname);
        $logo = (rand(1000, 9999) * time()) . '.' . $extra;



        $documentfolder = $_SERVER['DOCUMENT_ROOT'] . '/' . mainfolder . '/pics/';



        try {
            move_uploaded_file($_FILES['companylogo']['tmp_name'], $documentfolder . $logo);
        } catch (Exception $ex) {
            print_r($ex);
            return false;
        }

        $getcityname = $this->db->query("select * from company_info where  email = '" . $email . "'");
        if ($getcityname->num_rows() > 0) {


            echo json_encode(array('err' => 0));
        } else {

            $result['data'] = $this->db->query("insert into company_info(companyname,addressline1,city,state,postcode,vat_number,firstname,
                           lastname,email,mobile,userame,password,status,logo) values(
                           '" . $companyname . "',
                           '" . $address . "',
                           '" . $city . "',
                           '" . $state . "',
                           '" . $postcode . "',
                           '" . $vatnumber . "',
                           '" . $firstname . "',
                           '" . $lastname . "',
                           '" . $email . "',
                           '" . $mobile . "',

                           '" . $username . "',
                           '" . $password . "', '" . $status . "','" . $logo . "')");
//            echo json_encode(array('err' => 1));
        }
    }

    function update_company($param) {

        $companyname = $this->input->post('companyname');
        $firstname = $this->input->post('firstname');
        $lastname = $this->input->post('lastname');
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $email = $this->input->post('email');
        $address = $this->input->post('address');
        $mobile = $this->input->post('mobilenumber');
        $city = $this->input->post('cityname');
        $state = $this->input->post('state');
        $postcode = $this->input->post('pincode');
        $vatnumber = $this->input->post('vatnumber');
//        $company_log = $this->input->post('e_companylogo');


        $companylogo = $_FILES["e_companylog"]["name"];
        $extra = substr($companylogo, strrpos($companylogo, '.') + 1); //explode(".", $insurname);
        $logo = (rand(1000, 9999) * time()) . '.' . $extra;


        $documentfolder = $_SERVER['DOCUMENT_ROOT'] . '/' . mainfolder . '/pics/';



        try {
            move_uploaded_file($_FILES['e_companylog']['tmp_name'], $documentfolder . $logo);
        } catch (Exception $ex) {
            print_r($ex);
            return false;
        }


        if ($companylogo == '') {
            $result['data'] = $this->db->query("update company_info set companyname='" . $companyname . "',
                                                                       addressline1='" . $address . "' ,
                                                                         city='" . $city . "',
                                                                                  state='" . $state . "',
                                                                                  vat_number='" . $vatnumber . "',
                                                                                  postcode='" . $postcode . "',
                                                                                  userame='" . $username . "',
                                                                                  firstname='" . $firstname . "',
                                                                                  lastname='" . $lastname . "',
                                                                                  email='" . $email . "',
                                                                                  mobile='" . $mobile . "',
                                                                                 password='" . $password . "' where company_id='" . $param . "'");
        } else {



            $result['data'] = $this->db->query("update company_info set companyname='" . $companyname . "',
                                                                       addressline1='" . $address . "' ,
                                                                         city='" . $city . "',
                                                                                  state='" . $state . "',
                                                                                  vat_number='" . $vatnumber . "',
                                                                                  postcode='" . $postcode . "',
                                                                                  userame='" . $username . "',
                                                                                  firstname='" . $firstname . "',
                                                                                  lastname='" . $lastname . "',
                                                                                  email='" . $email . "',
                                                                                  mobile='" . $mobile . "',
                                                                                  logo='" . $logo . "',
                                                                                 password='" . $password . "' where company_id='" . $param . "'");
        }



//        if ($this->db->affected_rows() > 0) {
//            echo json_encode(array('msg' => "your company  updated successfully", 'flag' => 1));
//            return;
//        }
    }

    function get_passengerinfo($status) {
        $varToShowData = $this->db->query("select * from slave where status='" . $status . "'order by slave_id DESC")->result();

        return $varToShowData;
    }

    function inactivepassengers() {
        $val = $this->input->post('val');

        foreach ($val as $result) {
            $this->db->query("update slave set status= 4 where slave_id='" . $result . "'");
        }
    }

//
//    function get_compaigns_data($status = '') {
//
//        return $this->db->query(" select cp.*,c.city_name,c.Currency as currency from coupons cp, city c where cp.city_id = c.city_id and cp.coupon_type = '" . $status . "' and cp.status = '0' and user_type = 2")->result();
//    }

    function get_compaigns_data($status = '') {

        $this->load->library('mongo_db');

        $db = $this->mongo_db->db;

        $selecttb = $db->selectCollection('coupons');

        if ($status == '1' || $status == '')
            $cond = array('coupon_type' => 1, 'coupon_code' => 'REFERRAL', 'user_type' => 2, 'status' => 0);
        else if ($status == '2')
            $cond = array('coupon_type' => 2, 'user_type' => 2, 'status' => 0);
        else if ($status == '3')
            $cond = array('coupon_type' => 3, 'user_type' => 1);

        $find = $selecttb->find($cond);

        $allDocs = array();

        foreach ($find as $doc) {
            $allDocs[] = $doc;
        }

//        print_r($allDocs);exit();

        return $allDocs;

//        return $this->db->query(" select cp.*,c.city_name,c.Currency as currency from coupons cp, city c where cp.city_id = c.city_id and cp.coupon_type = '" . $status . "' and cp.status = '0' and user_type = 2")->result();
    }

    function get_compaigns_data_ajax($for = '') {
//            $date =  date('Y-m-d');

        $this->load->library('mongo_db');
        $db = $this->mongo_db->db;
        $selecttb = $db->selectCollection('coupons');
        $st = $this->input->post('value');
        $allDocs = array();
        if ($for == '1') {
            if ($st == '0') {
                $cond = array('status' => (int) $st, 'coupon_type' => 1, 'coupon_code' => 'REFERRAL', 'user_type' => 2);
            } else if ($st == '1') {
                $cond = array('status' => (int) $st, 'coupon_type' => 1, 'coupon_code' => 'REFERRAL', 'user_type' => 2);
                //$res = $this->db->query(" select cp.*,c.city_name,c.Currency as currency from coupons cp, city c where cp.city_id = c.city_id and cp.coupon_type = '" . $for . "' and cp.status = '" . $st . "' and user_type = 2")->result();
            }
        } else if ($for == '2') {
            if ($st == '0')
                $cond = array('coupon_type' => 2, 'user_type' => 2, 'status' => 0);
            else if ($st == '10')
                $cond = array('coupon_type' => 2, 'user_type' => 2, 'status' => 1);
        } else
            $cond = array('coupon_type' => 3, 'user_type' => 1);

        $res = $selecttb->find($cond);
        foreach ($res as $doc) {
            $allDocs[] = $doc;
        }


        echo json_encode(array('data' => $allDocs));
    }

    function deactivecompaigns() {
        $this->load->library('mongo_db');


        $val = $this->input->post('val');
        $fdata = array('status' => 1,);
        foreach ($val as $row) {
            //$this->$db->update("update coupons set status = 1   where id='" . $row . "'");
            $this->mongo_db->update("coupons", $fdata, array("_id" => new MongoId($row)));
        }
//        if ($this->db->affected_rows() > 0) {
//            echo json_encode(array('msg' => "your selected discount deactivated successfully", 'flag' => 0));
//            return;
//        }
    }

    function activepassengers() {
        $val = $this->input->post('val');

        foreach ($val as $result) {
            $this->db->query("update slave set status=3 where slave_id='" . $result . "'");
        }
    }

    function insertdispatches() {
        $name = $this->input->post('name');
        $city = $this->input->post('city');
        $email = $this->input->post('email');
        $password = $this->input->post('password');
        $status = 1;
        $res = $this->db->query("insert into dispatcher(dis_name,dis_email,dis_pass,city) values('" . $name . "','" . $email . "','" . $password . "','" . $city . "')");

        if ($this->db->affected_rows() > 0) {
            echo json_encode(array('msg' => '0'));
            return;
        } else {
            echo json_encode(array('msg' => '1'));
            return;
        }
    }

    function inactivedispatchers() {
        $status = $this->input->post('val');
        foreach ($status as $row) {
            $result = $this->db->query("update dispatcher set status=2 where dis_id='" . $row . "'");
        }
    }

    function activedispatchers() {
        $status = $this->input->post('val');
        foreach ($status as $row) {
            $result = $this->db->query("update dispatcher set status=1 where dis_id='" . $row . "'");
        }
    }

    function deletedispatchers() {
        $status = $this->input->post('val');
        foreach ($status as $row) {
            $result = $this->db->query("delete from dispatcher  where dis_id='" . $row . "'");
        }
    }

    function editdispatchers() {
        $city = $this->input->post('cityval');
        $val = $this->input->post('val');

        $this->db->query("update dispatcher set city='" . $city . "' where dis_id='" . $val . "'");

        if ($this->db->affected_rows() > 0) {
            echo json_encode(array('msg' => 'your city edited successfully', 'flag' => 0));
            return;
        } else {
            echo json_encode(array('msg' => 'your city update failed', 'flag' => 1));
            return;
        }
    }

    function editpass() {
        $newpass = $this->input->post('newpass');
        $val = $this->input->post('val');

//        $this->db->query("select * from dispatcher where dis_pass='" . $newpass . "' ")->result();

        $this->db->query("update dispatcher set dis_pass='" . $newpass . "' where dis_id = '" . $val . "' ");
        if ($this->db->affected_rows() > 0) {
            echo json_encode(array('msg' => "this password already exists. Enter new password", 'flag' => 1));
            return;
        }
//         else {
//              $this->db->query("update dispatcher set dis_pass='" . $newpass . "' ");
//
//        }
    }

    function get_disputesdata($status) {
        $result = $this->db->query(" select mas.first_name as mas_fname,mas.last_name as mas_lname,mas.mas_id,slv.slave_id,slv.first_name as slv_name,slv.last_name as slv_lname,rep.report_msg,rep.report_id,rep.report_dt,rep.appointment_id from master mas,slave slv, reports rep where rep.mas_id = mas.mas_id   and rep.slave_id = slv.slave_id and rep.report_status = '" . $status . "' order by rep.report_id DESC")->result();

        return $result;
    }

    function resolvedisputes() {
        $value = $this->input->post('val');
        $mesage = $this->input->post('message');

        $this->db->query("update reports set report_status=2, report_msg='" . $mesage . "' where report_id='" . $value . "'");
        if ($this->db->affected_rows() > 0) {
            echo json_encode(array('msg' => "your selected dispute resolved succesfully", 'flag' => 1));
            return;
        }
    }

    function driver() {
        $res = $this->db->query("select * from master order by first_name")->result();
        return $res;
    }

    function passenger() {
        $res = $this->db->query("select * from slave")->result();
        return $res;
    }

    function insertcampaigns() {



        //$coupon_type == '1'
        $city = $this->input->post('city');
        $coupon_type = $this->input->post('coupon_type');
        $discount = $this->input->post('discount');
        $discounttype = $this->input->post('discountradio');
        $referaldiscount = $this->input->post('referaldiscount');
        $refferaldiscounttype = $this->input->post('refferalradio');
        $message = $this->input->post('message');
        $title = $this->input->post('title');

//$coupon_type == '2'
        $codes = $this->input->post('codes');
        $citys = $this->input->post('citys');
        $discounts = $this->input->post('discounts');
        $messages = $this->input->post('messages');
        $discounttypes = $this->input->post('discounttypes');


        $this->load->library('mongo_db');

        $db = $this->mongo_db->db;

        $selecttb = $db->selectCollection('coupons');

        if ($coupon_type == '1') {
            $cond = array('coupon_type' => 1, 'coupon_code' => 'REFERRAL', 'city_id' => (int) $city, 'status' => 0);
            $find = $selecttb->findOne($cond);

            if (is_array($find)) {
                return json_encode(array('msg' => "Referral campaign already exists in this city ", 'flag' => 1));
            }
        }

        if ($coupon_type == '2') {
            $city = $citys;
            $cond = array('coupon_type' => 2, 'coupon_code' => $codes, 'city_id' => (int) $city, 'status' => 0, 'expiry_date' => array('$gt' => time()));
            $find = $selecttb->findOne($cond);

            if (is_array($find)) {
                return json_encode(array('msg' => "Same coupon already exists in this city", 'flag' => 1));
            }
        }

        $cityDet = $this->db->query("select * from city_available where City_Id = '" . $city . "'")->result();
        $cityCurrency = $this->db->query("select * from city where City_Id = '" . $city . "'")->result();


        if ($coupon_type == '1') {

            $insert = array(
                "coupon_code" => "REFERRAL",
                "coupon_type" => 1,
                "discount_type" => (int) $discounttype,
                "discount" => (float) $discount,
                "referral_discount_type" => (int) $refferaldiscounttype,
                "referral_discount" => (float) $referaldiscount,
                "message" => $message,
                "status" => 0,
                "title" => $title,
                "city_id" => (int) $city,
                "currency" => $cityCurrency[0]->Currency, // $cityDet['Currency'],
                "city_name" => $cityDet[0]->City_Name,
                "location" => array(
                    "longitude" => (double) $cityDet[0]->City_Long,
                    "latitude" => (double) $cityDet[0]->City_Lat
                ),
                "user_type" => 2
            );

            $selecttb->insert($insert);
        } else if ($coupon_type == '2') {
            $insert = array(
                "coupon_code" => $codes,
                "coupon_type" => 2,
                "start_date" => strtotime($this->input->post('sdate')),
                "expiry_date" => strtotime($this->input->post('edate')),
                "discount_type" => (int) $discounttypes,
                "discount" => (float) $discounts,
                "message" => $messages,
                "status" => 0,
                "title" => $title,
                "city_id" => (int) $city,
                "currency" => $cityCurrency[0]->Currency,
                "city_name" => $cityDet[0]->City_Name,
                "location" => array(
                    "longitude" => (double) $cityDet[0]->City_Long,
                    "latitude" => (double) $cityDet[0]->City_Lat
                ),
                "user_type" => 2
            );
            $selecttb->insert($insert);
        }
//         else{
        return json_encode(array('msg' => "Great! Your referrals has been added sucessfully for this city", 'flag' => 0, 'data' => $insert));
//            }
    }

//    function insertcampaigns() {
//        $codes = $this->input->post('codes');
//        $city = $this->input->post('city');
//        $coupon_type = $this->input->post('coupon_type');
//        $discount = $this->input->post('discount');
//        $discounttype = $this->input->post('discountradio');
//        $referaldiscount = $this->input->post('referaldiscount');
//        $refferaldiscounttype = $this->input->post('refferalradio');
//        $message = $this->input->post('message');
//        $title = $this->input->post('title');
//
//
//        $citys = $this->input->post('citys');
//        $discounts = $this->input->post('discounts');
//        $messages = $this->input->post('messages');
//        $discounttypes = $this->input->post('discounttypes');
//
//        if ($coupon_type == '1') {
//            $res = $this->db->query("select * from coupons where coupon_type=1 and status=0 and city_id='" . $city . "' ");
//
//            if ($res->num_rows() > 0) {
//                return json_encode(array('msg' => "Referral already exists in this city ", 'flag' => 1));
//            }
//        }
//        if ($coupon_type == '2') {
//            $res = $this->db->query("select * from coupons where coupon_type=2 and status=0 and city_id='" . $city . "' and coupon_code = '" . $codes . "' and expiry_date < '" . date('Y-m-d', time()) . "'");
//
//            if ($res->num_rows() > 0) {
//                return json_encode(array('msg' => "Coupon already exists in this city ", 'flag' => 1));
//            }
//        }
//
//        if ($coupon_type == '1') {
//            $this->db->query("insert into coupons(coupon_code,coupon_type,discount_type,discount,referral_discount_type,referral_discount,message,city_id,user_type,title)
//        values('REFERRAL','1','" . $discounttype . "','" . $discount . "','" . $refferaldiscounttype . "','" . $referaldiscount . "','" . $message . "','" . $city . "','2','" . $title . "') ");
//        } else if ($coupon_type == '2') {
//            $this->db->query("insert into coupons(coupon_code,start_date,expiry_date,coupon_type,discount_type,discount,message,city_id,user_type,title)
//                    values('" . $codes . "','" . date("Y-m-d", strtotime($this->input->post('sdate'))) . "','" . date("Y-m-d", strtotime($this->input->post('edate'))) . "','2','" . $discounttypes . "','" . $discounts . "','" . $messages . "','" . $citys . "','2','" . $title . "') ");
//        }
////         else{
//        return json_encode(array('msg' => "Great! Your referrals has been added sucessfully for this city", 'flag' => 0));
////            }
//    }

    function updatecompaigns() {

        // for coupon types 1
        $coupon_type = $this->input->post('coupon_type');
        $discount = $this->input->post('discount');
        $discounttype = $this->input->post('discountradio');
        $referaldiscount = $this->input->post('referaldiscount');
        $refferaldiscounttype = $this->input->post('refferalradio');
        $message = $this->input->post('message');
        $title = $this->input->post('title');
        $cuponid = $this->input->post('val');

        // for coupon types 2
        $cuponids = $this->input->post('val2');
        $discounts = $this->input->post('discounts');
        $messages = $this->input->post('messages');
        $codes = $this->input->post('codes');
        $discounttypes = $this->input->post('discounttypes');

        $this->load->library('mongo_db');

        $db = $this->mongo_db->db;

        $selecttb = $db->selectCollection('coupons');

        if ($coupon_type == '1') {

            $selecttb->update(array('_id' => new MongoId($cuponid)), array(
                '$set' => array(
                    "discount_type" => (int) $discounttype,
                    "discount" => (float) $discount,
                    "referral_discount_type" => (int) $refferaldiscounttype,
                    "referral_discount" => (float) $referaldiscount,
                    "message" => $message,
                    "title" => $title,
                    "status" => 0
            )));
        } else if ($coupon_type == '2') {
            $selecttb->update(array('_id' => new MongoId($cuponids)), array(
                '$set' => array(
                    "coupon_code" => $codes,
                    "start_date" => (int) strtotime($this->input->post('sdate')),
                    "expiry_date" => (int) strtotime($this->input->post('edate')),
                    "discount_type" => (int) $discounttypes,
                    "discount" => (float) $discounts,
                    "message" => $messages,
                    "status" => 0,
                    "title" => $title,
                    "user_type" => 2
            )));
        }
    }

    function get_referral_details($id, $page) {
        $this->load->library('mongo_db');

        $db = $this->mongo_db->db;

        $selecttb = $db->selectCollection('coupons');

//        error_reporting(E_ALL);
        $find = $selecttb->find(array('_id' => new MongoId($id)));

        $all = array();

        foreach ($find as $cur)
            $all[] = $cur;

        return $all;
    }

    function SignupEmail($email, $firstname) {


        $toemail = $email;
        $toname = $firstname;

        $reply = "info@roadyo.in";
        $subject = 'Thank you for registering with ' . Appname;

        $body = '<div style="padding:45px 45px 15px">          
  <div style="font-size:20px;font-weight:normal;margin-bottom:30px">
    <strong>Hello ' . ucwords($firstname) . '</strong>
  </div>

  <div style="font-size:24px;font-weight:normal;margin-bottom:15px;color:#1fbad6">
    Thank you for registering with ' . Appname . '!<br><br>
    One of our representatives will get in touch with you in the next 24 hours to setup your profile and get all the necessary documents.
  </div>

  <table style="width:460px;margin:30px auto 30px;border-spacing:0px;line-height:0px">
    <tbody><tr>
      <td style="border-bottom-width:1px;border-bottom-color:#c0c0c8;border-bottom-style:solid">&nbsp;</td>
      <td style="border-bottom-width:1px;border-bottom-color:#c0c0c8;border-bottom-style:solid">&nbsp;</td>
    </tr>
    <tr>
      <td style="border-top-width:1px;border-top-color:#ffffff;border-top-style:solid">&nbsp;</td>
      <td style="border-top-width:1px;border-top-color:#ffffff;border-top-style:solid">&nbsp;</td>
    </tr>
  </tbody></table>
  <div>Regards,  </div>
  <div>Team ' . Appname . '.</div>
  </div>';
//
////                exit();
        try {

            $config = array();


            $config['api_key'] = "key-fdf665bbe4dc0ba130613c95a14ef7b2";

            $config['api_url'] = "https://api.mailgun.net/v3/roadyo.in/messages";

            $message = array();

            $message['from'] = $reply;

            $message['toname'] = rtrim($toname, ',');

            $message['to'] = rtrim($toemail, ',');

            $message['h:Reply-To'] = $reply;

            $message['subject'] = $subject;

            $message['html'] = $body; //file_get_contents("http://www.domain.com/email/html");

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $config['api_url']);

            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

            curl_setopt($ch, CURLOPT_USERPWD, "api:{$config['api_key']}");

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

            curl_setopt($ch, CURLOPT_POST, true);

            curl_setopt($ch, CURLOPT_POSTFIELDS, $message);

            $result = curl_exec($ch);

            curl_close($ch);

            return $result;
        } catch (Mandrill_Error $e) {
            return array('msg' => $e->getMessage(), 'status' => 'failed', 'flag' => 1);
        }


# Include the Autoloader (see "Libraries" for install instructions)
# Instantiate the client.
//$mgClient = new Mailgun('key-fdf665bbe4dc0ba130613c95a14ef7b2');
//$domain = "roadyo.in";
//
//# Make the call to the client.
//$result = $mgClient->sendMessage($domain,
//                  array(
//                      'from'    => '',
//                        'to'      => "prashantp@mobifyi.com",
//                        'subject' => 'Thank you for registering with '
////                        'body'    => $body
//                        
//                      ));
    }

    function sendMailToDriverAfterAccept($email, $firstname) {


        $toemail = $email;
        $toname = $firstname;

        $reply = "roadyo@gmail.com";

        $subject = 'Thank you for registering with ' . Appname;

        $body = '<div style="padding:45px 45px 15px">          
  <div style="font-size:20px;font-weight:normal;margin-bottom:30px">
    <strong>Hello ' . ucwords($firstname) . '</strong>
  </div>

  <div style="font-size:24px;font-weight:normal;margin-bottom:15px;color:#1fbad6">
    You are accepted by our team ' . Appname . '!<br><br>
    
  </div>

  <table style="width:460px;margin:30px auto 30px;border-spacing:0px;line-height:0px">
    <tbody><tr>
      <td style="border-bottom-width:1px;border-bottom-color:#c0c0c8;border-bottom-style:solid">&nbsp;</td>
      <td style="border-bottom-width:1px;border-bottom-color:#c0c0c8;border-bottom-style:solid">&nbsp;</td>
    </tr>
    <tr>
      <td style="border-top-width:1px;border-top-color:#ffffff;border-top-style:solid">&nbsp;</td>
      <td style="border-top-width:1px;border-top-color:#ffffff;border-top-style:solid">&nbsp;</td>
    </tr>
  </tbody></table>
  <div>Regards,  </div>
  <div>Team ' . Appname . '.</div>
  </div>';

//                exit();
        try {

            $config = array();


            $config['api_key'] = "key-fdf665bbe4dc0ba130613c95a14ef7b2";

            $config['api_url'] = "https://api.mailgun.net/v3/roadyo.in/messages";

            $message = array();

            $message['from'] = $reply;

            $message['toname'] = rtrim($toname, ',');

            $message['to'] = rtrim($toemail, ',');

            $message['h:Reply-To'] = $reply;

            $message['subject'] = $subject;

            $message['html'] = $body; //file_get_contents("http://www.domain.com/email/html");

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $config['api_url']);

            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

            curl_setopt($ch, CURLOPT_USERPWD, "api:{$config['api_key']}");

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

            curl_setopt($ch, CURLOPT_POST, true);

            curl_setopt($ch, CURLOPT_POSTFIELDS, $message);

            $result = curl_exec($ch);

            curl_close($ch);

            return $result;
        } catch (Mandrill_Error $e) {
            return array('msg' => $e->getMessage(), 'status' => 'failed', 'flag' => 1);
        }
    }

    public function sendMasWelcomeMail($toMail, $toName) {

        $subject = 'Thank you for registering with ' . Appname;

        $body = '<div style="padding:45px 45px 15px">          
  <div style="font-size:20px;font-weight:normal;margin-bottom:30px">
    <strong>Hello ' . ucwords($toName) . '</strong>
  </div>

  <div style="font-size:24px;font-weight:normal;margin-bottom:15px;color:#1fbad6">
    Thank you for registering with ' . Appname . '!<br><br>
    One of our representatives will get in touch with you in the next 24 hours to setup your profile and get all the necessary documents.
  </div>

  <table style="width:460px;margin:30px auto 30px;border-spacing:0px;line-height:0px">
    <tbody><tr>
      <td style="border-bottom-width:1px;border-bottom-color:#c0c0c8;border-bottom-style:solid">&nbsp;</td>
      <td style="border-bottom-width:1px;border-bottom-color:#c0c0c8;border-bottom-style:solid">&nbsp;</td>
    </tr>
    <tr>
      <td style="border-top-width:1px;border-top-color:#ffffff;border-top-style:solid">&nbsp;</td>
      <td style="border-top-width:1px;border-top-color:#ffffff;border-top-style:solid">&nbsp;</td>
    </tr>
  </tbody></table>
  <div>Regards,  </div>
  <div>Team ' . Appname . '.</div>
  </div>';

        $recipients = array($toMail => $toName);

        return $this->mailFun($recipients, $subject, $body);
    }

    function mailFun($recipients, $subject, $body, $reply = MANDRILL_FROM_EMAIL) {

        $toemail = $toname = "";
        foreach ($recipients as $email => $name) {

            if ($email != '') {
                $toemail .= $email . ",";
                $toname .= $name . ",";
            }
        }
        try {

            $config = array();


            $config['api_key'] = "key-eb2fbb7432506149c63b2edcdd4f9185";

            $config['api_url'] = "https://api.mailgun.net/v3/roadyo.in/messages";

            $message = array();

            $message['from'] = $reply;

            $message['toname'] = rtrim($toname, ',');

            $message['to'] = rtrim($toemail, ',');

            $message['h:Reply-To'] = $reply;

            $message['subject'] = $subject;

            $message['html'] = $body; //file_get_contents("http://www.domain.com/email/html");

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $config['api_url']);

            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

            curl_setopt($ch, CURLOPT_USERPWD, "api:{$config['api_key']}");

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

            curl_setopt($ch, CURLOPT_POST, true);

            curl_setopt($ch, CURLOPT_POSTFIELDS, $message);

            $result = curl_exec($ch);

            curl_close($ch);

            return $result;
        } catch (Mandrill_Error $e) {
            return array('msg' => $e->getMessage(), 'status' => 'failed', 'flag' => 1);
        }
    }

//    function editcompaigns() {
//        $value = $this->input->post('val');
//
//        $resu = $this->db->query("select * from coupons where id='" . $value . "'")->result();
//        echo json_encode($resu);
//    }

    function editcompaigns() {
        $value = $this->input->post('val');

        $this->load->library('mongo_db');

        $db = $this->mongo_db->db;

        $selecttb = $db->selectCollection('coupons');

        $resu = $selecttb->findOne(array('_id' => new MongoId($value)));

//        print_r($resu);exit();

        echo json_encode($resu);
    }

    function insertpass() {
        $password = $this->input->post('newpass');
        $val = $this->input->post('val');

        $res = $this->db->query("update slave set password = md5('" . $password . "')  where slave_id='" . $val . "'");
//        return $res;
        if ($this->db->affected_rows() > 0) {
            echo json_encode(array('msg' => "Password updated successfully", 'flag' => 1));
            return;
        }
    }

    function get_company_data($param) {
        $result = $this->db->query("select * from company_info where company_id='" . $param . "' ")->result();
        return $result;
    }

    function company_data() {
        $result = $this->db->query("select * from company_info")->result();
        return $result;
    }

    function get_dispatchers_data($status) {

        $res = $this->db->query("select * from dispatcher where status='" . $status . "'")->result();
        return $res;
    }

    function delete_dispatcher() {
        $var = $this->input->post('val');

        foreach ($var as $row) {
            $this->db->query("delete  from dispatcher where dis_id ='" . $row . "'");
        }
    }

    function get_country() {
        return $this->db->query("select * from country order by Country_Name")->result();
    }

    function datatable_cities() {

        $this->load->library('Datatables');
        $this->load->library('table');

        $this->datatables->select('ci.City_Id,co.Country_Name,ci.City_Name,ci.City_Lat,ci.City_Long')
//                ->add_column('select','<img src="$2">', 'ci.City_Id','co.Country_Name')
                ->unset_column('ci.City_Id')
                ->add_column('select', '<input type="checkbox" class="checkbox" name="checkbox" value="$1"/>', 'ci.City_Id')
                ->from('city_available ci,country co')
                ->where('ci.country_id = co.country_id'); //order by slave_id DESC ",false);
        $this->db->order_by("co.Country_Name", "asc");

        echo $this->datatables->generate();
    }

    function datatable_referrals() {
        $this->load->library('Datatables');
        $this->load->library('table');

        $this->datatables->select('c.referred_user_id,c.user_id,s.created_dt,s.slave_id,s.first_name')
                ->unset_column('')
                ->add_column('')
                ->from('')
                ->where('');
        $this->db->order_by("", "desc");
        echo $this->datatables->generate();
    }

    function datatable_promodetails($id) {
        $this->load->library('Datatables');
        $this->load->library('table');


        $this->load->library('mongo_db');

        $db = $this->mongo_db->db->selectCollection('coupons');
        $getBookingIds = $db->findOne(array('_id' => new MongoId($id)));

        $ids = '';
        foreach ($getBookingIds['bookings'] as $res) {
            $ids .= $res['booking_id'] . ',';
        }


        $MasId = rtrim($ids, ',');

//        echo $MasId;
//        exit();
        $query = "a.appointment_id in ('" . $MasId . "') and a.status = 9 and a.slave_id = s.slave_id";

        $this->datatables->select('(a.amount + a.discount),a.discount,a.amount AS Afterdiscount,a.appointment_dt,a.appointment_id,a.slave_id,s.email')
                ->from('appointment a,slave s', false)
                ->where($query);
        $this->db->order_by("a.appointment_id", "desc");

        echo $this->datatables->generate();
    }

    function get_appointmentDetials() {
        $bid = $this->input->post('app_id');
        $query = "select a.appointment_id,(
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
    'Driver arrived.'
     when 8   then
    'Appointment started.'
     when 9   then
    'Appointment completed.'
    when 10 then
    'Appointment timed out.'
    else
    'Status Unavailable.'
    END) as status_result,(case a.payment_type  when 1 then 'card' when 2 then 'cash' END) as paymentstatus,(select type_name from workplace_types where  type_id = m.type_id) as typename,(select basefare from workplace_types where  type_id = m.type_id) as basefare,(select price_per_min from workplace_types where  type_id = m.type_id) as price_per_min,a.appt_lat,a.appt_long,m.first_name,m.mas_id,m.mobile,s.phone,s.first_name as sname,a.address_line1,a.drop_addr1,a.appointment_dt from appointment a,master m,slave s where a.mas_id = m.mas_id and s.slave_id =  a.slave_id and a.appointment_id ='" . $bid . "' ";

        $res = $this->db->query($query)->result();

        foreach ($res as $result) {
            $pickupLat = $result->appt_lat;
            $pickupLong = $result->appt_long;
            $mas_id = $result->mas_id;
            $basefare = $result->basefare;
            $price_per_min = $result->price_per_min;
            $returnJson = $result;
        }


        $this->load->library('mongo_db');
        $returnJson = json_decode(json_encode($returnJson), true);
        $db = $this->mongo_db->db->selectCollection('location');
        $getBookingIds = $db->findOne(array('user' => (int) $returnJson['mas_id']));

        $distance = $this->distance($returnJson['appt_lat'], $returnJson['appt_long'], $getBookingIds['location']['latitude'], $getBookingIds['location']['longitude'], '');



        $approxamt = $basefare + ($distance * $price_per_min);

        $datetime1 = strtotime($returnJson['appointment_dt']);
        $datetime2 = time();
        $interval = abs($datetime2 - $datetime1);
        $duration_in_mts_old = $minutes = round($interval / 60);
        if ($minutes >= 60) {
            $returnJson['appointment_dt'] = round(($minutes / 60), 2) . ' hour';
        } else {
            $returnJson['appointment_dt'] = $minutes . ' minutes';
        }


        $returnJson['apprxAmt'] = currency . " " . round($approxamt);
        $returnJson['droplat'] = $getBookingIds['location']['latitude'];
        $returnJson['droplong'] = $getBookingIds['location']['longitude'];


        echo json_encode($returnJson); //json_encode($returnJson);
    }

    function CompleteBooking() {

        $this->load->library('table');

        $bid = $this->input->post('app_id');
        $data = $this->input->post('data');
        $amount = trim(str_replace('$',"",$this->input->post('amount')));

        $query = "select a.*,s.stripe_id,s.email from appointment a,slave s where a.appointment_id = '" . $bid . "' and a.slave_id = s.slave_id ";
        $res = $this->db->query($query)->row_array();
        $return =   array('flag' => 0,'msg' => 'Updated Successfully.');

        if ($res['payment_type'] == 2) {
           
              $this->db->query("update appointment set status = 9,amount ='" . $amount . "' where appointment_id = '" . $bid . "' ");
        } else {
            if ($res['stripe_id'] == '') {
                $return =  array("flag" => 1, "error" => "Card Is not define.");
            }


            $stripe = new StripeModule();

            $chargeCustomerArr = array('stripe_id' => $res['stripe_id'], 'amount' => (int) ((float) $amount * 100), 'currency' => currencySMB, 'description' => 'From ' . $res['email']);

            $transfer = $stripe->apiStripe('chargeCard', $chargeCustomerArr);

            if ($transfer['error']) {

                $return =   array('flag' => 1 ,"msg" => $transfer['error']['message'], "stripeerror" => $transfer['error']['message']);
            }

               $this->db->query("update appointment set status = 9,amount ='" . $amount . "' where appointment_id = '" . $bid . "' ");
        }
        echo json_encode($return);
    }

//    function distance_($lat1, $lon1, $lat2, $lon2, $unit) {
//
//        $theta = $lon1 - $lon2;
//        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
//        $dist = acos($dist);
//        $dist = rad2deg($dist);
//        $miles = $dist * 60 * 1.1515;
//        $unit = strtoupper($unit);
//
//        if ($unit == "K") {
//            return ($miles * 1.609344);
//        } else if ($unit == "N") {
//            return ($miles * 0.8684);
//        } else {
//            return $miles;
//        }
//    }

    function datatable_companys($status = '') {

        $city = $this->session->userdata('city_id');
        if ($city != '0')
            $citylist = 'status ="' . $status . '"  and co.city = "' . $city . '"';
        else
            $citylist = 'status ="' . $status . '"';

        $this->load->library('Datatables');
        $this->load->library('table');


        $this->datatables->select('co.company_id,co.companyname,co.addressline1,(select City_Name from city  where City_Id = co.city) as cities,co.state,co.postcode,co.firstname,co.lastname,co.email,co.mobile', false)
//                ->unset_column('co.status')
                ->add_column('select', '<input type="checkbox" class="checkbox" name="checkbox" value= "$1"/>', 'co.company_id')
                ->from('company_info co')
                ->where($citylist);
        $this->db->order_by("co.company_id", "desc");
        echo $this->datatables->generate();
//        echo json_encode(array('status' => $status));
    }

    public function vehicletype_reordering() {
//        
        $this->load->library('mongo_db');
        $res = $this->mongo_db->get_one('vehicleTypes', array('type' => (int) $_REQUEST['curr_id']));
        $res1 = $this->mongo_db->get_one('vehicleTypes', array('type' => (int) $_REQUEST['prev_id']));



        $currcount = $res['order'];
        $prevcount = $res1['order'];



        $res_mongo1 = $this->mongo_db->update('vehicleTypes', array('order' => $prevcount), array('type' => (int) $_REQUEST['curr_id']));
        $res_mongo2 = $this->mongo_db->update('vehicleTypes', array('order' => $currcount), array('type' => (int) $_REQUEST['prev_id']));

//            
        $res_mysql1 = $this->db->query("update workplace_types set vehicle_order = '" . $prevcount . "'  where type_id = '" . $_REQUEST['curr_id'] . "'");
        $res_mysql2 = $this->db->query("update workplace_types set vehicle_order = '" . $currcount . "'  where type_id = '" . $_REQUEST['prev_id'] . "'");
//            
//             echo $res_mysql1;
//            echo $res_mysql2;
//            $mongo_flag = 1;
//            if ($restuet['ok'] == 1 && $restuet_['ok'] == 1)
//                $mongo_flag = 0;
//            
        $mysql_flag = 0;
        if ($this->db->affected_rows > 0)
            $mysql_flag = 1;

//            echo json_encode(array('mongo_flag' => $res_mongo2,'mysql_flag' => $res_mysql2,"currcount"=>$currcount,"prevcount"=>$prevcount));
        echo json_encode(array('flag' => 1));
        return true;
    }

    function datatable_vehicletype($status = '') {

        $this->load->library('Datatables');
        $this->load->library('table');

        $city = $this->session->userdata('city_id');

        $cityCond = "";

        if ($city != '0')
            $cityCond = ' and w.city_id = "' . $city . '"';

        $this->datatables->select('w.vehicle_order,w.type_id,w.type_name,w.max_size,w.basefare,w.min_fare,w.waiting_charge_per_min,w.cancilation_fee,w.price_per_min,w.price_per_km,w.type_desc,cty.City_Name,w.vehicle_img as on_image,w.vehicle_img_off as image_off,w.MapIcon as map_icon')
                ->unset_column('w.vehicle_order')
                ->unset_column('on_image')
                ->unset_column('image_off')
                ->unset_column('map_icon')
                ->add_column('ON IMAGE', '<img src="' . base_url() . '../../pics/$1" width="50px" height="50px" class="imageborder">', 'on_image')
                ->add_column('OFF IMAGE', '<img src="' . base_url() . '../../pics/$1" width="50px" height="50px" class="imageborder">', 'image_off')
                ->add_column('MAP IMAGE', '<img src="' . base_url() . '../../pics/$1" width="50px" height="50px" class="imageborder">', 'map_icon')
                ->add_column('Ordering', '<img src="' . base_url() . '/theme/assets/img/uparrow.png" id="$1" data="1" class="ordering"><img src="' . base_url() . '/theme/assets/img/downarrow.png" id="$1" data="2" class="ordering">', 'w.type_id')
                ->add_column('select', '<input type="checkbox" class="checkbox" name="checkbox" value= "$1"/>', 'w.type_id')
                ->from('workplace_types w, city_available cty')
                ->where('w.city_id = cty.City_Id' . $cityCond); //order by slave_id DESC ",false);
//        $this->db->order_by("w.vehicle_order", "desc");
        echo $this->datatables->generate();
    }

//     function datatable_payroll($status = '') {
//
//        $this->load->library('Datatables');
//        $this->load->library('table');
//
//        $this->datatables->select(' ')->from('')->where(''); //order by slave_id DESC ",false);
//
//        echo $this->datatables->generate();
//    }


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

    function datatable_vehicles($status = '') {

        $this->load->library('Datatables');
        $this->load->library('table');

        $city = $this->session->userdata('city_id');
        $company = $this->session->userdata('company_id');
        if (($city != '0') && ($company == '0'))
            $citylist = ' and wt.city_id = "' . $city . '"';
        else if (($city != '0') && ($company != '0'))
            $citylist = ' and wt.city_id = "' . $city . '" and w.company = "' . $company . '"';

//        $compCond = "";


        if ($status == '12') {
            $status = '1,2';

            $this->datatables->select('w.workplace_id,w.uniq_identity,'
                            . '(select vehicletype from vehicleType where id = w.Title),'
                            . '(select vehiclemodel from vehiclemodel where id = w.Vehicle_Model),'
                            . '(select type_name from workplace_types  where type_id = w.type_id),'
                            . '(select mas_id from master  where vehicle_id = w.uniq_identity),'
                            . '(select first_name from master  where vehicle_id = w.uniq_identity),'
                            . '(select email from master  where vehicle_id = w.uniq_identity),'
                            . '(select companyname from company_info  where company_id = w.company),'
                            . 'w.Vehicle_Reg_No,w.License_Plate_No,w.Vehicle_Insurance_No,w.Vehicle_Color,'
                            . '(select City_Name from city where City_Id = wt.city_id)')
                    ->unset_column('w.workplace_id')
                    ->add_column('select', '<input type="checkbox" class="checkbox" name="checkbox" value= "$1"/>', 'w.workplace_id')
                    ->from('workplace w,vehicleType vt,vehiclemodel vm,workplace_types wt,company_info ci ')
                    ->where('vt.id = w.title and w.company = ci.company_id and vm.id=w.Vehicle_Model and wt.type_id = w.type_id  and w.Status in (' . $status . ')' . $citylist); //order by slave_id DESC ",false);
            $this->db->order_by("w.workplace_id", "desc");
        } else if ($status == '2') {
            $this->datatables->select('w.workplace_id,w.uniq_identity,'
                            . '(select vehicletype from vehicleType where id = w.Title),'
                            . '(select vehiclemodel from vehiclemodel where id = w.Vehicle_Model),'
                            . '(select type_name from workplace_types  where type_id = w.type_id),'
                            . '(select mas_id from master  where vehicle_id = w.uniq_identity),'
                            . '(select first_name from master  where vehicle_id = w.uniq_identity),'
                            . '(select email from master  where vehicle_id = w.uniq_identity),'
                            . '(select companyname from company_info  where company_id = w.company),'
                            . 'w.Vehicle_Reg_No,w.License_Plate_No,w.Vehicle_Insurance_No,w.Vehicle_Color,'
                            . '(select City_Name from city where City_Id = wt.city_id)')
                    ->unset_column('w.workplace_id')
                    ->add_column('select', '<input type="checkbox" class="checkbox" name="checkbox" value= "$1"/>', 'w.workplace_id')
                    ->from('workplace w,vehicleType vt,vehiclemodel vm,workplace_types wt,company_info ci ')
                    ->where('vt.id = w.title and w.company = ci.company_id and vm.id=w.Vehicle_Model and wt.type_id = w.type_id  and w.Status in (' . $status . ')' . $citylist); //order by slave_id DESC ",false);
            $this->db->order_by("w.workplace_id", "desc");
        } else {
            $this->datatables->select('w.workplace_id,w.uniq_identity,'
                            . '(select vehicletype from vehicleType where id = w.Title),'
                            . '(select vehiclemodel from vehiclemodel where id = w.Vehicle_Model),'
                            . '(select type_name from workplace_types  where type_id = w.type_id),'
                            . '(select m.mas_id from master m where m.workplace_id = w.workplace_id ),'
                            . '(select m.first_name from master m where m.workplace_id = w.workplace_id ),'
                            . '(select m.email from master m where m.workplace_id = w.workplace_id  and  w.Status = 1),'
                            . '(select companyname from company_info  where company_id = w.company),'
                            . 'w.Vehicle_Reg_No,w.License_Plate_No,w.Vehicle_Insurance_No,w.Vehicle_Color,'
                            . '(select City_Name from city where City_Id = wt.city_id)')
                    ->unset_column('w.workplace_id')
                    ->add_column('select', '<input type="checkbox" class="checkbox" name="checkbox" value= "$1"/>', 'w.workplace_id')
                    ->from('workplace w,vehicleType vt,vehiclemodel vm,workplace_types wt,company_info ci ')
                    ->where('vt.id = w.title and w.company = ci.company_id and vm.id=w.Vehicle_Model and wt.type_id = w.type_id  and w.Status in (' . $status . ')' . $citylist); //order by slave_id DESC ",false);
            $this->db->order_by("w.workplace_id", "desc");
        }


        echo $this->datatables->generate();
    }

    function loadAvailableCity() {
        $countryid = $this->input->post('country');
        $Result = $this->db->query("select c.* from city c where c.Country_Id = '" . $countryid . "' and c.City_Id not in (select City_Id from city_available where Country_Id = '" . $countryid . "')")->result();
        return $Result;
    }

//    function datatable_disputes($status = '') {
//
//        $this->load->library('Datatables');
//        $this->load->library('table');
//
//        $company_id = $this->session->userdata('company_id');
//        $compCond = "";
//        if ($company_id != 0)
//            $compCond = " and mas.company_id = '" . $company_id . "'";
//
//        $this->datatables->select("rep.report_id,slv.slave_id,slv.first_name,mas.mas_id,mas.first_name as name,rep.report_msg,rep.report_dt,rep.appointment_id")
//            ->add_column('select', '<input type="checkbox" class="checkbox" name="checkbox" value="$1"/>', 'rep.report_id')
//            ->from("master mas,slave slv, reports rep")
//            ->where("rep.mas_id = mas.mas_id   and rep.slave_id = slv.slave_id and rep.report_status = '" . $status . "'" . $compCond);
//
//        echo $this->datatables->generate();
//    }

    function datatable_disputes($status = '') {

        $this->load->library('Datatables');
        $this->load->library('table');

        $company_id = $this->session->userdata('company_id');
        $compCond = "";
        if ($company_id != 0)
            $compCond = " and mas.company_id = '" . $company_id . "'";

        $this->datatables->select("rep.report_id,slv.slave_id,slv.first_name,mas.mas_id,mas.first_name as name,rep.report_msg,rep.report_dt,rep.appointment_id")
                ->add_column('select', '<input type="checkbox" class="checkbox" name="checkbox" value="$1"/>', 'rep.report_id')
                ->from("master mas,slave slv, reports rep")
                ->where("rep.mas_id = mas.mas_id   and rep.slave_id = slv.slave_id and rep.report_status = '" . $status . "'" . $compCond);
        $this->db->order_by("rep.report_dt", "desc");
        echo $this->datatables->generate();
    }

    function refered($code, $refCode, $page) {


        $this->load->library('mongo_db');

        $db = $this->mongo_db->db;

        $selecttb = $db->selectCollection('coupons');

        $find = $selecttb->find(array('_id' => new MongoId($code), 'signups.coupon_code' => $refCode), array('signups.$' => 1));

        $all = array();

        foreach ($find as $cur) {
            $all[] = $cur;
        }

//        print_r($all);

        return $all;
    }

    function validateCompanyEmail() {

        $query = $this->db->query("select company_id from company_info where email='" . $this->input->post('email') . "'");
        if ($query->num_rows() > 0) {

            echo json_encode(array('msg' => '1'));
            return;
        } else {
            echo json_encode(array('msg' => '0'));
        }
    }

    function datatable_vehiclemodels($status) {


        $this->load->library('Datatables');
        $this->load->library('table');

        if ($status == 1) {

            $this->datatables->select("id,vehicletype")
                    ->add_column('select', '<input type="checkbox" class="checkbox" name="checkbox" value="$1"/>', 'id')
                    ->from("vehicleType");
//             $this->db->order_by("id", "desc");//order by slave_id DESC ",false);
        } else if ($status == 2) {


            $this->datatables->select("vm.id,vm.*,vt.vehicletype")
                    ->add_column('select', '<input type="checkbox" class="checkbox" name="checkbox" value="$1"/>', 'vm.id')
                    ->from("vehiclemodel vm,vehicleType vt")
                    ->where("vm.vehicletypeid = vt.id");
//                   $this->db->order_by("vm.id", "desc");     //order by slave_id DESC ",false);
        }
        $this->db->order_by("id", "desc");
        echo $this->datatables->generate();
    }

    function datatable_drivers($for = '', $status = '') {

        $this->load->library('Datatables');
        $this->load->library('table');
        $company = $this->session->userdata('company_id');
        $city = $this->session->userdata('city_id');



        $comp_ids = array();


        if ($for == 'my') {

//            if($status == '3')
//            {
//                    $query = $this->db->query('select * from company_info where Status = 3 and city = "' . $city . '"')->result();
//                    foreach ($query as $row)
//                         $comp_ids[] = $row->company_id;
//                        
//                    $comp_ids = implode(',',$comp_ids);
//                   
//                 if ($company != '0')
//                 {
//                        $whererc = "mas.status IN ('" . $status . "') and mas.company_id = '" . $company . "' ";
//                 }
//                  else
//                  {
//                      $whererc = "mas.status IN ('" . $status . "') and mas.company_id IN (" . $comp_ids . ")";
//                        
//                  }
//            }
            if ($company != '0') {
                $whererc = "mas.status IN ('" . $status . "') and mas.company_id = '" . $company . "' ";
            } else {

                $whererc = "mas.status IN ('" . $status . "')";
            }
            if ($status == 1) {

                $this->datatables->select("mas.mas_id as rahul,mas.first_name ,mas.last_name ,mas.mobile, mas.email,"
                                . "(select uniq_identity from workplace where mas.workplace_id = workplace_id),"
                                . "mas.created_dt,mas.profile_pic as pp,"
                                . "round((select avg(star_rating) from master_ratings where mas_id = mas.mas_id),1),"
                                . "(select companyname from company_info where company_id = mas.company_id) as companyname1,"
                                . "(select (case type when 2 then 'android_icon.png' when 1 then 'iphone-logo.png' END) from user_sessions where oid = mas.mas_id and user_type = 1 order by oid DESC limit 0,1) as type_img", false)
//                                . "(select Vehicle_Image from workplace where workplace_id = mas.workplace_id ) as vehicleimage", false)
//                        ->unset_column('mas.company_id')
                        //->unset_column('vehicleid')
                        ->unset_column('type_img')
                        ->unset_column('pp')
                        //->add_column('VEHICLE ID', '<input class="vehicleid" name="vehicleid" value="$1"/>', 'vehicleid')
                        ->add_column('PROFILE PIC', '<img src="' . base_url() . '../../pics/$1" width="50px" height="50px" class="imageborder">', 'pp')
                        ->add_column('DEVICE TYPE', '<img src="' . base_url() . '../../admin/assets/$1" width="30px">', 'type_img')
//                        ->add_column('VEHICLE IMAGE', '<img src="' . base_url() . '../../pics/$1" width="50px" class="imageborder">', 'vehicleimage')
                        ->add_column('LATITUDE', "get_lat/$1", 'rahul')
//                        ->add_column('LONGITUDE', "get_lon/$1", 'rahul')
                        ->add_column('SELECT', '<input type="checkbox" class="checkbox" name="checkbox" value="$1"/>', 'rahul')
                        ->from("master mas")
                        ->where("mas.status IN ('" . $status . "')");
            } else if ($status == 3 || $status == 4) {
                $this->datatables->select("mas.mas_id as rahul,mas.first_name ,mas.last_name ,mas.mobile, mas.email,"
                                . "(select uniq_identity from workplace where mas.workplace_id = workplace_id),"
                                . "mas.created_dt,mas.profile_pic as pp,"
                                . "round((select avg(star_rating) from master_ratings where mas_id = mas.mas_id),1),"
                                . "(select companyname from company_info where company_id = mas.company_id ) as companyname1,"
                                . "(select (case type when 2 then 'android_icon.png' when 1 then 'iphone-logo.png' END) from user_sessions where oid = mas.mas_id and user_type = 1  order by oid DESC limit 0,1) as type_img", false)
                        //      . "(select Vehicle_Image from workplace where workplace_id = mas.workplace_id ) as vehicleimage", false)
//                           ->unset_column('vehicleimage')
                        ->unset_column('type_img')
                        ->unset_column('pp')
                        ->add_column('PROFILE PIC', '<img src="' . base_url() . '../../pics/$1" width="50px" height="50px" class="imageborder">', 'pp')
                        ->add_column('DEVICE TYPE', '<img src="' . base_url() . '../../admin/assets/$1" width="30px">', 'type_img')
                        //                      ->add_column('VEHICLE IMAGE', '<img src="' . base_url() . '../../pics/$1" width="50px" class="imageborder">', 'vehicleimage')
                        ->add_column('LATITUDE', "get_lat/$1", 'rahul')
//                        ->add_column('LONGITUDE', "get_lon/$1", 'rahul')
                        ->add_column('SELECT', '<input type="checkbox" class="checkbox" name="checkbox" value="$1"/>', 'rahul')
                        ->from("master mas")
                        ->where($whererc);
            }
        } else if ($for == 'mo') {

//            $m = new MongoClient();
            $this->load->library('mongo_db');

            $db = $this->mongo_db->db;

            $selecttb = $db->selectCollection('location');

            $darray = $latlong = array();
            if ($status == 3) { //online or free
                $drivers = $selecttb->find(array('status' => (int) $status));

                foreach ($drivers as $mas_id) {
                    $darray[] = $mas_id['user'];
                    $latlong[$mas_id['user']] = array($mas_id['location']['latitude'], $mas_id['location']['longitude']);
                }
            } elseif ($status == 567) {//booked
                $drivers = $selecttb->find(array('status' => array('$in' => array(5, 6, 7))));
                foreach ($drivers as $mas_id) {
                    $darray[] = $mas_id['user'];

                    $latlong[$mas_id['user']] = array($mas_id['location']['latitude'], $mas_id['location']['longitude']);
                }
            } elseif ($status == 30) {//OFFLINE
                $drivers = $selecttb->find(array('status' => 4));
                foreach ($drivers as $mas_id) {
                    $darray[] = $mas_id['user'];
                    $latlong[$mas_id['user']] = array($mas_id['location']['latitude'], $mas_id['location']['longitude']);
                }
            }

            $mas_ids = implode(',', array_filter(array_unique($darray)));
            if ($mas_ids == '')
                $mas_ids = 0;
            $companywhere = '';
            if ($company != '0') {
                $companywhere = "and mas.company_id=" . $company;
            }
//            $DriverOnline = array();
//            $mas_ids = implode(',', array_filter(array_unique($darray)));
//            if ($mas_ids == '')
//            {
//                $mas_ids = 0;
//            }
//            else
//            {
//                
////                print_r('select * from user_sessions where user_type = 1 and loggedIn = 1 and oid IN ("' . $mas_ids . '")');
//             $query = $this->db->query("select * from user_sessions WHERE user_type = 1 and loggedIn = 1 and oid in (" . $mas_ids . ")")->result();
//             
//                foreach ($query as $row)
//                {
//                   
//                    $DriverOnline[] = $row->oid;
//                }
//                $mas_ids = implode(',', array_filter(array_unique($DriverOnline)));
//            }
//            $companywhere = '';
//            if ($company != '0') {
//                $companywhere = "and mas.company_id=" . $company;
//            }


            $this->datatables->select("mas.mas_id as rahul,mas.first_name ,mas.last_name ,mas.mobile, mas.email,"
                            . "(select uniq_identity from workplace where mas.workplace_id = workplace_id),"
                            . "mas.created_dt,mas.profile_pic as pp,"
                            . "round((select avg(star_rating) from master_ratings where mas_id = mas.mas_id),1),"
                            . "(select companyname from company_info where company_id = mas.company_id),"
                            . "(select (case type when 2 then 'android_icon.png' when 1 then 'iphone-logo.png' END) from user_sessions where oid = mas.mas_id  and user_type = 1 order by oid DESC limit 0,1) as type_img", false)
                    //. "(select uniq_identity from workplace where workplace_id = mas.workplace_id) as vehicleid",false)
//                            . "(select Vehicle_Image from workplace where workplace_id = mas.workplace_id ) as vehicleimage", false)
//                    ->unset_column('vehicleimage')
                    ->unset_column('type_img')
                    ->unset_column('pp')
                    ->add_column('PROFILE PIC', '<img src="' . ServiceLink . '/pics/$1" width="50px" height="50px" class="imageborder">', 'pp')
                    ->add_column('DEVICE TYPE', '<img src="' . base_url() . '../../admin/assets/$1" width="30px">', 'type_img')
//                    ->add_column('VEHICLE IMAGE', '<img src="' . base_url() . ServiceLink.'/$1" width="50px" class="imageborder">', 'vehicleimage')
                    ->add_column('LATLONG', "get_lat/$1", 'rahul')
//                      ->add_column('LATITUDE', "get_lat/$1", 'rahul')
//                    ->add_column('LONGITUDE', "get_lon/$1", 'rahul')
                    ->add_column('SELECT', '<input type="checkbox" class="checkbox" name="checkbox" value="$1"/>', 'rahul')
                    ->from("master mas")
                    ->where("mas.mas_id in (" . $mas_ids . ")" . $companywhere);


//        $quaery = $this->db->query("SELECT mas.mas_id, mas.first_name ,mas.zipcode, mas.profile_pic, mas.last_name, mas.email, mas.mobile, mas.status,mas.created_dt,(select type from user_sessions where oid = mas.mas_id order by oid DESC limit 0,1) as dev_type FROM master mas where  mas.mas_id IN (" . $mas_ids . ")  order by mas.mas_id DESC")->result();
//        return $quaery;
        }



        $this->db->order_by("mas.mas_id", "desc");
        echo $this->datatables->generate();
    }

    function uniq_val_chk() {

        $query = $this->db->query('select * from workplace where uniq_identity = "' . $this->input->post('uniq_id') . '"');
        if ($query->num_rows() > 0) {

            echo json_encode(array('msg' => "This vehicleId Is Already Allocated", 'flag' => '1'));
        } else {
            echo json_encode(array('msg' => "", 'flag' => '0'));
        }
        return;
    }

    function get_options($id) {

        if ($id != '')
            return '<img src="' . base_url() . ServiceLink . '/' . $id . '" width="50px">';
        else
            return '<img src="' . base_url() . '../../admin/img/user.jpg" width="50px">';
    }

    function get_devicetype($id) {
//return $id;

        if ($id)
            return '<img src="' . base_url() . '../../admin/assets/' . $id . '" width="50px" class="imageborder" >';
        else
            return '<img src="' . base_url() . '../../admin/img/user.jpg" width="50px" class="imageborder">';
    }

    function datatable_bookings($status = '') {

        $this->load->library('Datatables');
        $this->load->library('table');

        $this->datatables->select("a.appointment_id,m.mas_id,m.first_name,s.first_name,a.address_line1,a.drop_addr1,a.appointment_dt,a.distance_in_mts")->from("appointment a,master m,slave s")->where("a.slave_id = s.slave_id and a.mas_id = m.mas_id"); //order by slave_id DESC ",false);

        echo $this->datatables->generate();
    }

    function datatable_dispatcher($status = '') {

        $this->load->library('Datatables');
        $this->load->library('table');


        $city = $this->session->userdata('city_id');

        $cityCond = "";

        if ($city != 0) {
            $cityCond = ' and city = "' . $city . '"';
        } else {
            
        }

        $this->datatables->select('dis_id,(select City_Name from city where City_Id = city),dis_email,dis_name')
                ->add_column('select', '<input type="checkbox" class="checkbox" name="checkbox" value="$1"/>', 'dis_id')
                ->from('dispatcher')
                ->where('status = "' . $status . '"' . $cityCond); //order by slave_id DESC ",false);

        echo $this->datatables->generate();
    }

    function datatable_document($status = '') {

        $this->load->library('Datatables');
        $this->load->library('table');

        $company = $this->session->userdata('company_id');

        if ($status == '1') {

            $this->datatables->select("d.doc_ids,c.first_name,c.last_name,d.expirydate,d.url")
                    ->unset_column('d.url')
                    ->add_column('VIEW', '<button type="button" name="view"  width="50px">'
                            . '<a target="_blank" href="' . base_url() . ServiceLink . '/$1">view</a><a target="_blank" href="' . base_url() . ServiceLink . '/$1"></button><button type="button" name="view"  width="50px">download</button></a>', 'd.url'
                    )
                    ->from("master c,docdetail d")
                    ->where("c.mas_id = d.driverid and d.doctype=1" . ($company != 0 ? ' and c.company_id = "' . $company . '"' : '')); //order by slave_id DESC ",false);
        } else if ($status == '2') {

            $this->datatables->select("d.doc_ids,c.first_name,c.last_name,d.expirydate,d.url")
                    ->unset_column('d.url')
                    ->add_column('VIEW', '<a target="_blank" href="' . base_url() . ServiceLink . '/$1"><button type="button" name="view"  width="50px">view</button></a><a target="_blank" href="' . base_url() . ServiceLink . '/$1"><button type="button" name="view"  width="50px">download</button></a>', 'd.url'
                    )
                    ->from("master c,docdetail d")->where("c.mas_id = d.driverid and d.doctype=2" . ($company != 0 ? ' and c.company_id = "' . $company . '"' : '')); //order by slave_id DESC ",false);
        } else if ($status == '3') {


            $this->datatables->select("d.docid,d.vechileid,(select companyname from company_info where company_id = w.company) as companyname,d.expirydate,d.url")
                    ->unset_column('d.url')
                    ->add_column('VIEW', '<button type="button" name="view"  width="50px"><a target="_blank" href="' . base_url() . ServiceLink . '/$1">view</button></a><a target="_blank" href="' . base_url() . ServiceLink . '/$1"><button type="button" name="view"  width="50px">download</button></a>', 'd.url'
                    )
//                     ->select("(select companyname from company_info where company_id = w.company) as companyname",false)
                    ->from("workplace w,vechiledoc d,vehicleType v")
                    ->where("w.title = v.id and w.workplace_id = d.vechileid and d.doctype = 2" . ($company != 0 ? ' and w.company = "' . $company . '"' : '')); //order by slave_id DESC ",false);
        } else if ($status == '4') {

            $this->datatables->select("d.docid,d.vechileid,(select companyname from company_info where company_id = w.company) as companyname,d.url,d.expirydate")
                    ->unset_column('d.url')
                    ->add_column('VIEW', '<a target="_blank" href="' . base_url() . ServiceLink . '/$1"><button type="button" name="view"  width="50px">view</button></a><a target="_blank" href="' . base_url() . ServiceLink . '/$1"><button type="button" name="view"  width="50px">download</button></a>', 'd.url'
                    )
//                     ->select("(select companyname from company_info where company_id = w.company) as companyname",false)
                    ->from("workplace w,vechiledoc d,vehicleType v")
                    ->where("w.title = v.id and w.workplace_id = d.vechileid and d.doctype = 3" . ($company != 0 ? ' and w.company = "' . $company . '"' : '')); //order by slave_id DESC ",false);
        } else if ($status == '5') {

            $this->datatables->select("d.docid,d.vechileid,(select companyname from company_info where company_id = w.company) as companyname,d.url,d.expirydate")
                    ->select("(select companyname from company_info where company_id = w.company) as companyname", false)
                    ->unset_column('d.url')
                    ->add_column('VIEW', '<a target="_blank" href="' . base_url() . ServiceLink . '/$1"><button type="button" name="view"  width="50px">view</button></a><a target="_blank" target="_blank" href="' . base_url() . ServiceLink . '/$1"><button type="button" name="view"  width="50px">download</button></a>', 'd.url'
                    )
                    ->from("workplace w,vechiledoc d,vehicleType v")
                    ->where("w.title = v.id and w.workplace_id = d.vechileid and d.doctype = 1" . ($company != 0 ? ' and w.company = "' . $company . '"' : '')); //order by slave_id DESC ",false);
        }

        echo $this->datatables->generate();
    }

    function datatable_driverreview($status = '') {


        $this->load->library('Datatables');
        $this->load->library('table');

        $this->datatables->select("r.appointment_id,a.appointment_dt, d.first_name,r.slave_id,r.review, r.star_rating")
//                ->unset_column('$i')
//                ->add_column('sl.no','value="$1"', '$i++')
//                ->unset_column('r.appointment_id')
                ->add_column('select', '<input type="checkbox" class="checkbox" name="checkbox" value="$1"/>', 'r.appointment_id')
                ->from("master_ratings r, master d, slave p,appointment a", false)
                ->where("r.slave_id = p.slave_id  AND r.mas_id = d.mas_id  AND r.status ='" . $status . "'AND r.review<>'' AND a.appointment_id = r.appointment_id"); //order by slave_id DESC ",false);
// ->where("r.slave_id = p.slave_id  AND r.mas_id = d.mas_id  AND r.status ='" . $status . "' AND a.appointment_id = r.appointment_id"); //order by slave_id DESC ",false);
        $this->db->order_by("r.appointment_id", "desc");
        echo $this->datatables->generate();
    }

    function editdispatchers_city() {
        $val = $this->input->post('val');

        $var = $this->db->query("select city from dispatcher where dis_id='" . $val . "'")->result();
        return $var;
    }

    function datatable_passengerrating() {

        $this->load->library('Datatables');
        $this->load->library('table');
        $status = 1;
        $this->datatables->select('p.slave_id, p.first_name ,p.email,IFNULL((select round(avg(rating),1)  from passenger_rating where p.slave_id =slave_id), 0) as rating', false)
                ->from('slave p'); //->where('r.status =" ' . $status . '"'); //order by slave_id DESC ",false);
        $this->db->order_by("p.slave_id", "desc");
        echo $this->datatables->generate();
    }

    function datatable_compaigns($status) {

        $this->load->library('Datatables');
        $this->load->library('table');
        if ($status == 1) {
            $this->datatables->select("cp.id,cp.discount,cp.referral_discount,cp.message,c.city_name")
                    ->unset_column('cp.id')
                    ->add_column('select', '<input type="checkbox" class="checkbox" name="checkbox" value="$1"/>', ' cp.id')
                    ->from(" coupons cp, city c")
                    ->where('cp.city_id = c.city_id and cp.coupon_type = " ' . $status . ' " and cp.status = "0" and user_type = 2'); //order by slave_id DESC ",false);
        } elseif ($status == 2) {

            $this->datatables->select("cp.id,cp.coupon_code,cp.start_date,cp.expiry_date, cp.discount,cp.message,c.city_name")
                    ->unset_column('cp.id')
                    ->add_column('select', '<input type="checkbox" class="checkbox" name="checkbox" value="$1"/>', ' cp.id')
                    ->from(" coupons cp, city c")
                    ->where('cp.city_id = c.city_id and cp.coupon_type = " ' . $status . ' " and cp.status = "0" and user_type = 2'); //order by slave_id DESC ",false);
        } else if ($status == 3) {
            $this->datatables->select("cp.id,cp.coupon_code,cp.start_date,cp.expiry_date, cp.discount,cp.message,c.city_name")
                    ->unset_column('cp.id')
                    ->add_column('select', '<input type="checkbox" class="checkbox" name="checkbox" value="$1"/>', ' cp.id')
                    ->from(" coupons cp, city c")
                    ->where('cp.city_id = c.city_id and cp.coupon_type = " ' . $status . ' " and cp.status = "0" and user_type = 2'); //order by slave_id DESC ",false);
        }
        echo $this->datatables->generate();
    }

    function editNewVehicleData() {

        $vehicle_id = $this->input->post('vehicle_id');
        $title = $this->input->post('title');
        $vehiclemodel = $this->input->post('vehiclemodel');
        $vechileregno = $this->input->post('vechileregno');
        $licenceplaetno = $this->input->post('licenceplaetno');
        $vechilecolor = $this->input->post('vechilecolor');
        $type_id = $this->input->post('getvechiletype');
        $expirationrc = $this->input->post('expirationrc');
        $expirationinsurance = $this->input->post('expirationinsurance');
        $expirationpermit = $this->input->post('expirationpermit');
        $companyid = $this->input->post('company_id'); //$this->session->userdata('LoginId');

        $insuranceno = $this->input->post('Vehicle_Insurance_No'); //$_REQUEST['Vehicle_Insurance_No'];
//        $documentfolder = $_SERVER['DOCUMENT_ROOT'] . '/roadyo_live/pics/';


        $documentfolder = $_SERVER['DOCUMENT_ROOT'] . '/' . mainfolder . '/pics/';


        if ($_FILES["certificate"]["name"] != '' && $_FILES["certificate"]["size"] > 0) {
            $name = $_FILES["certificate"]["name"];
            $ext = substr($name, strrpos($name, '.') + 1); //explode(".", $name); # extra () to prevent notice
            $cert_name = (rand(1000, 9999) * time()) . '.' . $ext;
            move_uploaded_file($_FILES['certificate']['tmp_name'], $documentfolder . $cert_name);
            $this->db->query("update vechiledoc set expirydate = '" . $expirationrc . "', url = '" . $cert_name . "' where doctype = 1 and vechileid = '" . $vehicle_id . "'");
        } else {
            $this->db->query("update vechiledoc set expirydate = '" . $expirationrc . "' where doctype = 1 and vechileid = '" . $vehicle_id . "'");
        }
        if ($_FILES["insurcertificate"]["name"] != '' && $_FILES["insurcertificate"]["size"] > 0) {
            $name = $_FILES["insurcertificate"]["name"];
            $ext = substr($name, strrpos($name, '.') + 1); //explode(".", $name); # extra () to prevent notice
            $cert_name = (rand(1000, 9999) * time()) . '.' . $ext;
            move_uploaded_file($_FILES['insurcertificate']['tmp_name'], $documentfolder . $cert_name);
            $this->db->query("update vechiledoc set expirydate = '" . $expirationinsurance . "', url = '" . $cert_name . "' where doctype = 2 and vechileid = '" . $vehicle_id . "'");
        } else {
            $this->db->query("update vechiledoc set expirydate = '" . $expirationinsurance . "' where doctype = 2 and vechileid = '" . $vehicle_id . "'");
        }
        if ($_FILES["carriagecertificate"]["name"] != '' && $_FILES["carriagecertificate"]["size"] > 0) {
            $name = $_FILES["carriagecertificate"]["name"];
            $ext = substr($name, strrpos($name, '.') + 1); //explode(".", $name); # extra () to prevent notice
            $cert_name = (rand(1000, 9999) * time()) . '.' . $ext;
            move_uploaded_file($_FILES['carriagecertificate']['tmp_name'], $documentfolder . $cert_name);
            $this->db->query("update vechiledoc set expirydate = '" . $expirationpermit . "', url = '" . $cert_name . "' where doctype = 3 and vechileid = '" . $vehicle_id . "'");
        } else {
            $this->db->query("update vechiledoc set expirydate = '" . $expirationpermit . "' where doctype = 3 and vechileid = '" . $vehicle_id . "'");
        }

        if ($_FILES["imagefile"]["name"] != '' && $_FILES["imagefile"]["size"] > 0) {
            $name = $_FILES["imagefile"]["name"];
            $ext = substr($name, strrpos($name, '.') + 1); //explode(".", $name); # extra () to prevent notice
            $cert_name = (rand(1000, 9999) * time()) . '.' . $ext;
            move_uploaded_file($_FILES['imagefile']['tmp_name'], $documentfolder . $cert_name);
            $updateImageString = ", Vehicle_Image = '" . $cert_name . "'";
        }


//        try {
//
//            move_uploaded_file($_FILES['insurcertificate']['tmp_name'], $documentfolder . $insurance_name);
//            move_uploaded_file($_FILES['carriagecertificate']['tmp_name'], $documentfolder . $carriage_name);
//            move_uploaded_file($_FILES['imagefile']['tmp_name'], $documentfolder . $image_name);
//        } catch (Exception $ex) {
//            print_r($ex);
//            return false;
//        }

        $this->db->query("update workplace set type_id = '" . $type_id . "',Title = '" . $title . "',Vehicle_Model = '" . $vehiclemodel . "',Vehicle_Reg_No = '" . $vechileregno . "', License_Plate_No = '" . $licenceplaetno . "',Vehicle_Color = '" . $vechilecolor . "',company = '" . $companyid . "',Vehicle_Insurance_No = '" . $insuranceno . "'" . $updateImageString . " where workplace_id = '" . $vehicle_id . "'");

        if ($this->db->affected_rows > 0) {
            return true;
        } else {
            return false;
        }


        return;
    }

    function delete_vehicletype() {
        $val = $this->input->post('val');
        $this->load->library('mongo_db');

        $db = $this->mongo_db->db;
        $dbcorsor = $db->selectCollection('vehicleTypes');
        foreach ($val as $row) {

            $data = $this->db->query("delete from workplace_types where type_id='" . $row . "' ");
            $dbcorsor->remove(array('type' => (int) $row));
        }
        if ($this->db->affected_rows() > 0) {

            echo json_encode(array('msg' => "vehicle type deleted successfully", 'flag' => 1, 'data' => $data));
            return;
        }
    }

    function delete_company() {

        $val = $this->input->post('val');
        foreach ($val as $row) {

            $this->db->query("delete from company_info where company_id='" . $row . "' ");

            $this->db->query("delete from master where company_id ='" . $row . "'");
        }
        if ($this->db->affected_rows() > 0) {
            echo json_encode(array('msg' => "company/companies deleted successfully", 'flag' => 1));
            return;
        }
    }

    function get_documentdata($status) {
        if ($status == 1) {
            $result = $this->db->query("select c.first_name,c.last_name,d.url,d.doc_ids,d.expirydate from master c,docdetail d where c.mas_id=d.driverid and d.doctype=1")->result();
            return $result;
        } else if ($status == 2) {
            $result = $this->db->query("select c.first_name,c.last_name,d.url,d.doc_ids,d.expirydate from master c,docdetail d where c.mas_id=d.driverid and d.doctype=2")->result();
            return $result;
        } else if ($status == 3) {
            $result = $this->db->query("SELECT d.docid,v.vehicletype,d.expirydate,d.vechileid,d.url,w.company,(select companyname from company_info where company_id = w.company) as companyname FROM workplace w,vechiledoc d,vehicleType v where w.title=v.id and w.workplace_id = d.vechileid and d.doctype=2")->result();
            return $result;
        } else if ($status == 4) {
            $result = $this->db->query("SELECT d.docid,v.vehicletype,d.expirydate,d.vechileid,d.url,w.company,(select companyname from company_info where company_id = w.company) as companyname FROM workplace w,vechiledoc d,vehicleType v where w.title=v.id and w.workplace_id = d.vechileid and d.doctype=3")->result();
            return $result;
        } else if ($status == 5) {
            $result = $this->db->query("SELECT d.docid,v.vehicletype,d.expirydate,d.vechileid,d.url,w.company,(select companyname from company_info where company_id = w.company) as companyname FROM workplace w,vechiledoc d,vehicleType v where w.title=v.id and w.workplace_id = d.vechileid and d.doctype=2")->result();
            return $result;
        } else if ($status == 5) {
            $result = $this->db->query("SELECT d.docid,v.vehicletype,d.expirydate,d.vechileid,d.url,w.company,(select companyname from company_info where company_id = w.company) as companyname FROM workplace w,vechiledoc d,vehicleType v where w.title=v.id and w.workplace_id = d.vechileid and d.doctype=2")->result();
            return $result;
        } else if ($status == 6) {
            $result = $this->db->query("SELECT d.url,d.docid,v.vehicletype,d.expirydate,d.vechileid,d.url,w.company,(select companyname from company_info where company_id = w.company) as companyname FROM workplace w,vechiledoc d,vehicleType v where w.title=v.id and w.workplace_id = d.vechileid and d.doctype=2")->result();
            return $result;
        }
    }

    //* naveena models *//







    function setsessiondata($tablename, $LoginId, $res, $email, $password) {
        $sessiondata = array(
            'emailid' => $email,
            'password' => $password,
            'LoginId' => $res->$LoginId,
            'profile_pic' => $res->logo,
            'first_name' => $res->companyname,
            'table' => $tablename,
            'city_id' => '0', 'company_id' => '0',
            'validate' => true
        );



        return $sessiondata;
    }

    function Drivers($status = '') {

        $quaery = $this->db->query("SELECT mas.mas_id, mas.first_name ,mas.zipcode, mas.profile_pic, mas.last_name, mas.email, mas.mobile, mas.status,mas.created_dt,(select type from user_sessions where oid = mas.mas_id order by oid DESC limit 0,1) as dev_type FROM master mas where  mas.status IN (" . $status . ") and mas.company_id IN (" . $this->session->userdata('LoginId') . ") order by mas.mas_id DESC")->result();
        return $quaery;
    }

    function datatable($status = '') {

        $this->load->library('Datatables');
        $this->load->library('table');

//        $explodeDateTime = explode(' ', date("Y-m-d H:s:i"));
//        $explodeDate = explode('-', $explodeDateTime[0]);
//        $weekData = $this->week_start_end_by_date(date("Y-m-d H:s:i"));
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
//        $this->datatables->select('doc.mas_id,doc.first_name,doc.workplace_id, doc.last_name, doc.email, doc.license_num,doc.license_exp,doc.board_certification_expiry_dt, doc.mobile, doc.status, doc.profile_pic')
//            ->select('(select count(appointment_id) from appointment where mas_id = doc.mas_id and status = 9) as cmpltApts')
//            ->select("(select sum(amount) from appointment where mas_id = doc.mas_id and DATE(appointment_dt) = '" . $explodeDateTime[0] . "' and status = 9) as today_earnings")
//            ->select('(select amount from appointment where mas_id = doc.mas_id and status = 9 order by appointment_id DESC limit 0'.','.'1) as last_billed_amount',false)
//            ->select("(select sum(amount) from appointment where mas_id = doc.mas_id and status = 9 and DATE(appointment_dt) BETWEEN '" . $weekData['first_day_of_week'] . "' and '" . $weekData['last_day_of_week'] . "') as week_earnings")
//            ->select("(select sum(amount) from appointment where mas_id = doc.mas_id and status = 9 and DATE_FORMAT(appointment_dt,  '%Y-%m') = '" . $explodeDate[0] . '-' . $explodeDate[1] . "' ) as month_earnings",false)
//            ->select("(select sum(amount) from appointment where mas_id = doc.mas_id and status = 9) as total_earnings")
//            ->from('master doc');
//        $this->datatables->select('count(appointment_id) as cmpltApts')->from('appointment')->where('mas_id = doc.mas_id and status = 9');
//        $this->datatables->select('sum(amount) as today_earnings')->from('appointment')->where('mas_id = doc.mas_id DATE(appointment_dt) = "' . $explodeDateTime[0] . '"and status = 9');


        $this->datatables->select("*")->from('slave')->where('status', 3); //order by slave_id DESC ",false);

        echo $this->datatables->generate();
    }

    function validateEmail() {

        $query = $this->db->query("select mas_id from master where email='" . $this->input->post('email') . "'");
        if ($query->num_rows() > 0) {
            echo json_encode(array('msg' => '1'));
            return;
        } else {
            echo json_encode(array('msg' => '0'));
        }
    }

    function validatedispatchEmail() {

        $query = $this->db->query("select dis_id from dispatcher where dis_email='" . $this->input->post('email') . "'");
        if ($query->num_rows() > 0) {
            echo json_encode(array('msg' => '1'));
            return;
        } else {
            echo json_encode(array('msg' => '0'));
            return;
        }
    }

    function get_workplace() {
        $res = $this->db->query("select * from workplace_types")->result();
        return $res;
    }

    function get_cities() {
        $query = $this->db->query('select * from city_available')->result();
        return $query;
    }

    function loadcity() {
        $countryid = $this->input->post('country');
        $Result = $this->db->query("select * from city where Country_Id=" . $countryid . "")->result();
        return $Result;
    }

    function loadcompany() {
        $cityid = $this->input->post('city');
        $Result = $this->db->query("select * from company_info where city=" . $cityid . " and status = 3 ")->result_array();
        return $Result;
    }

    function get_city() {
        return $this->db->query("select ci.*,co.Country_Name from city_available ci,country co where ci.country_id = co.country_id ORDER BY ci.City_Name ASC")->result();
    }

    function get_companyinfo($status) {
        return $this->db->query("select * from company_info where status = '" . $status . "' ")->result();
    }

    function editdriver($status = '') {
//        $driverid = $this->input->post('val');

        $data['masterdata'] = $this->db->query("select * from master where mas_id ='" . $status . "' ")->result();

        $data['masterdoc'] = $this->db->query("select * from docdetail where driverid ='" . $status . "' ")->result();

        return $data;
    }

    function getbooking_data($status = '', $companyid = '') {

//        return $this->db->query("select a.*,m.first_name,m.last_name,s.first_name as sfirst_name,s.last_name as slast_name from appointment a,master m,slave s where a.slave_id = s.slave_id and a.mas_id = m.mas_id ")->result();
        $this->load->library('Datatables');
        $this->load->library('table');

        $companyid = $this->session->userdata('company_id');
        if ($status == '11' && $this->session->userdata('company_id') == '0')
            $query = 'a.slave_id = s.slave_id and a.mas_id = m.mas_id';
        else if ($this->session->userdata('company_id') != '0' && $status != '11')
            $query = 'a.slave_id = s.slave_id and a.mas_id = m.mas_id and a.status = "' . $status . '" and m.company_id = "' . $companyid . '" ';
        else if ($this->session->userdata('company_id') == '0' && $status != '11')
            $query = 'a.slave_id = s.slave_id and a.mas_id = m.mas_id and a.status = "' . $status . '" ';
        else if ($status == '11' && $this->session->userdata('company_id') != '0')
            $query = 'a.slave_id = s.slave_id and a.mas_id = m.mas_id and  m.company_id = "' . $companyid . '" ';

        $this->datatables->select("a.appointment_id,(case a.appt_type when 1 then 'Now' when 2 then 'Later' END),m.mas_id,m.first_name,s.first_name as name,a.address_line1,a.drop_addr1,a.appointment_dt as rec_time,DATE_FORMAT(a.appointment_dt,'%b %d %Y %h:%i %p'),round(a.distance_in_mts/1609.344, 2),
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
    'Driver arrived.'
     when 8   then
    'Appointment started.'
     when 9   then
    'Appointment completed.'
    when 10 then
    'Appointment timed out.'
    else
    'Status Unavailable.'
    END) as status_result", false)
                ->edit_column('rec_time', 'getRec_time/$1', 'a.appointment_id')
                ->from('appointment a,master m,slave s')
                ->where($query);

        $this->db->order_by('a.appointment_id', 'DESC');
//            ->add_column('Actions', '<img src="asdf">', 'City_id')
        echo $this->datatables->generate();
    }

    public function getDatafromdate_for_all_bookings($stdate = '', $enddate = '', $status = '', $company_id = '') {



        $this->load->library('Datatables');
        $this->load->library('table');

//            if($status == '11' && $company_id == '0')
//                $query = 'a.slave_id = s.slave_id and a.mas_id = m.mas_id and a.status';
//            else
        if ($company_id == '0' && $status == '11')
            $query = 'a.slave_id = s.slave_id and a.mas_id = m.mas_id  and DATE(a.appointment_dt) BETWEEN "' . date('Y-m-d', strtotime($stdate)) . '" AND "' . date('Y-m-d', strtotime($enddate)) . '"';
        else if ($company_id != '0' && $status != '11')
            $query = 'a.slave_id = s.slave_id and a.mas_id = m.mas_id and a.status = "' . $status . '" and m.company_id = "' . $company_id . '" and DATE(a.appointment_dt) BETWEEN "' . date('Y-m-d', strtotime($stdate)) . '" AND "' . date('Y-m-d', strtotime($enddate)) . '"';
        else if ($status == '11' && $company_id != '0')
            $query = 'a.slave_id = s.slave_id and a.mas_id = m.mas_id and m.company_id = "' . $company_id . '" and DATE(a.appointment_dt) BETWEEN "' . date('Y-m-d', strtotime($stdate)) . '" AND "' . date('Y-m-d', strtotime($enddate)) . '"';
        else if ($status != '11' && $company_id == '0')
            $query = 'a.slave_id = s.slave_id and a.mas_id = m.mas_id and a.status = "' . $status . '" and DATE(a.appointment_dt) BETWEEN "' . date('Y-m-d', strtotime($stdate)) . '" AND "' . date('Y-m-d', strtotime($enddate)) . '"';
        else
            $query = 'a.slave_id = s.slave_id and a.mas_id = m.mas_id and a.status = "' . $status . '" and m.company_id = "' . $company_id . '" and DATE(a.appointment_dt) BETWEEN "' . date('Y-m-d', strtotime($stdate)) . '" AND "' . date('Y-m-d', strtotime($enddate)) . '"';

        $this->datatables->select("a.appointment_id,(case a.appt_type when 1 then 'Now' when 2 then 'Later' END),m.mas_id,m.first_name,s.first_name as name,a.address_line1,a.drop_addr1,a.appointment_dt as rec_time,DATE_FORMAT(a.appointment_dt,'%b %d %Y %h:%i %p'),round(a.distance_in_mts/1609.344, 2),
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
    'Driver arrived.'
     when 8   then
    'Appointment started.'
     when 9   then
    'Appointment completed.'
    when 10 then
    'Appointment timed out.'
    else
    'Status Unavailable.'
    END) as status_result", false)
                ->edit_column('rec_time', 'getRec_time/$1', 'a.appointment_id')
                ->from('appointment a,master m,slave s')
                ->where($query);

        $this->db->order_by('a.appointment_id', 'DESC');
        echo $this->datatables->generate();
    }

    function payroll() {

        $explodeDateTime = explode(' ', date("Y-m-d H:s:i"));
        $explodeDate = explode('-', $explodeDateTime[0]);
        $weekData = $this->week_start_end_by_date(date("Y-m-d H:s:i"));

        $this->load->library('Datatables');
        $this->load->library('table');
        $wereclousetocome = ';';
        if ($this->session->userdata('company_id') != '0') {
            $wereclousetocome = "a.mas_id = doc.mas_id and  doc.company_id ='" . $this->session->userdata('company_id') . "'";

            $this->datatables->select('doc.mas_id,doc.first_name,'
                            . "(case  when TRUNCATE((SELECT SUM(mas_earning) FROM appointment WHERE  payment_type = 2 and doc.mas_id  = mas_id and status = 9),2)  IS NULL then '--'  else TRUNCATE((SELECT SUM(mas_earning) FROM appointment WHERE  payment_type = 2 and doc.mas_id  = mas_id and status = 9),2) END) as MAS_CASH_EARNINGS ,"
                            . "TRUNCATE((SELECT SUM(tip_amount) FROM appointment WHERE  doc.mas_id  = mas_id  AND  STATUS = 9),2) as TIP,"
                            . "(case  when TRUNCATE((SELECT SUM(mas_earning) FROM appointment WHERE  payment_type = 1 and doc.mas_id  = mas_id and status = 9),2)  IS NULL then '--'  else TRUNCATE((SELECT SUM(mas_earning) FROM appointment WHERE  payment_type = 1 and doc.mas_id  = mas_id and status = 9),2) END) as MAS_CARD_EARNINGS ,"
                            . "(case  when TRUNCATE((SELECT SUM(mas_earning) FROM appointment WHERE   doc.mas_id  = mas_id and status = 9),2)  IS NULL then '--'  else TRUNCATE((SELECT SUM(mas_earning) FROM appointment WHERE    doc.mas_id  = mas_id and  status = 9),2) END) as DRIVER_EARNINGS ,"
                            . "(case  when TRUNCATE((SELECT (SUM(amount) + SUM(tip_amount)) FROM appointment WHERE   doc.mas_id  = mas_id and payment_type = 2 and status = 9),2)  IS NULL then '--'  else TRUNCATE((SELECT (SUM(amount) + SUM(tip_amount)) FROM appointment WHERE    doc.mas_id  = mas_id and payment_type = 2 and status = 9),2) END) as DRIVER_COLLECTED,"
                            . "(SELECT COALESCE( SUM(pay_amount) ,0) FROM payroll WHERE doc.mas_id  = mas_id) as TOTALRECIVED,"
                            . "COALESCE( TRUNCATE(((SELECT COALESCE(SUM(mas_earning),0) FROM appointment WHERE  doc.mas_id  = mas_id  AND  STATUS = 9 and payment_type = 1)) - (SELECT COALESCE(SUM(mas_earning),0) FROM appointment WHERE   doc.mas_id  = mas_id and payment_type = 2 and status = 9) - (SELECT COALESCE( SUM(pay_amount) ,0) FROM payroll WHERE doc.mas_id  = mas_id ),2),0) AS due", false)
                    ->add_column('SHOW', '<a href="' . base_url("index.php/superadmin/DriverDetails/$1") . '"><button class="btn btn-success btn-cons" style="min-width: 83px !important;">DETAILS</button></a>
            <a href="' . base_url("index.php/superadmin/Driver_pay/$1") . '"><button class="btn btn-success btn-cons" style="min-width: 83px !important;">Pay</button>', 'doc.mas_id ')
                    ->from('master doc', false)
                    ->where($wereclousetocome);
        } else {

            $this->datatables->select('doc.mas_id,doc.first_name,'
                            . "(case  when TRUNCATE((SELECT SUM(mas_earning) FROM appointment WHERE  payment_type = 2 and doc.mas_id  = mas_id and status = 9),2)  IS NULL then '--'  else TRUNCATE((SELECT SUM(mas_earning) FROM appointment WHERE  payment_type = 2 and doc.mas_id  = mas_id and status = 9),2) END) as MAS_CASH_EARNINGS ,"
                            . "TRUNCATE((SELECT SUM(tip_amount) FROM appointment WHERE  doc.mas_id  = mas_id  AND  STATUS = 9),2) as TIP,"
                            . "(case  when TRUNCATE((SELECT SUM(mas_earning) FROM appointment WHERE  payment_type = 1 and doc.mas_id  = mas_id and status = 9),2)  IS NULL then '--'  else TRUNCATE((SELECT SUM(mas_earning) FROM appointment WHERE  payment_type = 1 and doc.mas_id  = mas_id and status = 9),2) END) as MAS_CARD_EARNINGS ,"
                            . "(case  when TRUNCATE((SELECT SUM(mas_earning) FROM appointment WHERE   doc.mas_id  = mas_id and status = 9),2)  IS NULL then '--'  else TRUNCATE((SELECT SUM(mas_earning) FROM appointment WHERE    doc.mas_id  = mas_id and  status = 9),2) END) as DRIVER_EARNINGS ,"
                            . "(case  when TRUNCATE((SELECT (SUM(amount) + SUM(tip_amount)) FROM appointment WHERE   doc.mas_id  = mas_id and payment_type = 2 and status = 9),2)  IS NULL then '--'  else TRUNCATE((SELECT (SUM(amount) + SUM(tip_amount)) FROM appointment WHERE    doc.mas_id  = mas_id and payment_type = 2 and status = 9),2) END) as DRIVER_COLLECTED,"
                            . "(SELECT COALESCE( SUM(pay_amount) ,0) FROM payroll WHERE doc.mas_id  = mas_id) as TOTALRECIVED,"
                            . "COALESCE( TRUNCATE(
((SELECT COALESCE(SUM(mas_earning),0) FROM appointment WHERE  doc.mas_id  = mas_id  AND  STATUS = 9 and payment_type = 1)) - (SELECT COALESCE(SUM(mas_earning),0) FROM appointment WHERE   doc.mas_id  = mas_id and payment_type = 2 and status = 9) - (SELECT COALESCE( SUM(pay_amount) ,0) FROM payroll WHERE doc.mas_id  = mas_id ),2),0) AS due", false)
                    ->add_column('SHOW', '<a href="' . base_url("index.php/superadmin/DriverDetails/$1") . '"><button class="btn btn-success btn-cons" style="min-width: 83px !important;">DETAILS</button></a>
            <a href="' . base_url("index.php/superadmin/Driver_pay/$1") . '"><button class="btn btn-success btn-cons" style="min-width: 83px !important;">Pay</button>', 'doc.mas_id ')
                    ->from('master doc', false);
        }
        $this->db->order_by('doc.mas_id', 'DESC');
        echo $this->datatables->generate();
    }

//    
//    function payroll() {
//
//        $explodeDateTime = explode(' ', date("Y-m-d H:s:i"));
//        $explodeDate = explode('-', $explodeDateTime[0]);
//        $weekData = $this->week_start_end_by_date(date("Y-m-d H:s:i"));
//
//        $this->load->library('Datatables');
//        $this->load->library('table');
//        $wereclousetocome = ';';
//        if ($this->session->userdata('company_id') != '0') {
//            $wereclousetocome = "a.mas_id = doc.mas_id and  doc.company_id ='" . $this->session->userdata('company_id') . "'";
//
// 
//            
//            $this->datatables->select('doc.mas_id as masid,doc.first_name,'
//                            . "(case  when TRUNCATE((SELECT SUM(mas_earning) FROM appointment WHERE  payment_type = 1 and masid = mas_id and status = 9),2)  IS NULL then '--'  else TRUNCATE((SELECT SUM(mas_earning) FROM appointment WHERE  payment_type = 1 and masid = mas_id and status = 9),2) END) as MAS_CARD_EARNINGS ,"
//                            . "(case  when TRUNCATE((SELECT SUM(mas_earning) FROM appointment WHERE  payment_type = 2 and masid = mas_id and status = 9),2)  IS NULL then '--'  else TRUNCATE((SELECT SUM(mas_earning) FROM appointment WHERE  payment_type = 2 and masid = mas_id and status = 9),2) END) as MAS_CASH_EARNINGS ,"
//                            . "(case  when TRUNCATE((SELECT SUM(pg_commission) FROM appointment WHERE  payment_type = 1 and masid = mas_id and status = 9),2)  IS NULL then '--'  else TRUNCATE((SELECT SUM(pg_commission) FROM appointment WHERE  payment_type = 1 and masid = mas_id and status = 9),2) END) as PG_EARNINGS ,"
//                            . "(case  when TRUNCATE((SELECT SUM(app_owner_pl) FROM appointment WHERE   masid = mas_id and status = 9),2)  IS NULL then '--'  else TRUNCATE((SELECT SUM(app_owner_pl) FROM appointment WHERE    masid = mas_id and  status = 9),2) END) as APP_EARNINGS ,"
//                            . "(case  when TRUNCATE((SELECT SUM(mas_earning) FROM appointment WHERE   masid = mas_id and status = 9),2)  IS NULL then '--'  else TRUNCATE((SELECT SUM(mas_earning) FROM appointment WHERE    masid = mas_id and  status = 9),2) END) as DRIVER_EARNINGS ,"
//                            . "COALESCE((SELECT SUM(mas_earning) FROM appointment WHERE payment_type = 2 AND masid = mas_id  AND  STATUS = 9) - (SELECT SUM(app_owner_pl) FROM appointment WHERE payment_type = 1 AND masid = mas_id  AND STATUS = 9) - (SELECT COALESCE( SUM(pay_amount) ,0) FROM payroll WHERE masid = mas_id ),0) AS due", false)
//                    ->add_column('SHOW', '<a href="' . base_url("index.php/superadmin/DriverDetails/$1") . '"><button class="btn btn-success btn-cons" style="min-width: 83px !important;">DETAILS</button></a>
//                 <a href="' . base_url("index.php/superadmin/Driver_pay/$1") . '"><button class="btn btn-success btn-cons" style="min-width: 83px !important;">Pay</button>', 'masid')
//                    ->from('master doc,appointment a ', false)
//                    ->where($wereclousetocome);
//        } else {
//
//            $this->datatables->select('doc.mas_id as masid,doc.first_name,'
//                            . "(case  when TRUNCATE((SELECT SUM(mas_earning) FROM appointment WHERE  payment_type = 1 and masid = mas_id and status = 9),2)  IS NULL then '--'  else TRUNCATE((SELECT SUM(mas_earning) FROM appointment WHERE  payment_type = 1 and masid = mas_id and status = 9),2) END) as MAS_CARD_EARNINGS ,"
//                            . "(case  when TRUNCATE((SELECT SUM(mas_earning) FROM appointment WHERE  payment_type = 2 and masid = mas_id and status = 9),2)  IS NULL then '--'  else TRUNCATE((SELECT SUM(mas_earning) FROM appointment WHERE  payment_type = 2 and masid = mas_id and status = 9),2) END) as MAS_CASH_EARNINGS ,"
//                            . "(case  when TRUNCATE((SELECT SUM(pg_commission) FROM appointment WHERE  payment_type = 1 and masid = mas_id and status = 9),2)  IS NULL then '--'  else TRUNCATE((SELECT SUM(pg_commission) FROM appointment WHERE  payment_type = 1 and masid = mas_id and status = 9),2) END) as PG_EARNINGS ,"
//                            . "(case  when TRUNCATE((SELECT SUM(app_owner_pl) FROM appointment WHERE   masid = mas_id and status = 9),2)  IS NULL then '--'  else TRUNCATE((SELECT SUM(app_owner_pl) FROM appointment WHERE    masid = mas_id and  status = 9),2) END) as APP_EARNINGS ,"
//                            . "(case  when TRUNCATE((SELECT SUM(mas_earning) FROM appointment WHERE   masid = mas_id and status = 9),2)  IS NULL then '--'  else TRUNCATE((SELECT SUM(mas_earning) FROM appointment WHERE    masid = mas_id and  status = 9),2) END) as DRIVER_EARNINGS ,"
//                           . "COALESCE((SELECT SUM(mas_earning) FROM appointment WHERE payment_type = 2 AND masid = mas_id  AND  STATUS = 9) - (SELECT SUM(app_owner_pl) FROM appointment WHERE payment_type = 1 AND masid = mas_id  AND STATUS = 9) - (SELECT COALESCE( SUM(pay_amount) ,0) FROM payroll WHERE masid = mas_id ),0) AS due", false)
//                    ->add_column('SHOW', '<a href="' . base_url("index.php/superadmin/DriverDetails/$1") . '"><button class="btn btn-success btn-cons" style="min-width: 83px !important;">DETAILS</button></a>
//            <a href="' . base_url("index.php/superadmin/Driver_pay/$1") . '"><button class="btn btn-success btn-cons" style="min-width: 83px !important;">Pay</button>', 'masid')
//                    ->from('master doc', false);
//        }
//        $this->db->order_by('doc.mas_id', 'DESC');
//        echo $this->datatables->generate();
//    }



    function getmap_values() {

        $m = new MongoClient();
        $this->load->library('mongo_db');

        $db = $this->mongo_db->db;

        $bookingid = $this->input->post('mapval');


        $data = $this->mongo_db->get_one('booking_route', array('bid' => (int) $bookingid));

        echo json_encode($data["route"]);
    }

    function payroll_data_form_date($stdate = '', $enddate = '', $company_id = '') {

        $explodeDateTime = explode(' ', date("Y-m-d H:s:i"));
        $explodeDate = explode('-', $explodeDateTime[0]);
        $weekData = $this->week_start_end_by_date(date("Y-m-d H:s:i"));

        $this->load->library('Datatables');
        $this->load->library('table');

        if ($company_id == '0')
            $query = 'a.mas_id = doc.mas_id and DATE(a.appointment_dt) BETWEEN "' . date('Y-m-d', strtotime($stdate)) . '" AND "' . date('Y-m-d', strtotime($enddate)) . '"';
        else
            $query = 'a.mas_id = doc.mas_id and  DATE(a.appointment_dt) BETWEEN "' . date('Y-m-d', strtotime($stdate)) . '" AND "' . date('Y-m-d', strtotime($enddate)) . '" and doc.company_id ="' . $company_id . '"';

        $this->datatables->select('distinct doc.mas_id as masid,doc.first_name,'
                        . "(case  when TRUNCATE((SELECT SUM(mas_earning) FROM appointment WHERE  payment_type = 2 and masid = mas_id and status = 9),2)  IS NULL then '--'  else TRUNCATE((SELECT SUM(mas_earning) FROM appointment WHERE  payment_type = 2 and masid = mas_id and status = 9),2) END) as MAS_CASH_EARNINGS ,"
                        . "(case  when TRUNCATE((SELECT SUM(mas_earning) FROM appointment WHERE  payment_type = 1 and masid = mas_id and status = 9),2)  IS NULL then '--'  else TRUNCATE((SELECT SUM(mas_earning) FROM appointment WHERE  payment_type = 1 and masid = mas_id and status = 9),2) END) as MAS_CARD_EARNINGS ,"
                        . "(case  when TRUNCATE((SELECT SUM(mas_earning) FROM appointment WHERE   masid = mas_id and status = 9),2)  IS NULL then '--'  else TRUNCATE((SELECT SUM(mas_earning) FROM appointment WHERE    masid = mas_id and  status = 9),2) END) as DRIVER_EARNINGS ,"
                        . "(case  when TRUNCATE((SELECT SUM(amount) FROM appointment WHERE   masid = mas_id and payment_type = 2 and status = 9),2)  IS NULL then '--'  else TRUNCATE((SELECT SUM(amount) FROM appointment WHERE    masid = mas_id and payment_type = 2 and status = 9),2) END) as DRIVER_COLLECTED,"
                        . "(SELECT COALESCE( SUM(pay_amount) ,0) FROM payroll WHERE masid = mas_id) as TOTALRECIVED,"
                        . "COALESCE( TRUNCATE((SELECT SUM(mas_earning) FROM appointment WHERE  masid = mas_id  AND  STATUS = 9) - (SELECT SUM(amount) FROM appointment WHERE   masid = mas_id and payment_type = 2 and status = 9) - (SELECT COALESCE( SUM(pay_amount) ,0) FROM payroll WHERE masid = mas_id ),2),0) AS due", false)
                ->add_column('SHOW', '<a href="' . base_url("index.php/superadmin/DriverDetails/$1") . '"><button class="btn btn-success btn-cons" style="min-width: 83px !important;">DETAILS</button></a>
            <a href="' . base_url("index.php/superadmin/Driver_pay/$1") . '"><button class="btn btn-success btn-cons" style="min-width: 83px !important;">Pay</button>', 'masid')
                ->from(' master doc,appointment a ', false)
                ->where($query);


//                $this->db->order_by('ap.appointment_id' ,'DESC');


        echo $this->datatables->generate();
    }

    function addNewDriverData() {
        $datai['first_name'] = $this->input->post('firstname');
        $firstname = $this->input->post('firstname');
        $datai['last_name'] = $this->input->post('lastname');
        $pass = $this->input->post('password');
        $datai['password'] = md5($pass);
        $datai['created_dt'] = $this->input->post('current_dt');
        $datai['type_id'] = 1;
        $datai['status'] = 1;
        $datai['email'] = $this->input->post('email');
        $email = $this->input->post('email');

        $datai['mobile'] = $this->input->post('mobile');
        $datai['zipcode'] = $this->input->post('zipcode');
        $expirationrc = $this->input->post('expirationrc');
//        $expirationPassbook = $this->input->post('expirationPassbook');
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
        $docdetail = array('url' => $carriage_name, 'doctype' => 2, 'driverid' => $newdriverid);
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


        $this->SignupEmail($email, $firstname);

        return true;
    }

    function editdriverdata() {

        $driverid = $this->input->post('driver_id');

        $first_name = $this->input->post('firstname');
        $last_name = $this->input->post('lastname');
        $password = $this->input->post('password');
        $created_dt = date('Y-m-d H:i:s', time());
        $type_id = 1;

        $email = $this->input->post('email');
        $mobile = $this->input->post('mobile');
        $zipcode = $this->input->post('zipcode');
        $expirationrc = $this->input->post('expirationrc');
//                $expirationPassbook = $this->input->post('expirationPassbook');
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

        if ($name != '') {
            $data = $this->db->query("select * from docdetail where driverid = '" . $driverid . "' and doctype = 1");


            if ($data->num_rows() > 0) {

                $docdetail = array('url' => $license_pic, 'expirydate' => date("Y-m-d", strtotime($expirationrc)));
                $this->db->where('driverid', $driverid);
                $this->db->where('doctype', 1);
                $this->db->update('docdetail', $docdetail);
            } else {
                $this->db->insert('docdetail', array('doctype' => 1, 'driverid' => $driverid, 'url' => $license_pic, 'expirydate' => date("Y-m-d", strtotime($expirationrc))));
            }
        }

        if ($carriagecert != '') {
            $data = $this->db->query("select * from docdetail where driverid = '" . $driverid . "' and doctype = 2");

            if ($data->num_rows > 0) {
                $docdet = array('url' => $carriage_name);
                $this->db->where('driverid', $driverid);
                $this->db->where('doctype', 2);
                $this->db->update('docdetail', $docdet);
            } else {
                $this->db->insert('docdetail', array('doctype' => 2, 'driverid' => $driverid, 'url' => $carriage_name, 'expirydate' => '0000-00-00'));
            }
        }
        $this->load->library('mongo_db');

        if ($insurname != '')
            $mongoArr = array("name" => $first_name, "lname" => $last_name, "image" => $profilepic);
        else
            $mongoArr = array("name" => $first_name, "lname" => $last_name);

        $this->mongo_db->update('location', $mongoArr, array('user' => (int) $driverid));

//        $mail = new sendAMail($db1->host);
//        $err = $mail->sendMasWelcomeMail(strtolower($email), ucwords($firstname));


        return true;
    }

    //Get the all the Dispatched jobs by filter by company
    function filter_AllOnGoing_jobs() {

//        $this->load->library('Datatables');
//        $this->load->library('table');
//        $this->load->library('mongo_db');
//
//        if ($this->session->userdata('company_id') != '0')
//            $query = "ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and ap.status not in (9,10,3,4,5,11,12) and d.company_id ='" . $this->session->userdata('company_id') . "'";
//        else
//            $query = "ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and ap.status  not in (9,10,3,4,5,11,12)";
//
//        $this->datatables->select("ap.appointment_id,ap.mas_id,d.first_name,p.slave_id,p.first_name as sname,ap.address_line1,ap.appointment_dt as rec_time,
//              (case ap.status when 1 then 'Request'
//     when 2   then
//    'Driver accepted.'
//     when 3  then
//     'Driver rejected.'
//     when 4  then
//    'Passenger has cancelled.'
//     when 5   then
//    'Driver has cancelled.'
//     when 6   then
//    'Driver is on the way.'
//     when 7  then
//    'Appointment started.'
//     when 8   then
//    'Driver arrived.'
//     when 9   then
//    'Appointment completed.'
//    when 10 then
//    'Appointment timed out.'
//    else
//    'Status Unavailable.'
//    END) as status_result", false)
//                ->edit_column('rec_time', 'getRec_time/$1', 'ap.appointment_id')
//                ->from('appointment ap,master d,slave p')
//                ->where($query);
//
//        $this->db->order_by('ap.appointment_id', 'DESC');
//        echo $this->datatables->generate();


        $city = $this->session->userdata('city_id');
        $company = $this->session->userdata('company_id');
        $query_new = " appointment a,slave p,master m where a.mas_id = m.mas_id and a.slave_id=p.slave_id  and a.status IN (6,7,8) order by a.appointment_id desc LIMIT 200";
        if (($city != '0' && $company != '0'))
            $query_new = '  appointment a,slave p,master m,company_info ci,city_available ca where ca.City_Id = ci.city  and ci.company_id = m.company_id and  m.company_id = "' . $company . '" and a.mas_id = m.mas_id and a.slave_id=p.slave_id  and a.status IN (6,7,8) order by a.appointment_id desc LIMIT 200 ';
        else if ($city != '0')
            $query_new = '  appointment a,slave p,master m,company_info ci,city_available ca where ca.City_Id = "' . $city . '" AND ci.city = "' . $city . '" and ci.company_id = m.company_id and a.mas_id = m.mas_id and a.slave_id=p.slave_id  and a.status IN (6,7,8) order by a.appointment_id desc LIMIT 200 ';

        $query = $this->db->query("select m.first_name,m.last_name,a.mas_id,m.mobile as dphone,a.appointment_id,a.complete_dt,a.appointment_dt,a.B_type,a.address_line1,a.address_line2,a.apprxAmt,a.drop_addr1,a.drop_addr2,a.mas_id,a.slave_id,p.first_name as pessanger_fname,p.last_name as pessanger_lname,p.phone,a.status from " . $query_new)->result();
        echo json_encode(array('aaData' => $query, 'query' => $query_new));
    }

    public function testpush() {

        echo '1';
        $resids = array("4535466yVP5mRysz5hPt7EOvVz45443635462D353542412D344633442D423744382D3132334645433443394644456yVP5mRysz5hPt7EOvVz");
        $this->load->library('PushNotifications');
        // $deviceType = "",$usertype = "",$message = ""
        echo '1';
        $data = $this->PushNotifications->sendPush($resids, 1, 1, 'hi');
        var_dump($data);
        echo '1';
        exit();
    }

    function datatable_onGoing_jobs() {

        $this->load->library('Datatables');
        $this->load->library('table');

        if ($this->session->userdata('company_id') != '0')
            $query = "ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and ap.status not in (9,10,3,4,5,11,12) and d.company_id ='" . $this->session->userdata('company_id') . "'";
        else
            $query = "ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and ap.status not in (9,10,3,4,5,11,12)";
        $this->datatables->select("ap.appointment_id,ap.mas_id,d.first_name,p.slave_id,p.first_name as sname,ap.address_line1,ap.appointment_dt as rec_time,
               (case ap.status when 1 then 'Request'
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
                ->edit_column('rec_time', 'getRec_time/$1', 'ap.appointment_id')
//                 ->add_column('NO. OF DELIVERIES', "get_deliveriescount/$1",'ap.appointment_id')
//                ->add_column('UPDATE STATUS', '<a href="' . base_url("index.php/superadmin/updateStatus_OnGoing_jobs/1/$1").'"  target=""> <button class="btn btn-success btn-cons" style="width:50px">Complete</button></a> <div style="clear: both; height: 5px;">&nbsp;</div><a href="' . base_url("index.php/superadmin/updateStatus_OnGoing_jobs/2/$1").'" target=""> <button class="btn btn-success btn-cons" style="width:50px">Cancel</button></a>', 'ap.appointment_id', 'ap.appointment_id')
//                            ->add_column('UPDATE', '<a href="" target="_blank"> <button class="btn btn-success btn-cons" style="width:50px">Cancel</button></a>', 'ap.appointment_id')
//                ->add_column('JOB DETAILS', '<a href="' . base_url("index.php/superadmin/showJob_details/$1") . '" target="_blank">Show</a>', 'ap.appointment_id')
                ->from('appointment ap,master d,slave p')
                ->where($query);

        $this->db->order_by('ap.appointment_id', 'DESC');
        echo $this->datatables->generate();
    }

    //Get the all completed jobs by filter by company
    function filter_Allcompleted_jobs() {

        $this->load->library('Datatables');
        $this->load->library('table');
        $this->load->library('mongo_db');


        if ($this->session->userdata('company_id') != '0')
            $query = "ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and ap.status = '9' and d.company_id ='" . $this->session->userdata('company_id') . "'";
        else
            $query = "ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and ap.status = '9'";
        $this->datatables->select("ap.appointment_id,ap.mas_id,d.first_name,p.slave_id,p.first_name as sname,ap.address_line1,ap.appointment_dt as rec_time,
               (case ap.status when 1 then 'Request'
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
                ->edit_column('rec_time', 'getRec_time/$1', 'ap.appointment_id')
//                          ->add_column('NO. OF DELIVERIES', "get_deliveriescount/$1",'ap.appointment_id')
//                ->add_column('UPDATE STATUS', '<a href="" target="_blank"> <button class="btn btn-success btn-cons" style="width:50px">Complete</button></a> <div style="clear: both; height: 5px;">&nbsp;</div><a href="" target="_blank"> <button class="btn btn-success btn-cons" style="width:50px">Cancel</button></a>', 'ap.appointment_id', 'ap.appointment_id')
//                            ->add_column('UPDATE', '<a href="" target="_blank"> <button class="btn btn-success btn-cons" style="width:50px">Cancel</button></a>', 'ap.appointment_id')
                ->add_column('JOB DETAILS', '<a href="' . base_url("index.php/superadmin/tripDetails/$1") . '" target="_blank">Show</a>', 'ap.appointment_id')
                ->from('appointment ap,master d,slave p')
                ->where($query);

        $this->db->order_by('ap.appointment_id', 'DESC');
        echo $this->datatables->generate();
    }

    function datatable_completed_jobs() {

        $this->load->library('Datatables');
        $this->load->library('table');

        if ($this->session->userdata('company_id') != '0')
            $query = "ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and ap.status = '9' and d.company_id ='" . $this->session->userdata('company_id') . "'";
        else
            $query = "ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and ap.status = '9' ";

        $this->datatables->select("ap.appointment_id,ap.mas_id,d.first_name,p.slave_id,p.first_name as sname,ap.address_line1,ap.appointment_dt as rec_time,
                (case ap.status when 1 then 'Request'
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
                ->edit_column('rec_time', 'getRec_time/$1', 'ap.appointment_id')
//                 ->add_column('NO. OF DELIVERIES', "get_deliveriescount/$1",'ap.appointment_id')
//                ->add_column('UPDATE STATUS', '<a href="" target="_blank"> <button class="btn btn-success btn-cons" style="width:50px">Complete</button></a> <div style="clear: both; height: 5px;">&nbsp;</div><a href="" target="_blank"> <button class="btn btn-success btn-cons" style="width:50px">Cancel</button></a>', 'ap.appointment_id', 'ap.appointment_id')
//                            ->add_column('UPDATE', '<a href="" target="_blank"> <button class="btn btn-success btn-cons" style="width:50px">Cancel</button></a>', 'ap.appointment_id')
                ->add_column('JOB DETAILS', '<a href="' . base_url("index.php/superadmin/tripDetails/$1") . '" target="_blank">Show</a>', 'ap.appointment_id')
                ->from('appointment ap,master d,slave p')
                ->where($query);

        $this->db->order_by('ap.appointment_id', 'DESC');
        echo $this->datatables->generate();
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
        $vehicleImage = $this->db->query("select Vehicle_Image from workplace where workplace_id = '" . $val[0] . "'")->row_array();
        $return[] = array('doctype' => '99', 'urls' => $vehicleImage['Vehicle_Image'], 'expirydate' => "");

        return $return;
    }

    function uploadimage_diffrent_redulation($file_to_open, $imagename, $servername, $ext) {


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

        $mdpi_file = $servername . 'pics/mdpi/' . $imagename;

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

        $hdpi_file = $servername . 'pics/hdpi/' . $imagename;

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

        $xhdpi_file = $servername . 'pics/xhdpi/' . $imagename;

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

        $xxhdpi_file = $servername . 'pics/xxhdpi/' . $imagename;

        imagejpeg($xxtmp, $xxhdpi_file, 100);
    }

    function AddNewVehicleData() {

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
        $companyname = $this->input->post('company_select'); //$this->session->userdata('LoginId');
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

    function getTransectionData() {
        $this->load->library('Datatables');
        $this->load->library('table');

        if ($this->session->userdata('company_id') == '0')
            $query = 'd.company_id = c.company_id and ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and ap.status = 9 and payment_status in(1,3)';
        else
            $query = 'd.company_id = c.company_id and ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and ap.status = 9 and payment_status in(1,3) and d.company_id = "' . $this->session->userdata('company_id') . '"';

//        $this->datatables->select("ap.appointment_id,d.first_name,ap.mas_id, DATE_FORMAT(ap.appointment_dt, '%b %d %Y %h:%i %p'),round(ap.meter_fee+ap.toll_fee+ap.parking_fee+ap.airport_fee,2),round(ap.amount,2),round(ap.discount,2),round(ap.app_commission,2),round(ap.pg_commission,2),round(ap.pg_commission + ap.discount,2) as app_expences,round(ap.app_owner_pl,2),round(ap.tip_amount,2),round(ap.mas_earning,2),(
        $this->datatables->select("ap.appointment_id,
            d.first_name,
            ap.mas_id, 
            DATE_FORMAT(ap.appointment_dt, '%b %d %Y %h:%i %p'),
           round((ap.amount + ap.tip_amount),2),round(ap.amount - ap.discount,2) ,round(ap.discount,2),
            round(ap.app_commission,2),round(ap.pg_commission,2),
            round(ap.tip_amount,2),round(ap.mas_earning,2),(
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
    'Completed.'
    when 10 then
    'Appointment timed out.'
    else
    'Status Unavailable.'
    END) as status_result, (case ap.payment_type  when 1 then 'card' when 2 then 'cash' END)", false)
                ->add_column('Trip Details', '<a href="' . base_url("index.php/superadmin/tripDetails/$1") . '" target="_blank">Show</a>', 'ap.appointment_id')
                ->add_column('Download', '<a href="' . base_url() . '../../getPDF.php?apntId=$1" target="_blank"> <button class="btn btn-success btn-cons" style="width:50px">Download </button></a>', 'ap.appointment_id')
                ->from('appointment ap,master d,slave p,company_info c')
                ->where($query);

        $this->db->order_by('ap.appointment_id', 'DESC');

//            ->add_column('Actions', '<img src="asdf">', 'City_id')
        echo $this->datatables->generate();
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

        $this->datatables->select("ap.appointment_id,
            d.first_name,
            ap.mas_id, 
            DATE_FORMAT(ap.appointment_dt, '%b %d %Y %h:%i %p'),
            round(ap.amount,2),round(ap.discount,2),
            round(ap.app_commission,2),round(ap.pg_commission,2),
            round(ap.tip_amount,2),round(ap.mas_earning,2),(
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
    'Completed.'
    when 10 then
    'Appointment timed out.'
    else
    'Status Unavailable.'
    END) as status_result, (case ap.payment_type  when 1 then 'card' when 2 then 'cash' END)", false)
                ->add_column('Trip Details', '<a href="' . base_url("index.php/superadmin/tripDetails/$1") . '" target="_blank">Show</a>', 'ap.appointment_id')
                ->add_column('Download', '<a href="' . base_url() . '../getPDF.php?apntId=$1" target="_blank"> <button class="btn btn-success btn-cons" style="width:50px">Download </button></a>', 'ap.appointment_id')
                ->from('appointment ap,master d,slave p,company_info c')
                ->where($query);

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
            $query = 'd.company_id = c.company_id and ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and ap.status = 9 and payment_status in(1,3) and ap.payment_type = "' . $selectdval . '" and d.company_id="' . $this->session->userdata('company_id') . '"';
        } else if ($selectdval == '0' && $this->session->userdata('company_id') == '0') {
            $query = 'd.company_id = c.company_id and ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and ap.status = 9 and payment_status in(1,3)';
        } else if ($selectdval != '0' && $this->session->userdata('company_id') == '0') {
            $query = 'd.company_id = c.company_id and ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and ap.status = 9 and payment_status in(1,3) and ap.payment_type = "' . $selectdval . '" ';
        } else {
            $query = 'd.company_id = c.company_id and ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and ap.status = 9 and payment_status in(1,3) and d.company_id="' . $this->session->userdata('company_id') . '"';
        }


        $this->datatables->select("ap.appointment_id,
            d.first_name,
            ap.mas_id, 
            DATE_FORMAT(ap.appointment_dt, '%b %d %Y %h:%i %p'),
            round(ap.amount,2),round(ap.discount,2),
            round(ap.app_commission,2),round(ap.pg_commission,2),
            round(ap.tip_amount,2),round(ap.mas_earning,2),(
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
    'Completed.'
    when 10 then
    'Appointment timed out.'
    else
    'Status Unavailable.'
    END) as status_result, (case ap.payment_type  when 1 then 'card' when 2 then 'cash' END)", false)
                ->add_column('Trip Details', '<a href="' . base_url("index.php/superadmin/tripDetails/$1") . '" target="_blank">Show</a>', 'ap.appointment_id')
                ->add_column('Download', '<a href="' . base_url() . '../getPDF.php?apntId=$1" target="_blank"> <button class="btn btn-success btn-cons" style="width:50px">Download </button></a>', 'ap.appointment_id')
                ->from('appointment ap,master d,slave p,company_info c')
                ->where($query);

        $this->db->order_by('ap.appointment_id', 'DESC');

//            ->add_column('Actions', '<img src="asdf">', 'City_id')
        echo $this->datatables->generate();
    }

    function passenger_rating() {
        $status = 1;
        $query = $this->db->query(" SELECT p.slave_id, p.first_name ,p.email,(select avg(rating) from passenger_rating where slave_id = p.slave_id) as rating FROM passenger_rating r, slave p WHERE r.slave_id = p.slave_id  AND r.status ='" . $status . "'")->result();
        return $query;
    }

    function driver_review($status) {


        $query = $this->db->query(" SELECT r.review, r.status,r.star_rating, r.review_dt,r.appointment_id, r.mas_id, d.first_name AS mastername, p. slave_id,a.appointment_dt  FROM master_ratings r, master d, slave p,appointment a WHERE r.slave_id = p.slave_id  AND r.mas_id = d.mas_id  AND r.status ='" . $status . "' AND r.review <>'' AND a.appointment_id = r.appointment_id ")->result();
        return $query;
    }

    function DriverDetails($mas_id = '') {
//        $query = $this->db->query("select ap.appointment_dt,ap.appointment_id,ap.inv_id,ap.txn_id as tr_id,ap.status,ap.amount,d.email as mas_email,d.first_name as mas_fname,d.last_name as mas_lname,p.email as slv_email,p.first_name as slv_fname,p.last_name as slv_lname,c.company_id from appointment ap,master d,slave p,company_info c where d.company_id = c.company_id and ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and d.mas_id ='" . $mas_id . "'  order by ap.appointment_id DESC")->result(); //get_where('slave', array('email' => $email, 'password' => $password));
//        return $query;

        $this->load->library('Datatables');
        $this->load->library('table');

        if ($this->session->userdata('company_id') != '0')
            $query = "ap.status = 9 and ap.payment_status in(1,3) and p.slave_id = ap.slave_id and ap.mas_id = doc.mas_id and ap.mas_id='" . $mas_id . "' and  doc.company_id ='" . $this->session->userdata('company_id') . "'";
        else
            $query = 'ap.status = 9 and ap.payment_status in(1,3) and p.slave_id = ap.slave_id and ap.mas_id = doc.mas_id and ap.mas_id="' . $mas_id . '"';

        $this->datatables->select('ap.appointment_id,p.first_name,ap.amount, truncate(ap.app_owner_pl,2), ap.pg_commission,ap.tip_amount,ap.mas_earning', false)
                ->from(' master doc,appointment ap,slave p', false)
                ->where($query);


//                $this->db->order_by('ap.appointment_id' ,'DESC');


        echo $this->datatables->generate();
    }

    function DriverDetails_form_Date($stdate = '', $enddate = '', $company_id = '', $mas_id = '') {

        $this->load->library('Datatables');
        $this->load->library('table');

        if ($company_id == '0')
            $query = 'ap.status = 9 and ap.payment_status in(1,3) and p.slave_id = ap.slave_id and ap.mas_id="' . $mas_id . '" and DATE(ap.appointment_dt) BETWEEN "' . date('Y-m-d', strtotime($stdate)) . '" AND "' . date('Y-m-d', strtotime($enddate)) . '" and ap.mas_id=doc.mas_id';
        else
            $query = 'ap.status = 9 and ap.payment_status in(1,3) and p.slave_id = ap.slave_id and ap.mas_id="' . $mas_id . '" and DATE(ap.appointment_dt) BETWEEN "' . date('Y-m-d', strtotime($stdate)) . '" AND "' . date('Y-m-d', strtotime($enddate)) . '" and doc.company_id ="' . $company_id . '" and ap.mas_id=doc.mas_id';

        $this->datatables->select('ap.appointment_id,p.first_name,ap.amount, ap.app_commission, ap.pg_commission,ap.mas_earning', false)
                ->from(' master doc,appointment ap,slave p', false)
                ->where($query);


//                $this->db->order_by('ap.appointment_id' ,'DESC');


        echo $this->datatables->generate();
    }

    function inactivedriver_review() {
        $val = $this->input->post('val');

        foreach ($val as $row) {
            $values = explode(",", $row);
            $query = $this->db->query("update master_ratings set status = 2 where appointment_id= '" . $row . "'");
        }
    }

    function activedriver_review() {
        $val = $this->input->post('val');

        foreach ($val as $row) {
            $values = explode(",", $row);
            $query = $this->db->query("update master_ratings set status=1 where  appointment_id= '" . $row . "'");
        }
    }

    function get_Drivers_from_mongo($status) {

        $m = new MongoClient();
        $this->load->library('mongo_db');

        $db = $this->mongo_db->db;

        $selecttb = $db->location;
        $darray = array();
        if ($status == 3) { //online or free
            $drivers = $selecttb->find(array('status' => (int) $status));

            foreach ($drivers as $mas_id) {
                $darray[] = $mas_id['user'];
            }
        } elseif ($status == 567) {//booked
            $drivers = $selecttb->find(array('status' => array('$in' => array(5, 6, 7))));
            foreach ($drivers as $mas_id) {
                $darray[] = $mas_id['user'];
            }
        } elseif ($status == 30) {//OFFLINE
            $drivers = $selecttb->find(array('status' => (int) 4));
            foreach ($drivers as $mas_id) {
                $darray[] = $mas_id['user'];
            }
        }

        $mas_ids = implode(', ', $darray);

        $quaery = $this->db->query("SELECT mas.mas_id, mas.first_name ,mas.zipcode, mas.profile_pic, mas.last_name, mas.email, mas.mobile, mas.status,mas.created_dt,(select type from user_sessions where oid = mas.mas_id order by oid DESC limit 0,1) as dev_type FROM master mas where  mas.mas_id IN (" . $mas_ids . ")  order by mas.mas_id DESC")->result();
        return $quaery;

//        print_r($mas_ids);
    }

    function getDtiverDetail() {

        $did = $this->input->post("did");

        $queryM = $this->db->query("select * from master where mas_id ='" . $did . "'")->result();
        $queryV = $this->db->query("select w.Title,w.Vehicle_Model,vm.vehiclemodel,vt.vehicletype from master m,vehicleType vt,vehiclemodel vm,workplace w where m.mas_id='" . $did . "' and m.workplace_id=w.workplace_id and w.Title =vt.id and w.Vehicle_Model = vm.id")->result();
        $queryapp = $this->db->query("select appointment_id,appointment_dt,address_line1,drop_addr1 from appointment  where mas_id='" . $did . "' and status  in(1,2,6,7,8)")->result();


        foreach ($queryM as $master) {
            $name = $master->first_name . $master->last_name;
            $mobile = $master->mobile;
            $license = $master->license_num;
            $profile = $master->profile_pic;
        }
        foreach ($queryV as $vehicle) {
            $vtype = $vehicle->vehicletype;
            $vmodel = $vehicle->vehiclemodel;
        }

        if ($profile) {
            $img = ServiceLink . '/pics/' . $profile;
        } else {
            $img = ServiceLink . '/pics/aa_default_profile_pic.gif';
        }
        $html = '<div id="quickview" class="quickview-wrapper open" data-pages="quickview" style="max-height: 487px;margin-top: 39px;">

<ul class="nav nav-tabs" style="padding: 0 14px;">
    <a data-view-animation="push-parrallax" data-view-port="#chat" data-navigate="view" class="" href="#">
                                                                 <span class="col-xs-height col-middle">
                                                                <span class="thumbnail-wrapper d32 circular bg-success">
                                                                    <img width="34" height="34" alt="" data-src-retina="' . $img . '" data-src="' . $img . '" src="' . $img . '" class="col-top">
                                                                </span>
                                                                </span>
        <p class="p-l-20 col-xs-height col-middle col-xs-12">
            <span class="text-master" style="color: #ffffff !important;">' . $name . '</span>
            <span class="block text-master hint-text fs-12" style="color: #ffffff !important;">+91' . $mobile . '</span>
        </p>
    </a>


</ul>
<p class="close_quick"> <a class="btn-link quickview-toggle"><i class="pg-close" style="color: #ffffff ! important;" ></i></a></p>

<div class="tab-content" style="top: 21px !important;">


<div class="list-view-group-container" >

<ul>

<li class="chat-user-list clearfix">
        <div class="form-control">
            <label class="col-sm-5 control-label">Model</label><label class="col-sm-7 control-label">' . $vmodel . '</label>
        </div>

    </li>
    <li class="chat-user-list clearfix">

        <div class="form-control">
            <label class="col-sm-5 control-label">Car Type</label><label class="col-sm-7 control-label">' . $vtype . '</label>
        </div>


    </li>

    <li class="chat-user-list clearfix">

        <div class="form-control">
            <label class="col-sm-5 control-label">License no</label><label class="col-sm-7 control-label">' . $license . '</label>
        </div>

    </li>


</ul>


<div class="list-view-group-container" style="overflow-y: scroll;max-height: 314px;">
<div class="list-view-group-header text-uppercase" style="background-color: #f0f0f0;padding: 10px;">
            ASSIGNED JOBS</div>';
        foreach ($queryapp as $result) {

            $html.='<div style="overflow: auto;background: #fff;">
    <ul style="margin-top: 15px;">

        <li class="chat-user-list clearfix">


            <div class="item share share-self col1" data-social="item" style="border: 2px solid #e5e8e9;">
                <div class="pull-right" style="margin: 5px 5px 0px 11px;width: 157px;">
                ' . date("M d Y g:i A", strtotime($result->appointment_dt)) . '

            </div>
                <div class="item-header clearfix" style="margin: 5px 8px 11px 12px;">

                ' . $result->appointment_id . '

            </div>
                <div class="item-description" style="">

                    <ul>

                        <li class="chat-user-list clearfix">


                             <div class=""  style="border: 1px solid rgba(0, 0, 0, 0.07);">
                             <p style="padding: 8px;">' . $result->address_line1 . '</p>


                            </div>


                        </li>
                        <li class="chat-user-list clearfix">



                        <div class="" style="border: 1px solid rgba(0, 0, 0, 0.07);">
                             <p style="padding: 8px;">' . $result->drop_addr1 . '</p>


                            </div>
                        </li>

                    </ul>
                </div>
            </div>



        </li>


    </ul>

</div>';
        }




        $html.='</div></div></div></div>';


        echo json_encode(array('html' => $html));
    }

//    function Driver_pay($masid = '') {
//
////      $query = "select * from payroll wehre company_id='".$this->session->userdata('LoginId')."'";
//
//        $query = "select sum(a.amount) as total,m.first_name from appointment a,master m where a.mas_id = '" . $masid . "' and a.mas_id = m.mas_id and a.status = 9 and (a.cancel_status not in(1,2,7) or a.cancel_status is null)";
//        return $this->db->query($query)->result();
//    }



    function Driver_pay($masid = '') {

//      $query = "select * from payroll wehre company_id='".$this->session->userdata('LoginId')."'";

        $query = "select COALESCE( TRUNCATE(((SELECT COALESCE(SUM(mas_earning),0) FROM appointment WHERE  m.mas_id  = mas_id  AND  STATUS = 9 and payment_type = 1)) - (SELECT COALESCE(SUM(mas_earning),0) FROM appointment WHERE   m.mas_id  = mas_id and payment_type = 2 and status = 9) - (SELECT COALESCE( SUM(pay_amount) ,0) FROM payroll WHERE m.mas_id  = mas_id ),2),0) as total,m.first_name,"
                . "(select count(settled_flag) from appointment where settled_flag = 0 and mas_id = a.mas_id and mas_earning != 0 and status = 9 and payment_status IN (1,3)) as unsettled_amount_count,"
                . "(select appointment_id from appointment where settled_flag = 0 and mas_id = a.mas_id and status = 9 and payment_status IN (1,3) order by appointment_id DESC limit 0,1) as last_unsettled_appointment_id from appointment a,master m where a.mas_id = '" . $masid . "' and a.mas_id = m.mas_id and settled_flag = 0 and a.status = 9 and a.payment_status in (1,3)";
        return $this->db->query($query)->result();
    }

//    function get_payrolldata($id = '') {
//        $quaery = $this->db->query("SELECT * from payroll WHERE  mas_id = '" . $id . "'")->result();
////        $quaery = $this->db->query("SELECT due_amount,closing_balance,pay_date,pay_date,opening_balance,mas_id,trasaction_id,payroll_id,sum(pay_amount) as totalpaid from payroll  WHERE  mas_id = '" . $id . "'")->result();
//        return $quaery;
//    }
//
//    function Totalamountpaid($id = '') {
//        $quaery = $this->db->query("SELECT sum(pay_amount) as totalamt from payroll WHERE  mas_id = '" . $id . "'")->result();
////        $quaery = $this->db->query("SELECT due_amount,closing_balance,pay_date,pay_date,opening_balance,mas_id,trasaction_id,payroll_id,sum(pay_amount) as totalpaid from payroll  WHERE  mas_id = '" . $id . "'")->result();
//        return $quaery;
//    }

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

    function get_all_data($stdate, $enddate) {

        if ($stdate || $enddate) {
            $query = $this->db->query("select ap.drop_long,ap.complete_dt,ap.coupon_code,ap.discount,ap.tip_amount,ap.airport_fee,ap.toll_fee,ap.parking_fee,ap.arrive_dt,ap.waiting_mts,ap.drop_lat,ap.drop_addr1,ap.drop_addr2, ap.start_dt,ap.distance_in_mts,ap.appt_long,ap.appt_lat,ap.address_line1,ap.address_line2,ap.appointment_dt as appointment_date,ap.appointment_id as Bookin_Id,ap.inv_id,ap.txn_id as tr_id,ap.status,ap.amount,d.email as Driver_email,d.first_name as Driver_First_Name,d.last_name as Driver_Last_Name,p.email as Passenger_email,p.first_name as Passenger_fname,p.last_name as Passenger_lname,c.company_id from appointment ap,master d,slave p,company_info c where d.company_id = c.company_id and ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and DATE(ap.appointment_dt) BETWEEN '" . date('Y-m-d', strtotime($stdate)) . "' AND '" . date('Y-m-d', strtotime($enddate)) . "' order by ap.appointment_id DESC");
        } else {
            $query = $this->db->query("select ap.drop_long,ap.complete_dt,ap.coupon_code,ap.discount,ap.tip_amount,ap.airport_fee,ap.toll_fee,ap.parking_fee,ap.arrive_dt,ap.waiting_mts,ap.drop_lat,ap.drop_addr1,ap.drop_addr2, ap.start_dt,ap.distance_in_mts,ap.appt_long,ap.appt_lat,ap.address_line1,ap.address_line2,ap.appointment_dt as appointment_date,ap.appointment_id as Bookin_Id,ap.inv_id,ap.txn_id as tr_id,ap.status,ap.amount,d.email as Driver_email,d.first_name as Driver_First_Name,d.last_name as Driver_Last_Name,p.email as Passenger_email,p.first_name as Passenger_fname,p.last_name as Passenger_lname,c.company_id from appointment ap,master d,slave p ,company_info c where d.company_id = c.company_id and ap.mas_id = d.mas_id and ap.slave_id = p.slave_id order by ap.appointment_id DESC");
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
        $query = $this->db->query("select ap.appointment_dt,ap.appointment_id,ap.inv_id,ap.txn_id as tr_id,ap.status,ap.amount,d.email as mas_email,d.first_name as mas_fname,d.last_name as mas_lname,p.email as slv_email,p.first_name as slv_fname,p.last_name as slv_lname,c.company_id from appointment ap,master d,slave p,company_info c where c.company_id = d.company_id and ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and DATE(ap.appointment_dt) BETWEEN '" . date('Y-m-d', strtotime($stdate)) . "' AND '" . date('Y-m-d', strtotime($enddate)) . "' order by ap.appointment_id DESC LIMIT 200")->result(); //get_where('slave', array('email' => $email, 'password' => $password));
        return $query;
    }

    function getuserinfo() {
        $query = $this->db->query("select * from company_info  ")->row();
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

    function Vehicles($status = '') {
        $quaery = $this->db->query("select w.workplace_id,w.uniq_identity,w.Title,w.Vehicle_Model,w.type_id,w.Vehicle_Reg_No,w.License_Plate_No,w.Vehicle_Insurance_No,w.Vehicle_Color,vt.vehicletype,vm.vehiclemodel,wt.type_id,wt.type_name,ci.companyname FROM workplace w,vehicleType vt,vehiclemodel vm,workplace_types wt,company_info ci where vt.id=w.title and w.company = ci.company_id and vm.id=w.Vehicle_Model and wt.type_id =w.type_id  and w.status ='" . $status . "' order by w.workplace_id desc")->result();
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

    function getRediousPrice() {

        $this->load->library('mongo_db');
        $data = array();
        if ($this->session->userdata('city_id') != 0) {

            $data = $this->mongo_db->get_where('RediousPrice', array('cityid' => $this->session->userdata('city_id')));
        } else {

            $data = $this->mongo_db->get('RediousPrice');
        }
        $senddata = array();
        foreach ($data as $res)
            $senddata[] = $res;

        return $senddata;
    }

    public function senPushToDriver($driversArrIos, $driversArrAndroid, $message, $city_id, $usertype, $User_ids, $query = '') {


        $driversArrIos1 = array_values(array_filter(array_unique($driversArrIos)));
        $driversArrAndroid1 = array_values(array_filter(array_unique($driversArrAndroid)));

        $amazon = new AwsPush();
        $pushReturn = array();
        foreach ($driversArrIos1 as $endpointArn)
            $pushReturn[] = $amazon->publishJson(array(
                'MessageStructure' => 'json',
                'TargetArn' => $endpointArn,
                'Message' => json_encode(array(
                    'APNS' => json_encode(array(
                        'aps' => array(
                            'alert' => $message,
                            'nt' => 420
                        )
                    ))
                )),
            ));


        $count = count($driversArrAndroid1) + count($driversArrIos1);

        if ($query != '') {

            $data = $this->db->query($query)->result();
            foreach ($data as $res)
                $driversArrAndroid[] = $res->push_token;
        }


        $fields = array(
            'registration_ids' => $driversArrAndroid1,
            'data' => array('payload' => $message, 'action' => 420),
        );

        if ($usertype == 1)
            $apiKey = ANDROID_DRIVER_PUSH_KEY;
        else if ($usertype == 2)
            $apiKey = ANDROID_PASSENGER_PUSH_KEY;

        $headers = array(
            'Authorization: key=' . $apiKey,
            'Content-Type: application/json'
        );
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'http://android.googleapis.com/gcm/send');

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        $result = curl_exec($ch);

        curl_close($ch);
        $res_dec = json_decode($result);



        if ($res_dec->success >= 1) {

//            $query = "insert into DriverNotification(city,message,date,NumOfDriver,user_type)  values('" . $citylatlon . "','" . $message . "',now(),'" . count($driversArrAndroid) . "','" . $usertype . "')";
//            $this->db->query($query);
            $this->load->library('mongo_db');

            $insertArr = array('user_type' => (int) $usertype, 'DateTime' => date('Y-m-d H:i:s'), 'msg' => $message, 'city' => $city_id, 'user_ids' => $User_ids);
            $lastid = $this->mongo_db->insert('AdminNotifications', $insertArr);


            return array('errorNo' => 44, 'result' => $driversArrAndroid1, 'count' => count($driversArrAndroid1), 'test1' => $pushReturn[0]['MessageId'], 'test' => $query);
        } else
            return array('errorNo' => 46, 'result' => $driversArrAndroid1, 'test' => $result);
    }

    function addRediousPrice($from, $to, $price, $cityid) {

        $this->load->library('mongo_db');

        $dbins = $this->mongo_db->db;
        $dbsname = $dbins->selectCollection('RediousPrice');
        $data = $dbsname->findOne(
                array(
                    'cityid' => $cityid,
                    '$or' => array(
                        array('$and' =>
                            array(
                                array('from_' =>
                                    array('$gte' => (int) $from)
                                ),
                                array('from_' => array('$lte' => (int) $to))
                            )
                        ),
                        array('$and' =>
                            array(
                                array('to_' =>
                                    array('$lte' => (int) $to)
                                ),
                                array('to_' =>
                                    array('$gt' => (int) $from)
                                )
                            )
                        )
                    )
                )
        );
//        foreach ($cursor as $r){
//            $data[]  = $r;
//        }
        if (!empty($data)) {
            echo json_encode(array('flag' => 1, 'error' => 'This redious is already defined.'));
        } else {
            $insertArr = array('from_' => (int) $from, 'to_' => (int) $to, 'price' => (int) $price, 'cityid' => $cityid);
            $lastid = $this->mongo_db->insert('RediousPrice', $insertArr);
            echo json_encode(array('flag' => 0, 'error' => 'Redious price has been added.', 'mid' => (string) $lastid));
        }
    }

    function editRediousPrice($from, $to, $price, $mongoId, $cityid) {

        $this->load->library('mongo_db');


//         $dbins = $this->mongo_db->db;
//        $dbsname = $dbins->selectCollection('RediousPrice');
//        $data = $dbsname->findOne(
//                array(
//                    'cityid' => array('$ne' => $cityid),
//                    '$or' => array(
//                        array('$and' =>
//                            array(
//                                array('from_' =>
//                                    array('$gte' => (int) $from)
//                                ),
//                                array('from_' => array('$lte' => (int) $to))
//                            )
//                        ),
//                        array('$and' =>
//                            array(
//                                array('to_' =>
//                                    array('$lte' => (int) $to)
//                                ),
//                                array('to_' =>
//                                    array('$gt' => (int) $from)
//                                )
//                            )
//                        )
//                    )
//                )
//        );




        $update = array('from_' => (int) $from, 'to_' => (int) $to, 'price' => (int) $price, "cityid" => $cityid);
        $respon = $this->mongo_db->update('RediousPrice', $update, array('_id' => new MongoId($mongoId)));
        if ($respon == TRUE)
            echo json_encode(array('flag' => 0, 'error' => 'Redious price has been Updated.'));
        else {
            echo json_encode(array('flag' => 1, 'error' => 'Not Updated.'));
        }
    }

    function DeleteRediousPrice($mid) {
        $this->load->library('mongo_db');
        $respon = $this->mongo_db->delete('RediousPrice', array('_id' => new MongoId($mid)));
        echo json_encode(array('flag' => 0, 'error' => 'Deleted Succesfully.', 'rest' => $respon));
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
        $todayone = $this->db->query("SELECT a.appointment_id,m.mas_id,c.company_id FROM appointment a,master m,company_info c  WHERE a.mas_id = m.mas_id and m.company_id = c.company_id  and a.appointment_dt like '" . date('Y-m-d') . "%' and a.status = 9 ");
//        $today
        //this week completed booking
        $weekArr = $this->week_start_end_by_date($currTime);
        $week = $this->db->query("SELECT  a.appointment_id,m.mas_id,c.company_id FROM appointment a,master m,company_info c  WHERE a.mas_id = m.mas_id and m.company_id = c.company_id  and a.status = 9 and DATE(a.appointment_dt) >= '" . $weekArr['first_day_of_week'] . "'");


        // this month completed booking

        $currMonth = date('n', $currTime);
        $month = $this->db->query("SELECT a.appointment_id,m.mas_id,c.company_id FROM appointment a,master m,company_info c  WHERE a.mas_id = m.mas_id and m.company_id = c.company_id  and a.status = 9  and  MONTH(a.appointment_dt) = '" . $currMonth . "' ");


        // lifetime completed booking
        $lifetime = $this->db->query("SELECT a.appointment_id,m.mas_id,c.company_id FROM appointment a,master m,company_info c  WHERE a.mas_id = m.mas_id and m.company_id = c.company_id  and a.status = 9 ");

        // total booking uptodate
        $totaluptodate = $this->db->query("SELECT  a.appointment_id,m.mas_id,c.company_id FROM appointment a,master m,company_info c  WHERE a.mas_id = m.mas_id and m.company_id = c.company_id ");



        //today earnings
//
        $todayearning = $this->db->query("SELECT sum(a.amount) as totamount,a.appointment_id,m.mas_id,c.company_id FROM appointment a,master m,company_info c  WHERE a.mas_id = m.mas_id and m.company_id = c.company_id  and a.appointment_dt  like '" . date('Y-m-d') . "%' and a.status = 9 ");

//
//
//        //this week completed booking
//
        $weekearning = $this->db->query("SELECT sum(a.amount) as totamount,a.appointment_id,m.mas_id,c.company_id FROM appointment a,master m,company_info c  WHERE a.mas_id = m.mas_id and m.company_id = c.company_id  and a.status = 9 and DATE(a.appointment_dt) >= '" . $weekArr['first_day_of_week'] . "'");
//
//
//        // this month completed booking
//
//
        $monthearning = $this->db->query("SELECT sum(a.amount) as totamount,a.appointment_id,m.mas_id,c.company_id FROM appointment a,master m,company_info c  WHERE a.mas_id = m.mas_id and m.company_id = c.company_id  and a.status = 9  and  MONTH(a.appointment_dt) = '" . $currMonth . "' ");
//
//
//        // lifetime completed booking
        $lifetimeearning = $this->db->query("SELECT sum(a.amount) as totamount, a.appointment_id,m.mas_id,c.company_id FROM appointment a,master m,company_info c  WHERE a.mas_id = m.mas_id and m.company_id = c.company_id  and a.status = 9 ");
//
//        // total booking uptodate
        $totaluptodateearning = $this->db->query("SELECT  sum(a.amount) as totalearning, a.appointment_id,m.mas_id,c.company_id FROM appointment a,master m,company_info c  WHERE a.mas_id = m.mas_id and m.company_id = c.company_id ");


        $t = $todayearning->row();
        $w = $weekearning->row();
        $m = $monthearning->row();
        $l = $lifetimeearning->row();
        $te = $totaluptodateearning->row();


        $data = array('today' => $todayone->num_rows(), 'week' => $week->num_rows(), 'month' => $month->num_rows(), 'lifetime' => $lifetime->num_rows(), 'total' => $totaluptodate->num_rows(),
            'todayearning' => (float) (($t->totamount - ($t->totamount * (10 / 100)) - (float) (($t->totamount * (2.9 / 100)) + 0.3))), 'weekearning' => (float) (($w->totamount - ($w->totamount * (10 / 100)) - (float) (($w->totamount * (2.9 / 100)) + 0.3))), 'monthearning' => (float) (($m->totamount - ($m->totamount * (10 / 100)) - (float) (($m->totamount * (2.9 / 100)) + 0.3))), 'lifetimeearning' => (float) (($l->totamount - ($l->totamount * (10 / 100)) - (float) (($l->totamount * (2.9 / 100)) + 0.3))), 'totalearning' => $te->totalearning
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

    function issessionset() {

        if ($this->session->userdata('emailid') && $this->session->userdata('password')) {

            return true;
        }
        return false;
    }

}

?>
