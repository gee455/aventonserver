<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

session_start();
require_once 'Models/ConDB.php';

if (!isset($_REQUEST['data']) && !isset($_SESSION['resetData']))
    header('location: http://www.google.com');

if (isset($_REQUEST['data']))
    $_SESSION['resetData'] = $_REQUEST['data'];

function checkPassword($pwd) {

    $errors = array();

    if (strlen($pwd) < 8) {
        $errors[] = "Password too short, 8 characters least!";
    }

    if (!preg_match("#[0-9]+#", $pwd)) {
        $errors[] = "Password must include at least one number!";
    }

    if (!preg_match("#[a-zA-Z]+#", $pwd)) {
        $errors[] = "Password must include at least one letter!";
    }

    return $errors;
}

$dataArr = explode('_', $_SESSION['resetData']);

if ($dataArr[1] == '1') {
    $table = 'master';
    $uid = 'mas_id';
} else if ($dataArr[1] == '2') {
    $table = 'slave';
    $uid = 'slave_id';
} else {
    header('location: http://www.google.com');
}

$message = "";

if (isset($_REQUEST['pass']) && isset($_REQUEST['conf_pass'])) {

    if ($_REQUEST['pass'] == '' || $_REQUEST['conf_pass'] == '') {
        $message = 'Please enter both fields.';
    } else if ($_REQUEST['pass'] != $_REQUEST['conf_pass']) {
        $message = 'Passwords does not match, please try again.';
    } else {

        $strength = checkPassword($_REQUEST['pass']);

        if (count($strength) > 0) {
            foreach ($strength as $err) {
                $message .= $err.'<br>';
            }
        } else {

            $db = new ConDB();

            $selectDataQry = "select $uid from $table where resetData = '" . $_SESSION['resetData'] . "' and resetFlag = 1";
            $selectDataRes = mysql_query($selectDataQry, $db->conn);

            if (mysql_num_rows($selectDataRes) <= 0) {
                $message = 'Nice try, use forget password option on the mobile App!.';
            } else {
                $userData = mysql_fetch_assoc($selectDataRes);

                $updateDataQry = "update $table set password = md5('" . $_REQUEST['pass'] . "'), resetData = null, resetFlag = null where $uid = '" . $userData[$uid] . "'";
                mysql_query($updateDataQry, $db->conn);

                if (mysql_affected_rows() > 0)
                    $message = 'Password changed successfully.';
                else
                    $message = 'Failed to change password.';
            }
        }
    }
}
?>
<div style="text-align: center;">
    <h3>Change the password here: </h3>
    <?php echo '<h5 style="color:red;">' . $message . '</h5>'; ?>
    <form action="" method="post">
        <lable>Password:</lable><input type="password" name="pass" /><br><br>
        <lable>Confirm :</lable><input type="password" name="conf_pass" /><br>
        <input type="submit" name="submit" /><br>
    </form>
</div>