<?php

/**
 * Description of ConDB
 *
 * @author admin@3embed
 */
require_once 'config.php';
class ConMongo {

    //put your code here

    public $mongo;

    public function __construct() {

        $host = MONGODB_HOST;
        $user = MONGODB_USER;
        $pass = MONGODB_PASS;
        $db = MONGODB_DB;
        $port = MONGODB_PORT;

        $con = new Mongo("mongodb://{$host}:{$port}");
        $this->mongo = $con->selectDB($db);
        if ($user != '' && $pass != '')
            $this->mongo->authenticate($user, $pass);
    }

    public function close($db) {
//            mysql_close($db);
    }

    public function MongoConnect($database, $host, $port) {
        $con = new Mongo("mongodb://{$host}:{$port}"); // Connect to Mongo Server
        return $con->selectDB($database); // Connect to Database
    }

}

?>
