<?php

/*
 * For more details refer->    amazon_s3_php/example.php
 *  */
//echo json_encode(array('msg' => '2'));
require_once 'S3.php';


$name = $_FILES['myfile']['name']; // filename to get file's extension
$size = $_FILES['myfile']['size'];



$fold_name =$_REQUEST['uploadType'];
$type = $_REQUEST['type'];

$ext = substr($name, strrpos($name, '.') + 1);


$dat = getdate();
$rename_file = "file" . $dat['year'] . $dat['mon'] . $dat['mday'] . $dat['hours'] . $dat['minutes'] . $dat['seconds'] . "." . $ext;
$flag = FALSE;

$tmp1 = $_FILES['myfile']['tmp_name'];

if (!defined('awsAccessKey'))
    define('awsAccessKey', 'AKIAIWUIVGMPK7KHOHSA');
if (!defined('awsSecretKey'))
    define('awsSecretKey', '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');
$uploadFile = $tmp1;
$bucketName = 'tutree';


if (!file_exists($uploadFile) || !is_file($uploadFile)){
    exit("\nERROR: No such file: $uploadFile\n\n");

}
if (!extension_loaded('curl') && !@dl(PHP_SHLIB_SUFFIX == 'so' ? 'curl.so' : 'php_curl.dll')){
    exit("\nERROR: CURL extension not loaded\n\n");

}
if (awsAccessKey == 'change-this' || awsSecretKey == 'change-this'){
    exit("\nERROR: AWS access information required\n\nPlease edit the following lines in this file:\n\n" .
        "define('awsAccessKey', 'change-me');\ndefine('awsSecretKey', 'change-me');\n\n");
}
// Instantiate the class
$s3 = new S3(awsAccessKey, awsSecretKey);

//// Put our file (also with public read access)
if ($s3->putObjectFile($uploadFile, $bucketName, $fold_name . '/' .$type . '/'. $rename_file, S3::ACL_PUBLIC_READ)) {
    $flag = true;
}

if ($flag) {
    echo json_encode(array('msg' => '1', 'fileName' => $bucketName . '/' . $fold_name . '/' .$type . '/'. $rename_file));
} else {
    echo json_encode(array('msg' => '2', 'folder' => $fold_name));
}

?>