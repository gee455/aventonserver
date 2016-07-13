<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
//require_once("src/Mandrill.php");

class Mongo_db
{

    public $CI;

    public $connection;
    public $db;

    public $selects = array();
    public  $wheres = array();
    public $sorts = array();
    public $updates = array();

    public $limit = FALSE;
    public $offset = FALSE;
    private $_inserted_id = FALSE;


    public function __construct()
    {
        //Check mongodb is installed in your server otherwise display an error

        
        if(!class_exists('Mongo'))
        {
            $this->_show_error('The MongoDB PECL extension has not been installed or enabled', 500);
        }

        //get instance of CI class
        if (function_exists('get_instance'))
        {
            $this->_ci = get_instance();
        }
        else
        {
            $this->_ci = NULL;
        }

        //load the config file which we have created in 'config' directory
        $this->_ci->load->config('mongodb');
        $config='default';

        //Fetch Mongo server and database configuration from config file which we have created in 'config' directory
        $config_data = $this->_ci->config->item($config);

        try
        {
            

            
            //connect to the mongodb server
            $this->mb = new MongoClient('mongodb://'.$config_data['mongo_hostbase'].':27017');

            //select the mongodb database
            $this->db=$this->mb->selectDB($config_data['mongo_database']);
            
            
//echo 'hi';
//        exit();
            

            //user  name and pass word authetication
            
            if($config_data['mongo_username'] != '' &&  $config_data['mongo_password'] != '')
            $this->db->authenticate($config_data['mongo_username'], $config_data['mongo_password']);

        }
        catch(MongoConnectionException $exception)
        {

            
            //if mongodb is not connect, then display the error
            show_error('Unable to connect to Database', 500);
        }
    }

    public function get($collection = "", $limit = FALSE, $offset = FALSE) {
        if (empty($collection)) {
            //FIXME theow exception instead show error
            show_error("In order to retreive documents from MongoDB, a collection name must be passed", 500);
        }
         $cursor = $this->db->selectCollection($collection)->find($this->wheres, $this->selects);//->find($this->wheres, $this->selects);
         $this->_clear();
         return $cursor;
    }

    public function get_where($collection = "", $where = array(), $limit = FALSE, $offset = FALSE) {
        return $this->where($where)->get($collection, $limit, $offset);

//        return $this->where($where);
    }

    public function get_one($collection = "", $where = array()) {
        return $this->db->selectCollection($collection)->findOne($where);

//        return $this->where($where);
    }
    public function where($wheres = array(), $native = FALSE) {
        if ($native === TRUE && is_array($wheres)) {
            $this->wheres = $wheres;
        } elseif (is_array($wheres)) {
            foreach ($wheres as $where => $value) {
                $this->_where_init($where);
                $this->wheres[$where] = $value;
            }
        }
        return $this;
    }
    public function _where_init($param){
        if (!isset($this->wheres[$param])){
            $this->wheres[$param] = array();
        }
    }



    /**
     *
     * 	Count the documents based upon the passed parameters
     *
     *  @since v1.0.0
     */
    public function count_all_results($collection = "",$operation = array()) {
        if (empty($collection)) {
            show_error("In order to retreive a count of documents from MongoDB, a collection name must be passed", 500);
        }
        $cursor = $this->db->selectCollection($collection)->find($operation);
        $this->_clear();
        return $cursor->count();
    }




    /**
     *
     * 	Insert a new document into the passed collection
     *
     *  @since v1.0.0
     */
    public function insert($collection = "", $insert = array()) {
        if (empty($collection)) {
            show_error("No Mongo collection selected to insert into", 500);
        }

        if (count($insert) == 0) {
            show_error("Nothing to insert into Mongo collection or insert is not an array", 500);
        }
        $this->_inserted_id = FALSE;
        try {
            $query = $this->db->selectCollection($collection)->insert($insert);
            if (isset($insert['_id'])) {
                $this->_inserted_id = $insert['_id'];
                $this->_clear();
                return $insert['_id'];

            } else {
                return FALSE;
            }
        } catch (MongoException $e) {
            show_error("Insert of data into MongoDB failed: {$e->getMessage()}", 500);
        } catch (MongoCursorException $e) {
            show_error("Insert of data into MongoDB failed: {$e->getMessage()}", 500);
        }
    }




