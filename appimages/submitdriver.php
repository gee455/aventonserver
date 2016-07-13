<?php

include('../Models/ConDB.php');
$db1 = new ConDB();



if ($_REQUEST['item_type'] == '1') {

    extract($_REQUEST);

    if (is_array($_REQUEST['item_list'])) {
        $value = implode(', ', $_REQUEST['item_list']);
    } else {
        $value = $_REQUEST['item_list'];
    }

    $checkUserQry = "select mas_id from master where email = '" . $email . "' and mas_id != '" . $value . "'";
    if (mysql_num_rows(mysql_query($checkUserQry, $db1->conn)) > 0) {

        echo json_encode(array('msg' => "Email already exist, choose another", 'flag' => 1));
        return false;
    }

    if ($_FILES['certificate']['size'] > 1000000 || $_FILES['insurcertificate']['size'] > 1000000 || $_FILES['carriagecertificate']['size'] > 1000000) {
        echo json_encode(array('msg' => "File size cannot be more than 1 MB", 'flag' => 1));
        return false;
    }

    if ($_FILES['certificate']['size'] > 0) {

        $name = $_FILES["certificate"]["name"];
        $ext = end((explode(".", $name))); # extra () to prevent notice
        $cert_name = (rand(1000, 9999) * time()) . '.' . $ext;

        if (!move_uploaded_file($_FILES['certificate']['tmp_name'], sprintf('../pics/%s', $cert_name))) {
            echo json_encode(array('msg' => "Failed to upload certificate-1", 'flag' => 1));
            return false;
        }

        $updateQry = "update docdetail set url = '" . $cert_name . "', expirydate = '" . $_REQUEST['expirationrc'] . "' where doctype =  1 and driverid = '" . $value . "'";
        mysql_query($updateQry, $db1->conn);
    }

    if ($_FILES['passbook']['size'] > 0) {
        $name = $_FILES["passbook"]["name"];
        $ext = end((explode(".", $name))); # extra () to prevent notice
        $cert_name = (rand(1000, 9999) * time()) . '.' . $ext;

        if (!move_uploaded_file($_FILES['passbook']['tmp_name'], sprintf('../pics/%s', $cert_name))) {
            echo json_encode(array('msg' => "Failed to upload passbook copy", 'flag' => 1));
            return false;
        }

        $updateQry = "update docdetail set url = '" . $cert_name . "' where doctype =  2 and driverid = '" . $value . "'";
        mysql_query($updateQry, $db1->conn);
    }

    if ($_FILES['photos']['size'] > 0) {
        $name = $_FILES["photos"]["name"];
        $ext = end((explode(".", $name))); # extra () to prevent notice
        $photosnew = (rand(1000, 9999) * time()) . '.' . $ext;

        $valid_exts = array("jpg", "jpeg", "gif", "png");

        if (!in_array($ext, $valid_exts)) {
            echo json_encode(array('msg' => "Please upload any of jpeg,png or gif only", 'flag' => 1));
            return false;
        }

        if (!move_uploaded_file($_FILES['photo']['tmp_name'], sprintf('../pics/%s', $photosnew))) {
            echo json_encode(array('msg' => "Failed to upload passbook copy", 'flag' => 1));
            return false;
        }

        $newwidth = "54";
        $newheight = "55";

        $file_to_open = "../pics/" . $photosnew;

        list($width, $height) = getimagesize($file_to_open);

        if ($width > $height) {

            $newwidth = $width;
            $newheight = ($newwidth / $width) * $height;
        } else {

            $newheight = $height;
            $newwidth = ($newheight / $height) * $width;
        }

        $ratio = $newheight / $newwidth;

        /* mdpi 36*36 */
        $mdpi_nw = 36;
        $mdpi_nh = $ratio * 36;

        $mtmp = imagecreatetruecolor($mdpi_nw, $mdpi_nh);

        if ($ext2 == "jpg" || $ext2 == "jpeg") {
            $image = imagecreatefromjpeg($file_to_open);
        } else if ($ext2 == "gif") {
            $image = imagecreatefromgif($file_to_open);
        } else if ($ext2 == "png") {
            $image = imagecreatefrompng($file_to_open);
        }

//$mdpi_image = imagecreatefromjpeg($file_to_open);

        imagecopyresampled($mtmp, $image, 0, 0, 0, 0, $mdpi_nw, $mdpi_nh, $width, $height);

        $mdpi_file = '../pics/mdpi/' . $photosnew;


        if (imagejpeg($mtmp, $mdpi_file, 100)) {
//    echo "mdpi success";
        } else {
//    echo "mdpi failed";
        }

        /* HDPI Image creation 55*55 */
        $hdpi_nw = 55;
        $hdpi_nh = $ratio * 55;

        $tmp = imagecreatetruecolor($hdpi_nw, $hdpi_nh);

//$hdpi_image = imagecreatefromjpeg($file_to_open);

        imagecopyresampled($tmp, $image, 0, 0, 0, 0, $hdpi_nw, $hdpi_nh, $width, $height);

        $hdpi_file = '../pics/hdpi/' . $photosnew;

        imagejpeg($tmp, $hdpi_file, 100);

        /* XHDPI 84*84 */
        $xhdpi_nw = 84;
        $xhdpi_nh = $ratio * 84;

        $xtmp = imagecreatetruecolor($xhdpi_nw, $xhdpi_nh);

//$xhdpi_image = imagecreatefromjpeg($file_to_open);

        imagecopyresampled($xtmp, $image, 0, 0, 0, 0, $xhdpi_nw, $xhdpi_nh, $width, $height);

        $xhdpi_file = '../pics/xhdpi/' . $photosnew;

        imagejpeg($xtmp, $xhdpi_file, 100);

        /* xXHDPI 125*125 */
        $xxhdpi_nw = 125;
        $xxhdpi_nh = $ratio * 125;

        $xxtmp = imagecreatetruecolor($xxhdpi_nw, $xxhdpi_nh);

//$xxhdpi_image = imagecreatefromjpeg($file_to_open);

        imagecopyresampled($xxtmp, $image, 0, 0, 0, 0, $xxhdpi_nw, $xxhdpi_nh, $width, $height);

        $xxhdpi_file = '../pics/xxhdpi/' . $photosnew;

        imagejpeg($xxtmp, $xxhdpi_file, 100);


        $updateQry = "update master set profile_pic = '" . $photosnew . "' where mas_id = '" . $value . "'";
        mysql_query($updateQry, $db1->conn);
    }

    $update_company = "UPDATE master SET mobile='" . $mobile . "',first_name='" . $firstname . "',last_name='" . $lastname . "',email='" . $email . "',password='" . $password . "',company_id = '" . $company . "' WHERE mas_id='" . $value . "'";
    $update_company_res = mysql_query($update_company);
    $affected = mysql_affected_rows();
    if ($affected > 0) {
        echo json_encode(array('qrys' => $update_company, 'flag' => 0, 'msg' => 'Driver details edited'));
    } else if ($affected == 0) {
        echo json_encode(array('qrys' => $update_company, 'flag' => 1, 'msg' => 'Edit any detail to update'));
    } else {
        echo json_encode(array('qrys' => $update_company, 'flag' => 1, 'msg' => 'Edit failed'));
    }
//        echo json_encode(array('flag'=>mysql_affected_rows()));

    return true;
}

if ($_REQUEST['item_type'] == '5') {
    if (is_array($_REQUEST['item_list'])) {
        $value = implode(', ', $_REQUEST['item_list']);
    } else {
        $value = $_REQUEST['item_list'];
    }
    $getvechiletype = "select m.*,(select expirydate from docdetail where driverid = '" . $value . "' and doctype = 1) as expirationrc from master m where m.mas_id in (" . $value . ")";
    $getvechiletype_res = mysql_query($getvechiletype, $db1->conn);
    $count = mysql_num_rows($getvechiletype_res);
    $vechileData = mysql_fetch_assoc($getvechiletype_res);
    if (is_array($vechileData)) {
        $vechileData['errFlag'] = 0;
        $vechileData['qry'] = $count;
        echo json_encode($vechileData);
    } else {
        $vechileData['errFlag'] = 1;
        //$vechileData['editflag'] = $edit_flag;
        $vechileData['qry'] = $getvechiletype;
        echo json_encode($vechileData);
    }
}
?>















