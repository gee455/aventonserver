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
        $password = md5($this->input->post("password"));

        $queryforslave = $this->db->get_where('superadmin', array('username' => $email, 'password' => $password));
        $res = $queryforslave->row();

        if ($queryforslave->num_rows > 0) {
            $tablename = 'company_info';
            $LoginId = 'company_id';
            $sessiondata = $this->setsessiondata($tablename, $LoginId, $res, $email, $password);
            $this->session->set_userdata($sessiondata);
            return true;
        }

        return false;
    }

    //* naveena models *//


    function dt_passenger($status = '') {

        $this->load->library('Datatables');
        $this->load->library('table');



        $this->datatables->select("s.slave_id as rahul,s.first_name,s.last_name,s.phone,s.email,s.created_dt,s.profile_pic,"
                        . "(select (case type when 2 then 'android_icon.png' when 1 then 'iphone-logo.png' END) from user_sessions where oid = rahul and user_type = 2 order by oid DESC limit 0,1) as dtype", FALSE)
                ->unset_column('dtype')
                ->unset_column('s.profile_pic')
                ->add_column('PROFILE PIC', '<img src="' . base_url() . '../../pics/$1" width="50px">', 's.profile_pic')
                ->add_column('DEVICE TYPE', '<img src="' . base_url() . '../../admin/assets/$1" width="50px" >', 'dtype')
                ->add_column('select', '<input type="checkbox" class="checkbox" name="checkbox" value= "$1"/>', 'rahul')
                ->from('slave s')
                ->where('s.status', $status);
        $this->db->order_by("rahul", "desc");

        echo $this->datatables->generate();
    }

    function addcountry() {
        $query = $this->db->query("select * from country where Country_Name= '" . $this->input->post('data2') . "'");
        if ($query->num_rows() > 0) {
            echo json_encode(array('msg' => "country already exists", 'flag' => 0));
            return;
        } else {


            $data2 = $this->input->post('data2');
            $this->db->query("insert into country(Country_Name)  values('" . $data2 . "')");

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
                echo json_encode(array("msg" => "your selected cities deleted successfully", "flag" => 0));
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

    function insert_payment($mas_id = '') {
        $currunEarnigs = $this->input->post('currunEarnigs');
        $amoutpaid = $this->input->post('paid_amount');
        $curuntdate = $this->input->post('ctime');
        $closingamt = $currunEarnigs - $amoutpaid;

        $query = "insert into payroll(mas_id,opening_balance,pay_date,pay_amount,closing_balance,due_amount) VALUES (
        '" . $mas_id . "',
        '" . $currunEarnigs . "',
        '" . $curuntdate . "',
        '" . $amoutpaid . "',
        '" . $closingamt . "','" . $closingamt . "')";
        $this->db->query($query);
//        echo $query;
//        exit();
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
            $this->db->query("update workplace set status=4  where workplace_id='" . $result . "'");
        }
        if ($this->db->affected_rows() > 0) {
            echo json_encode(array('msg' => "your selected vehicle/vehicles rejected succesfully", 'flag' => 1));
            return;
        }
    }

    function acceptdrivers() {
        $val = $this->input->post('val');
        $company_id = $this->input->post('company_id');
        foreach ($val as $result) {
            $this->db->query("update master set status = 3 , company_id = '" . $company_id . "'  where mas_id='" . $result . "' ");
        }
        if ($this->db->affected_rows() > 0) {
            echo json_encode(array('msg' => "your selected driver/drivers accepted succesfully", 'flag' => 1));
            return;
        }
    }

    function rejectdrivers() {
        $val = $this->input->post('val');
//        error_reporting(E_ALL);
//        include_once(base_url().'application/views/company/aws.phar');
//        include_once(base_url().'application/views/company/AwsPush.php');
        $getTokensQry = $this->db->query("select u.*,m.workplace_id from user_sessions u,master m where u.oid IN (" . implode(',', $val) . ") and u.loggedIn = 1 and u.user_type = 1 and m.mas_id = u.oid")->result();
        $this->load->library('mongo_db');
        foreach ($getTokensQry as $token) {
            if ($token->type == '2') {
                $res [] = $this->sendAndroidPush(array($token->push_token), array('action' => 12, 'payload' => 'Your profile has been suspended on OPA, contact OPA customer care'), 'AIzaSyBK7MVDQ-jm8GAd3BjmF0w2Z1_BjZ_qszA');
            } else {
//               $amazon = new AwsPush();
//               $pushReturn2 = $amazon->publishJson(array(
//                   'MessageStructure' => 'json',
//                   'TargetArn' => $token->push_token,
//                   'Message' => json_encode(array(
//                       'APNS' => json_encode(array(
//                           'aps' => array('alert'=>'Rejected')
//                       ))
//                   )),
//               ));
//
//               if ($pushReturn2[0]['MessageId'] == '')
//                   $ret[] = array('errorNo' => 44);
//               else
//                   $ret[] = array('errorNo' => 46);
            }
            $this->db->query("update master set status=4   where mas_id='" . $token->oid . "'");
            if ($token->workplace_id != 0) {
                $this->db->query("update workplace set status=2   where workplace_id='" . $token->workplace_id . "'");
                $this->mongo_db->update("location", array('carId' => 0), array('user' => (int) $token->oid));
            }
        }

        if ($this->db->affected_rows() > 0) {
            echo json_encode(array('msg' => "your selected driver/drivers rejected Successfully", 'flag' => 1, 'res' => $res));
            return;
        }
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

        if ($pass == $newpass) {
            echo json_encode(array('msg' => "this password already exists. Enter new password", 'flag' => 1));
            return;
        } else {
            $this->db->query("update master set password='" . $newpass . "' where mas_id = '" . $val . "' ");

            if ($this->db->affected_rows() > 0) {
                echo json_encode(array('msg' => "your new password updated successfully", 'flag' => 0));
                return;
            }
        }
    }

    function editvehicle($status) {

        $data['vehicle'] = $this->db->query("select w.*,wt.city_id,v.id,v.vehiclemodel from  workplace w ,workplace_types wt,vehiclemodel v where workplace_id='" . $status . "' and w.type_id = wt.type_id and v.id = w.Vehicle_Model ")->result();

        $cityId = $data['vehicle'][0]->city_id;

        if ($cityId == '')
            return array('flag' => 1);

        $data['company'] = $this->db->query("select companyname,company_id from company_info where city = '" . $cityId . "'")->result();

        $data['cityList'] = $this->db->query("select City_Name,City_Id from city_available")->result();

        $data['workplaceTypes'] = $this->db->query("select * from workplace_types where city_id = '" . $cityId . "'")->result();

        $data['vehicleTypes'] = $this->db->query("select * from vehicleType")->result();




        $data['vehicleDoc'] = $this->db->query("select * from vechiledoc where vechileid = '" . $status . "'")->result();

        $this->load->library('mongo_db');


//        print_r($data);
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
        
        $this->mongo_db->update('location',array('status' => 4),array('user' => (int) $driverid));
        
    }

    function insert_vehicletype() {
        $vehicletype = $this->input->post('vehicletype');
        $seating = $this->input->post('seating');
        $minimumfare = $this->input->post('minimumfare');
        $basefare = $this->input->post('basefare');
        $priceperminute = $this->input->post('priceperminute');
        $priceperkm = $this->input->post('priceperkm');
        $discription = $this->input->post('discription');
        $city = $this->input->post('city');

        $resulrt = $this->db->query("insert into workplace_types(type_name,max_size,basefare,min_fare,price_per_min,
                     price_per_km,type_desc,city_id) values('" . $vehicletype . "',

                                                                    '" . $seating . "',

                                                                        '" . $basefare . "',
                                                                            '" . $minimumfare . "',
                                                                                '" . $priceperminute . "',
                                                                                    '" . $priceperkm . "',
                                                                                        '" . $discription . "',
                                                                                            '" . $city . "')");

        $type_id = $this->db->insert_id();




        $this->load->database();
        $cityData = $this->db->query("select * from city_available where city_id =  '" . $city . "'")->row_array();




        $this->load->library('mongo_db');

//        $db = $this->mongo_db->db;
//        $vehicleTypes = $db->selectCollection('vehicleTypes');
//        $vehicleTypes->ensureIndex(array('location' => '2d'));

        $insertArr = array('type' => (int) $type_id, 'type_name' => $vehicletype, 'max_size' => (int) $seating, 'basefare' => (float) $basefare, 'min_fare' => (float) $minimumfare, 'price_per_min' => (float) $priceperminute, 'price_per_km' => (float) $priceperkm, 'type_desc' => $discription, 'city_id' => (int) $city, "location" => array("longitude" => (double) $cityData['City_Long'], "latitude" => (double) $cityData['City_Lat']));
        $this->mongo_db->insert('vehicleTypes', $insertArr);
        if ($this->db->affected_rows() > 0) {
            echo json_encode(array('msg' => "your vehicle type added succesfully", 'flag' => 1));
            return;
        }
    }

    function edit_vehicletype($param) {
        //   $city_id = $this->input->post('');

        $result = $this->db->query("select * from workplace_types where type_id='" . $param . "'")->result();
        //     $result = $this->db->query("select City_Id from city  where City_Name ='" . $param . "'")->result();
        //    $result = $this->db->query("update workplace_types set  where type_id='" . $param . "'")->result();
        return $result;
    }

    function update_vehicletype($param) {
        $vehicletype = $this->input->post('vehicletype');
        $seating = $this->input->post('seating');
        $minimumfare = $this->input->post('minimumfare');
        $basefare = $this->input->post('basefare');
        $priceperminute = $this->input->post('priceperminute');
        $priceperkm = $this->input->post('priceperkm');
        $discription = $this->input->post('discription');
        $city = $this->input->post('city');

        $fdata = array('type_name' => $vehicletype,
            'max_size' => (int) $seating,
            'basefare' => (float) $basefare, 'min_fare' => (float) $minimumfare,
            'price_per_min' => (float) $priceperminute,
            'price_per_km' => (float) $priceperkm, 'type_desc' => $discription,
            'city_id' => (int) $city,
        );
        //   $city_name = $this->db->query("select City_Name from city_available where City_Id = '" . $city . "'")->result();

        $result = $this->db->query("update workplace_types set type_name='" . $vehicletype . "',
                       max_size='" . $seating . "',
                       basefare='" . $basefare . "',
                       min_fare='" . $minimumfare . "',
                       price_per_min='" . $priceperminute . "',
                       price_per_km='" . $priceperkm . "',
                       type_desc='" . $discription . "',
                      city_id='" . $city . "' where type_id='" . $param . "' ");

        $this->load->library('mongo_db');

        $this->mongo_db->update("vehicleTypes", $fdata, array("type" => (int) $param));




        if ($this->db->affected_rows() > 0) {
            echo json_encode(array('msg' => "your vehicle type updated succesfully", 'flag' => 1));
            return;
        } else {
            echo json_encode(array('msg' => "your vehicle type not updated try again!", 'flag' => 0));
            return;
        }
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
        return $this->db->query("select * from city_available order by City_Name")->result();
    }

    function get_driver() {
        return $this->db->query("select * from master order by last_name")->result();
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
        $logo = "0";
        $getcityname = $this->db->query("select * from company_info where  email = '" . $email . "'");

        if ($getcityname->num_rows() > 0) {


            echo json_encode(array('err' => 0));
        } else {
            $result['data'] = $this->db->query("insert into company_info(companyname,addressline1,city,state,postcode,vat_number,firstname,
                           lastname,email,mobile,userame,password,status) values(
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
                           '" . $password . "', '" . $status . "')");
            echo json_encode(array('err' => 1));
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


        if ($this->db->affected_rows() > 0) {
            echo json_encode(array('msg' => "your company  updated successfully", 'flag' => 1));
            return;
        }
    }

    function get_passengerinfo($status) {
        $varToShowData = $this->db->query("select * from slave where status='" . $status . "'order by slave_id DESC")->result();

        return $varToShowData;
    }

    function inactivepassengers() {
        $val = $this->input->post('val');

        foreach ($val as $result) {
            $this->db->query("update slave set status=1 where slave_id='" . $result . "'");
        }
    }

    function get_compaigns_data($status = '') {

        return $this->db->query(" select cp.*,c.city_name,c.Currency as currency from coupons cp, city c where cp.city_id = c.city_id and cp.coupon_type = '" . $status . "' and cp.status = '0' and user_type = 2")->result();
    }

    function get_compaigns_data_ajax($for = '') {
//            $date =  date('Y-m-d');
        $st = $this->input->post('value');
        if ($for == '1') {
            if ($st == '0')
                $res = $this->db->query(" select cp.*,c.city_name,c.Currency as currency from coupons cp, city c where cp.city_id = c.city_id and cp.coupon_type = '" . $for . "' and cp.status = '" . $st . "' and user_type = 2")->result();
            else if ($st == '1') {

                $res = $this->db->query(" select cp.*,c.city_name,c.Currency as currency from coupons cp, city c where cp.city_id = c.city_id and cp.coupon_type = '" . $for . "' and cp.status = '" . $st . "' and user_type = 2")->result();
            }
        } else if ($for == '2') {
            if ($st == '0')
                $res = $this->db->query(" select cp.*,c.city_name,c.Currency as currency from coupons cp, city c where cp.city_id = c.city_id and cp.coupon_type = '" . $for . "' and cp.status = '" . $st . "' and user_type = 2 and cp.expiry_date >= '" . date('Y-m-d') . "'")->result();


            else if ($st == '10')
                $res = $this->db->query("select cp.*,c.city_name,c.Currency as currency from coupons cp, city c where cp.city_id = c.city_id and cp.coupon_type = '" . $for . "' and user_type = 2 and cp.expiry_date < '" . date('Y-m-d') . "'")->result();
        }

        echo json_encode(array('data' => $res, 'tst' => "select cp.*,c.city_name,c.Currency as currency from coupons cp, city c where cp.city_id = c.city_id and cp.coupon_type = '" . $for . "' and user_type = 2 and cp.expiry_date > '" . date('Y-m-d') . "'"));
    }

    function deactivecompaigns() {
        $val = $this->input->post('val');
        foreach ($val as $row) {
            $this->db->query(" update  coupons set status=1   where id='" . $row . "'");
        }
        if ($this->db->affected_rows() > 0) {
            echo json_encode(array('msg' => "your selected discount deactivated successfully", 'flag' => 1));
            return;
        }
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
    
        if($this->db->affected_rows() > 0){
            echo json_encode(array('msg' => '0'));
            return;
          }
        else{
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
        $city = $this->input->post('city');
        $coupon_type = $this->input->post('coupon_type');
        $discount = $this->input->post('discount');
        $discounttype = $this->input->post('discountradio');
        $referaldiscount = $this->input->post('referaldiscount');
        $refferaldiscounttype = $this->input->post('refferalradio');
        $message = $this->input->post('message');
        $title = $this->input->post('title');


        $citys = $this->input->post('citys');
        $discounts = $this->input->post('discounts');
        $messages = $this->input->post('messages');
        $codes = $this->input->post('codes');
        $discounttypes = $this->input->post('discounttypes');

        if ($coupon_type == '1') {
            $res = $this->db->query("select * from coupons where coupon_type=1 and status=0 and city_id='" . $city . "' ");

            if ($res->num_rows() > 0) {
                return json_encode(array('msg' => "your coupon already exists in this city ", 'flag' => 1));
            }
        }

        if ($coupon_type == '1') {
            $this->db->query("insert into coupons(coupon_code,coupon_type,discount_type,discount,referral_discount_type,referral_discount,message,city_id,user_type,title)
        values('REFERRAL','1','" . $discounttype . "','" . $discount . "','" . $refferaldiscounttype . "','" . $referaldiscount . "','" . $message . "','" . $city . "','2','" . $title . "') ");
        } else if ($coupon_type == '2') {
            $this->db->query("insert into coupons(coupon_code,start_date,expiry_date,coupon_type,discount_type,discount,message,city_id,user_type,title)
                    values('" . $codes . "','" . date("Y-m-d", strtotime($this->input->post('sdate'))) . "','" . date("Y-m-d", strtotime($this->input->post('edate'))) . "','2','" . $discounttypes . "','" . $discounts . "','" . $messages . "','" . $citys . "','2','" . $title . "') ");
        }
    }

    function insertpass() {
        $password = $this->input->post('newpass');
        $val = $this->input->post('val');

        $res = $this->db->query("update slave set password='" . $password . "' where slave_id='" . $val . "'");
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
        $this->db->order_by("ci.City_Id", "desc");

        echo $this->datatables->generate();
    }

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

    function datatable_vehicletype($status = '') {

        $this->load->library('Datatables');
        $this->load->library('table');

        $city = $this->session->userdata('city_id');

        $cityCond = "";

        if ($city != '0')
            $cityCond = ' and w.city_id = "' . $city . '"';

        $this->datatables->select(' w.type_id,w.type_name,w.max_size,w.basefare,w.min_fare,w.price_per_min,w.price_per_km,w.type_desc,cty.City_Name')
                ->add_column('select', '<input type="checkbox" class="checkbox" name="checkbox" value= "$1"/>', 'w.type_id')
                ->from('workplace_types w, city_available cty')
                ->where('w.city_id = cty.City_Id' . $cityCond); //order by slave_id DESC ",false);
        $this->db->order_by("w.type_id", "desc");
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
            $citylist = ' and w.company = "' . $company . '"';




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
//        $this->datatables->select('w.workplace_id,w.uniq_identity,vt.vehicletype,vm.vehiclemodel,wt.type_name,w.Vehicle_Reg_No,w.License_Plate_No,w.Vehicle_Insurance_No,w.Vehicle_Color',false)
//            ->unset_column('w.workplace_id')
//            ->add_column('select', '<input type="checkbox" class="checkbox" name="checkbox" value= "$1"/>', 'w.workplace_id')
//            ->from('workplace w,vehicleType vt,vehiclemodel vm,workplace_types wt ')
//            ->where('vt.id = w.title and vm.id=w.Vehicle_Model and wt.type_id = w.type_id  and w.status = "' . $status . '" ' . $compCond); //order by slave_id DESC ",false);


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

        echo $this->datatables->generate();
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

        if ($for == 'my') {
            if ($company != '0') {
//            if ($status == '1')
                $whererc = "mas.status IN ('" . $status . "') and mas.company_id = '" . $company . "' ";
//            else
//                $whererc = 'mas.status IN ("' . $status . '") and  mas.company_id = "'.$company.'" ';
            } else {

//                if ($status == '1')
                $whererc = "mas.status IN ('" . $status . "')";
//                else
//                    $whererc = 'mas.status IN ("' . $status . '") ';
            }
            if ($status == 1) {

                $this->datatables->select("distinct mas.mas_id as rahul,mas.first_name ,mas.last_name ,mas.mobile, mas.email,mas.created_dt,mas.profile_pic as pp,"
                                . "(select companyname from company_info where mas.company_id = company_id ) as companyname1,"
                                . "(select (case type when 2 then 'android_icon.png' when 1 then 'iphone-logo.png' END) from user_sessions where oid = mas.mas_id order by oid DESC limit 0,1) as type_img ", false)
                        ->unset_column('type_img')
                        ->unset_column('pp')
                        ->unset_column('companyname1')
                        ->add_column('PROFILE PIC', '<img src="' . base_url() . '../../pics/$1" width="50px" height="50px">', 'pp')
                        ->add_column('DEVICE TYPE', '<img src="' . base_url() . '../../admin/assets/$1" width="50px">', 'type_img')
                        ->add_column('SELECT', '<input type="checkbox" class="checkbox" name="checkbox" value="$1"/>', 'rahul')
                        ->from("master mas")
                        ->where($whererc);
            } else if ($status == 3 || $status == 4) {
                $this->datatables->select("distinct mas.mas_id as rahul,mas.first_name ,mas.last_name ,mas.mobile, mas.email,mas.created_dt,mas.profile_pic as pp,"
                                . "(select companyname from company_info where mas.company_id = company_id ) as companyname1,"
                                . "(select (case type when 2 then 'android_icon.png' when 1 then 'iphone-logo.png' END) from user_sessions where oid = mas.mas_id order by oid DESC limit 0,1) as type_img ", false)
                        ->unset_column('type_img')
                        ->unset_column('pp')
                        ->add_column('PROFILE PIC', '<img src="' . base_url() . '../../pics/$1" width="50px" height="50px">', 'pp')
                        ->add_column('DEVICE TYPE', '<img src="' . base_url() . '../../admin/assets/$1" width="50px">', 'type_img')
                        ->add_column('SELECT', '<input type="checkbox" class="checkbox" name="checkbox" value="$1"/>', 'rahul')
                        ->from("master mas")
                        ->where($whererc);
            }
        } else if ($for == 'mo') {

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
                $drivers = $selecttb->find(array('status' => (int) 4,'carId' => array('$nin' =>array(0))));
                foreach ($drivers as $mas_id) {
                    $darray[] = $mas_id['user'];
                }
            }

            $mas_ids = implode(',', $darray);
            if ($mas_ids == '')
                $mas_ids = 0;
            $companywhere = '';
            if ($company != '0') {
                $companywhere = "and mas.company_id=" . $company;
            }

            $this->datatables->select("distinct mas.mas_id as rahul,mas.first_name ,mas.last_name ,mas.mobile, mas.email,mas.created_dt,mas.profile_pic as pp,"
                            . "(select companyname from company_info where mas.company_id = company_id ),"
                            . "(select (case type when 2 then 'android_icon.png' when 1 then 'iphone-logo.png' END) from user_sessions where oid = mas.mas_id order by oid DESC limit 0,1) as type_img,"
                            . "(select uniq_identity from workplace where workplace_id = mas.workplace_id) as vehicleid,"
                            . "(select Vehicle_Image from workplace where workplace_id = mas.workplace_id ) as vehicleimage", false)
                          
                                ->unset_column('vehicleimage')
                                ->unset_column('type_img')
                             ->unset_column('pp')
                             ->add_column('PROFILE PIC', '<img src="' . base_url() . '../../pics/$1" width="50px">', 'pp')
                            ->add_column('DEVICE TYPE', '<img src="' . base_url() . '../../admin/assets/$1" width="50px">', 'type_img')
                             ->add_column('VEHICLE IMAGE', '<img src="' . base_url() . '../../admin/assets/$1" width="50px">', 'vehicleimage')
                            ->add_column('SELECT', '<input type="checkbox" class="checkbox" name="checkbox" value="$1"/>', 'rahul')
                            ->from("master mas")
                            ->where("mas.mas_id IN (" . $mas_ids . ")" . $companywhere);


//        $quaery = $this->db->query("SELECT distinct mas.mas_id, mas.first_name ,mas.zipcode, mas.profile_pic, mas.last_name, mas.email, mas.mobile, mas.status,mas.created_dt,(select type from user_sessions where oid = mas.mas_id order by oid DESC limit 0,1) as dev_type FROM master mas where  mas.mas_id IN (" . $mas_ids . ")  order by mas.mas_id DESC")->result();
//        return $quaery;
        }

        $this->db->order_by("rahul", "desc");
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
            return '<img src="' . base_url() . '../../pics/' . $id . '" width="50px">';
        else
            return '<img src="' . base_url() . '../../admin/img/user.jpg" width="50px">';
    }

    function get_devicetype($id) {
//return $id;

        if ($id)
            return '<img src="' . base_url() . '../../admin/assets/' . $id . '" width="50px" >';
        else
            return '<img src="' . base_url() . '../../admin/img/user.jpg" width="50px" >';
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
        }

        $this->datatables->select('dis_id,(select City_Name from city where City_Id = city),dis_email,dis_name,status')
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
                            . '<a target="_blank" href="' . base_url() . '../../pics/$1">view</a><a target="_blank" href="' . base_url() . '../../pics/$1"></button><button type="button" name="view"  width="50px">download</button></a>', 'd.url'
                    )
                    ->from("master c,docdetail d")
                    ->where("c.mas_id = d.driverid and d.doctype=1" . ($company != 0 ? ' and c.company_id = "' . $company . '"' : '')); //order by slave_id DESC ",false);
        } else if ($status == '2') {

            $this->datatables->select("d.doc_ids,c.first_name,c.last_name,d.expirydate,d.url")
                    ->unset_column('d.url')
                    ->add_column('VIEW', '<a target="_blank" href="' . base_url() . '../../pics/$1"><button type="button" name="view"  width="50px">view</button></a><a target="_blank" href="' . base_url() . '../../pics/$1"><button type="button" name="view"  width="50px">download</button></a>', 'd.url'
                    )
                    ->from("master c,docdetail d")->where("c.mas_id = d.driverid and d.doctype=2" . ($company != 0 ? ' and c.company_id = "' . $company . '"' : '')); //order by slave_id DESC ",false);
        } else if ($status == '3') {


            $this->datatables->select("d.docid,d.vechileid,(select companyname from company_info where company_id = w.company) as companyname,d.expirydate,d.url")
                    ->unset_column('d.url')
                    ->add_column('VIEW', '<button type="button" name="view"  width="50px"><a target="_blank" href="' . base_url() . '../../pics/$1">view</button></a><a target="_blank" href="' . base_url() . '../../pics/$1"><button type="button" name="view"  width="50px">download</button></a>', 'd.url'
                    )
//                     ->select("(select companyname from company_info where company_id = w.company) as companyname",false)
                    ->from("workplace w,vechiledoc d,vehicleType v")
                    ->where("w.title = v.id and w.workplace_id = d.vechileid and d.doctype = 2" . ($company != 0 ? ' and w.company = "' . $company . '"' : '')); //order by slave_id DESC ",false);
        } else if ($status == '4') {

            $this->datatables->select("d.docid,d.vechileid,(select companyname from company_info where company_id = w.company) as companyname,d.url,d.expirydate")
                    ->unset_column('d.url')
                    ->add_column('VIEW', '<a target="_blank" href="' . base_url() . '../../pics/$1"><button type="button" name="view"  width="50px">view</button></a><a target="_blank" href="' . base_url() . '../../pics/$1"><button type="button" name="view"  width="50px">download</button></a>', 'd.url'
                    )
//                     ->select("(select companyname from company_info where company_id = w.company) as companyname",false)
                    ->from("workplace w,vechiledoc d,vehicleType v")
                    ->where("w.title = v.id and w.workplace_id = d.vechileid and d.doctype = 3" . ($company != 0 ? ' and w.company = "' . $company . '"' : '')); //order by slave_id DESC ",false);
        } else if ($status == '5') {

            $this->datatables->select("d.docid,d.vechileid,(select companyname from company_info where company_id = w.company) as companyname,d.url,d.expirydate")
                    ->select("(select companyname from company_info where company_id = w.company) as companyname", false)
                    ->unset_column('d.url')
                    ->add_column('VIEW', '<a target="_blank" href="' . base_url() . '../../pics/$1"><button type="button" name="view"  width="50px">view</button></a><a target="_blank" target="_blank" href="' . base_url() . '../../pics/$1"><button type="button" name="view"  width="50px">download</button></a>', 'd.url'
                    )
                    ->from("workplace w,vechiledoc d,vehicleType v")
                    ->where("w.title = v.id and w.workplace_id = d.vechileid and d.doctype = 1" . ($company != 0 ? ' and w.company = "' . $company . '"' : '')); //order by slave_id DESC ",false);
        }

        echo $this->datatables->generate();
    }

    function datatable_driverreview($status = '') {


        $this->load->library('Datatables');
        $this->load->library('table');

        $this->datatables->select("r.appointment_id,a.appointment_dt, d.first_name,r.slave_id,r.review, r.star_rating,r.status")
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
        if ($_FILES["certificate"]["name"] != '' && $_FILES["certificate"]["size"] > 0) {
            $name = $_FILES["certificate"]["name"];
            $ext = substr($name, strrpos($name, '.') + 1); //explode(".", $name); # extra () to prevent notice
            $cert_name = (rand(1000, 9999) * time()) . '.' . $ext;
            move_uploaded_file($_FILES['certificate']['tmp_name'], $documentfolder . $cert_name);
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
        foreach ($val as $row) {

            $this->db->query("delete from workplace_types where type_id='" . $row . "' ");

            $this->mongo_db->delete('vehicleTypes', array('type' => (int) $row));
        }
        if ($this->db->affected_rows() > 0) {

            echo json_encode(array('msg' => "vehicle type deleted successfully", 'flag' => 1));
            return;
        }
    }

    function delete_company() {

        $val = $this->input->post('val');
        foreach ($val as $row) {

            $this->db->query("delete from company_info where company_id='" . $row . "' ");
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

        $quaery = $this->db->query("SELECT distinct mas.mas_id, mas.first_name ,mas.zipcode, mas.profile_pic, mas.last_name, mas.email, mas.mobile, mas.status,mas.created_dt,(select type from user_sessions where oid = mas.mas_id order by oid DESC limit 0,1) as dev_type FROM master mas where  mas.status IN (" . $status . ") and mas.company_id IN (" . $this->session->userdata('LoginId') . ") order by mas.mas_id DESC")->result();
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
        return $this->db->query("select ci.*,co.Country_Name from city_available ci,country co where ci.country_id = co.country_id")->result();
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
                ->from('appointment a,master m,slave s')
                ->where($query);

        $this->db->order_by('a.appointment_id', 'DESC');
//            ->add_column('Actions', '<img src="asdf">', 'City_id')
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


            $this->datatables->select('distinct doc.mas_id as masid,doc.first_name,'
//                 .'(select count(appointment_id) from appointment where mas_id = doc.mas_id and status = 9) as cmpltApts ,'
                            . "(case  when TRUNCATE((select sum(amount) from appointment where mas_id = doc.mas_id and DATE(appointment_dt) = '" . $explodeDateTime[0] . "' and status = 9),2)  IS NULL then '--'  else TRUNCATE((select sum(amount) from appointment where mas_id = doc.mas_id and DATE(appointment_dt) = '" . $explodeDateTime[0] . "' and status = 9),2) END) as TODAY_EARNINGS ,"
//                 .'(select amount from appointment where mas_id = doc.mas_id and status = 9 order by appointment_id DESC limit 0'.','.'1) as last_billed_amount ,'
//                    TODAY EARNINGS','WEEK EARNINGS','MONTH EARNINGS','LIFE TIME EARNINGS'
                            . " (case  when TRUNCATE((select sum(amount) from appointment where mas_id = doc.mas_id and status = 9 and DATE(appointment_dt) BETWEEN '" . $weekData['first_day_of_week'] . "' and '" . $weekData['last_day_of_week'] . "'),2)  IS NULL then '--' else TRUNCATE((select sum(amount) from appointment where mas_id = doc.mas_id and status = 9 and DATE(appointment_dt) BETWEEN '" . $weekData['first_day_of_week'] . "' and '" . $weekData['last_day_of_week'] . "'),2) END) as WEEK_EARNINGS ,"
                            . " (case  when TRUNCATE((select sum(amount) from appointment where mas_id = doc.mas_id and status = 9 and DATE_FORMAT(appointment_dt,  '%Y-%m') = '" . $explodeDate[0] . '-' . $explodeDate[1] . "' ),2)  IS NULL then '--' else TRUNCATE((select sum(amount) from appointment where mas_id = doc.mas_id and status = 9 and DATE_FORMAT(appointment_dt,  '%Y-%m') = '" . $explodeDate[0] . '-' . $explodeDate[1] . "' ),2)  END) as MONTH_EARNINGS,"
                            . " (case  when TRUNCATE((select sum(amount) from appointment where mas_id = doc.mas_id and status = 9),2)  IS NULL then '--'  else TRUNCATE((select sum(amount) from appointment where mas_id = doc.mas_id and status = 9),2) END) as LIFE_TIME_EARNINGS,"
                            . "(case  when TRUNCATE((select sum(amount) from appointment where mas_id = doc.mas_id and status = 9)-(select sum(pay_amount) from payroll where doc.mas_id = mas_id),2)  IS NULL then '--'  else TRUNCATE((select due_amount from payroll where doc.mas_id = mas_id order by payroll_id DESC limit 0,1),2) END) as DUE", false)
                    ->add_column('SHOW', '<a href="' . base_url("index.php/superadmin/DriverDetails/$1") . '"><button class="btn btn-success btn-cons" style="min-width: 83px !important;">DETAILS</button></a>
            <a href="' . base_url("index.php/superadmin/Driver_pay/$1") . '"><button class="btn btn-success btn-cons" style="min-width: 83px !important;">Pay</button>', 'masid')
                    ->from('master doc', false)
                    ->where($wereclousetocome);
        } else {

            $this->datatables->select('doc.mas_id,doc.first_name,'
//                 .'(select count(appointment_id) from appointment where mas_id = doc.mas_id and status = 9) as cmpltApts ,'
                            . "(case  when TRUNCATE((select sum(amount) from appointment where mas_id = doc.mas_id and DATE(appointment_dt) = '" . $explodeDateTime[0] . "' and status = 9),2)  IS NULL then '--'  else TRUNCATE((select sum(amount) from appointment where mas_id = doc.mas_id and DATE(appointment_dt) = '" . $explodeDateTime[0] . "' and status = 9),2) END) as TODAY_EARNINGS ,"
//                 .'(select amount from appointment where mas_id = doc.mas_id and status = 9 order by appointment_id DESC limit 0'.','.'1) as last_billed_amount ,'
//                    TODAY EARNINGS','WEEK EARNINGS','MONTH EARNINGS','LIFE TIME EARNINGS'
                            . " (case  when TRUNCATE((select sum(amount) from appointment where mas_id = doc.mas_id and status = 9 and DATE(appointment_dt) BETWEEN '" . $weekData['first_day_of_week'] . "' and '" . $weekData['last_day_of_week'] . "'),2)  IS NULL then '--' else TRUNCATE((select sum(amount) from appointment where mas_id = doc.mas_id and status = 9 and DATE(appointment_dt) BETWEEN '" . $weekData['first_day_of_week'] . "' and '" . $weekData['last_day_of_week'] . "'),2) END) as WEEK_EARNINGS ,"
                            . " (case  when TRUNCATE((select sum(amount) from appointment where mas_id = doc.mas_id and status = 9 and DATE_FORMAT(appointment_dt,  '%Y-%m') = '" . $explodeDate[0] . '-' . $explodeDate[1] . "' ),2)  IS NULL then '--' else TRUNCATE((select sum(amount) from appointment where mas_id = doc.mas_id and status = 9 and DATE_FORMAT(appointment_dt,  '%Y-%m') = '" . $explodeDate[0] . '-' . $explodeDate[1] . "' ),2)  END) as MONTH_EARNINGS,"
                            . " (case  when TRUNCATE((select sum(amount) from appointment where mas_id = doc.mas_id and status = 9),2)  IS NULL then '--'  else TRUNCATE((select sum(amount) from appointment where mas_id = doc.mas_id and status = 9),2) END) as LIFE_TIME_EARNINGS,"
                            . "(case  when TRUNCATE((select sum(amount) from appointment where mas_id = doc.mas_id and status = 9)-(select sum(pay_amount) from payroll where doc.mas_id = mas_id),2)  IS NULL then '--'  else TRUNCATE((select due_amount from payroll where doc.mas_id = mas_id order by payroll_id DESC limit 0,1),2) END) as DUE", false)
                    ->add_column('SHOW', '<a href="' . base_url("index.php/superadmin/DriverDetails/$1") . '"><button class="btn btn-success btn-cons" style="min-width: 83px !important;">DETAILS</button></a>
            <a href="' . base_url("index.php/superadmin/Driver_pay/$1") . '"><button class="btn btn-success btn-cons" style="min-width: 83px !important;">Pay</button>', 'doc.mas_id')
                    ->from('master doc', false);
        }


//                $this->db->order_by('ap.appointment_id' ,'DESC');


        echo $this->datatables->generate();
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

        $this->datatables->select('distinct doc.mas_id as rahul,doc.first_name,'
//                 .'(select count(appointment_id) from appointment where mas_id = doc.mas_id and status = 9) as cmpltApts ,'
                        . "(case  when TRUNCATE((select sum(amount) from appointment where mas_id = doc.mas_id and DATE(appointment_dt) = '" . $explodeDateTime[0] . "' and status = 9),2)  IS NULL then '--'  else TRUNCATE((select sum(amount) from appointment where mas_id = doc.mas_id and DATE(appointment_dt) = '" . $explodeDateTime[0] . "' and status = 9),2) END) as TODAY_EARNINGS ,"
//                 .'(select amount from appointment where mas_id = doc.mas_id and status = 9 order by appointment_id DESC limit 0'.','.'1) as last_billed_amount ,'
//                    TODAY EARNINGS','WEEK EARNINGS','MONTH EARNINGS','LIFE TIME EARNINGS'
                        . " (case  when TRUNCATE((select sum(amount) from appointment where mas_id = doc.mas_id and status = 9 and DATE(appointment_dt) BETWEEN '" . $weekData['first_day_of_week'] . "' and '" . $weekData['last_day_of_week'] . "'),2)  IS NULL then '--' else TRUNCATE((select sum(amount) from appointment where mas_id = doc.mas_id and status = 9 and DATE(appointment_dt) BETWEEN '" . $weekData['first_day_of_week'] . "' and '" . $weekData['last_day_of_week'] . "'),2) END) as WEEK_EARNINGS ,"
                        . " (case  when TRUNCATE((select sum(amount) from appointment where mas_id = doc.mas_id and status = 9 and DATE_FORMAT(appointment_dt,  '%Y-%m') = '" . $explodeDate[0] . '-' . $explodeDate[1] . "' ),2)  IS NULL then '--' else TRUNCATE((select sum(amount) from appointment where mas_id = doc.mas_id and status = 9 and DATE_FORMAT(appointment_dt,  '%Y-%m') = '" . $explodeDate[0] . '-' . $explodeDate[1] . "' ),2)  END) as MONTH_EARNINGS,"
                        . " (case  when TRUNCATE((select sum(amount) from appointment where mas_id = doc.mas_id and status = 9),2)  IS NULL then '--'  else TRUNCATE((select sum(amount) from appointment where mas_id = doc.mas_id and status = 9),2) END) as LIFE_TIME_EARNINGS,"
                        . "(case  when TRUNCATE((select sum(amount) from appointment where mas_id = doc.mas_id and status = 9)-(select sum(pay_amount) from payroll where doc.mas_id = mas_id),2)  IS NULL then '--'  else TRUNCATE((select due_amount from payroll where doc.mas_id = mas_id order by payroll_id DESC limit 0,1),2) END) as DUE", false)
                ->add_column('SHOW', '<a href="' . base_url("index.php/superadmin/DriverDetails/$1") . '"><button class="btn btn-success btn-cons" style="min-width: 83px !important;">DETAILS</button></a>
             <a href="' . base_url("index.php/superadmin/Driver_pay/$1") . '"><button class="btn btn-success btn-cons" style="min-width: 83px !important;">Pay</button>', 'rahul')
                ->from(' master doc,appointment a ', false)
                ->where($query);


//                $this->db->order_by('ap.appointment_id' ,'DESC');


        echo $this->datatables->generate();
    }

    function addNewDriverData() {
        $datai['first_name'] = $this->input->post('firstname');
        $datai['last_name'] = $this->input->post('lastname');
        $datai['password'] = $this->input->post('password');
        $datai['created_dt'] = date('y/m/d h:i:s a', time());
        $datai['type_id'] = 1;
        $datai['status'] = 1;
        $datai['email'] = $this->input->post('email');
        $datai['mobile'] = $this->input->post('mobile');
        $datai['zipcode'] = $this->input->post('zipcode');
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

    function editdriverdata() {

        $driverid = $this->input->post('driver_id');

        $first_name = $this->input->post('firstname');
        $last_name = $this->input->post('lastname');
        $password = $this->input->post('password');
        $created_dt = date('y/m/d h:i:s a', time());
        $type_id = 1;
        $status = 1;
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
                'password' => $password, 'created_dt' => $created_dt, 'type_id' => $type_id, 'status' => $status, 'mobile' => $mobile, 'zipcode' => $zipcode);
        else
            $driverdetails = array('first_name' => $first_name, 'last_name' => $last_name, 'license_pic' => $license_pic,
                'password' => $password, 'created_dt' => $created_dt, 'type_id' => $type_id, 'status' => $status, 'mobile' => $mobile, 'zipcode' => $zipcode);

        $this->db->where('mas_id', $driverid);
        $this->db->update('master', $driverdetails);



        $docdetail = array('url' => $license_pic, 'expirydate' => date("Y-m-d", strtotime($expirationrc)));
        $this->db->where('driverid', $driverid);
        $this->db->where('doctype', 1);
        $this->db->update('docdetail', $docdetail);

        $docdet = array('url' => $carriage_name, 'expirydate' => '0000-00-00');
        $this->db->where('driverid', $driverid);
        $this->db->where('doctype', 2);
        $this->db->update('docdetail', $docdet);

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
//            $query = 'ap.status = 9 and ap.payment_status in(1,3)';

        $this->datatables->select("ap.appointment_id,ap.mas_id, DATE_FORMAT(ap.appointment_dt, '%b %d %Y %h:%i %p'),ap.amount, ap.app_commission, ap.pg_commission,ap.mas_earning, ap.txn_id,(
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

        $this->datatables->select("ap.appointment_id,ap.mas_id,DATE_FORMAT(ap.appointment_dt, '%b %d %Y %h:%i %p'),ap.amount, ap.app_commission, ap.pg_commission,ap.mas_earning, ap.txn_id,(
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
            $query = 'd.company_id = c.company_id and ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and ap.status = 9 and payment_status in(1,3) and ap.payment_type = "' . $selectdval . '" and d.company_id="' . $this->session->userdata('company_id') . '"';
        } else if ($selectdval == '0' && $this->session->userdata('company_id') == '0') {
            $query = 'd.company_id = c.company_id and ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and ap.status = 9 and payment_status in(1,3)';
        } else if ($selectdval != '0' && $this->session->userdata('company_id') == '0') {
            $query = 'd.company_id = c.company_id and ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and ap.status = 9 and payment_status in(1,3) and ap.payment_type = "' . $selectdval . '" ';
        } else {
            $query = 'd.company_id = c.company_id and ap.mas_id = d.mas_id and ap.slave_id = p.slave_id and ap.status = 9 and payment_status in(1,3) and d.company_id="' . $this->session->userdata('company_id') . '"';
        }


        $this->datatables->select("ap.appointment_id,ap.mas_id,DATE_FORMAT(ap.appointment_dt, '%b %d %Y %h:%i %p'),ap.amount, ap.app_commission, ap.pg_commission,ap.mas_earning, ap.txn_id,(
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

    function passenger_rating() {
        $status = 1;
        $query = $this->db->query(" SELECT distinct p.slave_id, p.first_name ,p.email,(select avg(rating) from passenger_rating where slave_id = p.slave_id) as rating FROM passenger_rating r, slave p WHERE r.slave_id = p.slave_id  AND r.status ='" . $status . "'")->result();
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
            $query = "ap.status = 9 and ap.payment_status in(1,3) and p.slave_id = ap.slave_id and ap.mas_id='" . $mas_id . "' and  doc.company_id ='" . $this->session->userdata('company_id') . "'";
        else
            $query = 'ap.status = 9 and ap.payment_status in(1,3) and p.slave_id = ap.slave_id and ap.mas_id="' . $mas_id . '"';

        $this->datatables->select('distinct ap.appointment_id,p.first_name,ap.amount, ap.app_commission, ap.pg_commission,ap.mas_earning', false)
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

        $quaery = $this->db->query("SELECT distinct mas.mas_id, mas.first_name ,mas.zipcode, mas.profile_pic, mas.last_name, mas.email, mas.mobile, mas.status,mas.created_dt,(select type from user_sessions where oid = mas.mas_id order by oid DESC limit 0,1) as dev_type FROM master mas where  mas.mas_id IN (" . $mas_ids . ")  order by mas.mas_id DESC")->result();
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
            $img = base_url() . "../pics/" . $profile;
        } else {
            $img = "http://104.236.41.101/tutree/pics/aa_default_profile_pic.gif";
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

    function Driver_pay($masid = '') {

//      $query = "select * from payroll wehre company_id='".$this->session->userdata('LoginId')."'";

        $query = "select sum(a.amount) as total,m.first_name from appointment a,master m where a.mas_id = '" . $masid . "' and a.mas_id = m.mas_id and a.status = 9 and (a.cancel_status not in(1,2,7) or a.cancel_status is null)";
        return $this->db->query($query)->result();
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