    /**
     *
     * Update a single document
     *
     *   @since v1.0.0
     */
    public function update($collection = "", $data = array(), $options = array()) {
        if (empty($collection)) {
            show_error("No Mongo collection selected to update", 500);
        }
        if (is_array($data) && count($data) > 0) {
            $this->_update_init('$set');
            $this->updates['$set'] += $data;
        }
        if (count($this->updates) == 0) {
            show_error("Nothing to update in Mongo collection or update is not an array", 500);
        }
        try {
//            $options = array_merge($options,array('multiple'=> FALSE));
            $this->db->selectCollection($collection)->update($options,array('$set' => $data),array('multiple'=> true));
            $this->_clear();
            return TRUE;
        } catch (MongoCursorException $e) {
            show_error("Update of data into MongoDB failed: {$e->getMessage()}", 500);
        } catch (MongoCursorException $e) {
            show_error("Update of data into MongoDB failed: {$e->getMessage()}", 500);
        } catch (MongoCursorTimeoutException $e) {
            show_error("Update of data into MongoDB failed: {$e->getMessage()}", 500);
        }
    }

    public function updatewithpush($collection = "", $data = array(), $options = array())
    {
        $this->db->selectCollection($collection)->update($options,array('$push' => $data),array("upsert" => true));
        
    }
    
    public function _update_init($method){
        if ( ! isset($this->updates[$method])){
            $this->updates[$method] = array();
        }
    }



    /**
     *
     * Delete document from the passed collection based upon certain criteria
     *
     *   @since v1.0.0
     */
    public function delete($collection = "", $options = array()) {
        if (empty($collection)) {
            show_error("No Mongo collection selected to delete from", 500);
        }
        try {
            $options = array_merge($options);
            if($options)
            $this->db->selectCollection($collection)->remove($options);
            $this->_clear();
            return TRUE;
        } catch (MongoCursorException $e) {
            show_error("Delete of data into MongoDB failed: {$e->getMessage()}", 500);
        } catch (MongoCursorTimeoutException $e) {
            show_error("Delete of data into MongoDB failed: {$e->getMessage()}", 500);
        }
    }



    public function _clear()
    {
        $this->selects	= array();
        $this->updates	= array();
        $this->wheres	= array();
        $this->limit	= FALSE;
        $this->offset	= FALSE;
        $this->sorts	= array();
    }



    function sendMail($template,$to,$from,$subject)
    {
        try {

            $mandrill = new Mandrill('KWT3-2cqRMWL1BawEIzGjw');
            $message = array(
                'html' => ($template),
                'text' => 'Example text content',
                'subject' => $subject,
                'from_email' =>$from,
                'from_name' => 'Ryland Insurence',
                'to' => $to,
                'headers' => array('Reply-To' => "prakashjoshi9090@gmail.com"),
                'important' => false,
                'track_opens' => null,
                'track_clicks' => null,
                'auto_text' => null,
                'auto_html' => null,
                'inline_css' => null,
                'url_strip_qs' => null,
                'preserve_recipients' => null,
                'view_content_link' => null,
                'bcc_address' => 'message.bcc_address@example.com',
                'tracking_domain' => null,
                'signing_domain' => null,
                'return_path_domain' => null,
                'merge' => true,
                'merge_language' => 'mailchimp',
                'metadata' => array('website' => 'www.RylandIncurence.com'),
            );

            $async = false;
            $ip_pool = 'Main Pool';
            $result = $mandrill->messages->send($message, $async, $ip_pool);
            $result['flag'] = 0;
            $result['message'] = $message;
            
            return true;

        } catch (Mandrill_Error $e) {
            print_r($e);
            return false;
        }
    }
}
?>