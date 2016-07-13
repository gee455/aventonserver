<?php

/**
 * Description of ConDB
 *
 * @author admin@3embed
 */
error_reporting(1);

ini_set("log_errors", 1);
ini_set("error_log", "/tmp/admin-error.log");
$date = date("Y-m-d H:i:s");
error_log("$date: " . PHP_EOL);

require_once 'config.php';

class ConDB {

    //put your code here

    public $picHost = APP_PIC_HOST;
    public $conn;
    public $flag_conn;
    public $mongo;

    public function __construct() {
//        $this->mongo = $this->MongoConnect($this->mongoDB, 'localhost', '27017');

        $host = MONGODB_HOST;
        $user = MONGODB_USER;
        $pass = MONGODB_PASS;
        $db = MONGODB_DB;
        $port = MONGODB_PORT;

        $con = new MongoClient("mongodb://{$host}:{$port}");
        $this->mongo = $con->selectDB($db);
        if ($user != '' && $pass != '')
            $this->mongo->authenticate($user, $pass);

        $this->conn = mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASS);
        if ($this->conn) {
            if (mysql_select_db(MYSQL_DB, $this->conn)) {
                $this->flag_conn = 0;
            } else {
                $this->flag_conn = 1;
                die(print_r(mysql_errors(), true));
            }
        } else {
            $this->flag_conn = 1;
            die(print_r(mysql_error(), true));
        }
    }

    public function close($db) {
//            mysql_close($db);
    }

    public function MongoConnect($database, $host, $port) {
        $con = new MongoClient("mongodb://{$host}:{$port}"); // Connect to Mongo Server
        return $con->selectDB($database); // Connect to Database
    }

}

?>
