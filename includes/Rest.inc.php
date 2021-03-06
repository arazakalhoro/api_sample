<?php
define('BASEPATH', realpath(dirname(__FILE__)).'/');
define('APPPATH', realpath(dirname(__FILE__)).'');
require_once("gump.class.php");
require_once("core/Common.php");
require_once("db/src/DB.php");

/*
 * Libraries are used in this API Class
 * 1) Request Validation
 *    Description: GUMP Validation library is used for validating and filtering any kind of request and its data
 *    Documentation URL: (https://github.com/Wixel/GUMP)
 * 2) Codeigniter Active Record class
 *    Description: This class is stand alone used for perform queries to db
 *    Documentation URL: (http://codeigniter.com/user_guide/database/)
 * */

require_once("config.php");

use Evolution\CodeIgniterDB as CI;



class REST {

	protected $form_validation = null;
	protected $config = [];
	protected $api_url = '';
	protected $db = NULL;

	private $_content_type = "application/json";
	private $_request = array();
	private $_code = 200;


	public function __construct(){
		// Configurations
		global  $config;
		$this->config = $config;
		// Debug mood
		$this->debug();

		$this->inputs();

		// Initialize form validation
		$this->form_validation = new GUMP;

		// Create DB connection
		$this->db_connection();

	}

	/*
	 * @desc This function will check and set debug of API
	 * @param
	 * @return void
	 * */
	private function debug(){
		if($this->config['api_mood'] == 'Development'){
			ini_set('display_errors', 1);
			ini_set('display_startup_errors', 1);
			error_reporting(E_ALL);
		}

	}

	/*
	 * @desc This function used send user/client json response
	 * @param string|array $data integer $status
	 * @return json string
	 * */
	public function response($data,$status){
		$this->_code = ($status) ? $status : 200;
		$this->set_headers();
		echo json_encode($data);
		exit;
	}
	/*
	 * @desc This function is used to build db connection
	 * @param
	 * @return PDO MySql driver base connection
	 * */
	protected function db_connection(){
		try{

			$this->db =& CI\DB($this->config['DB']);
		} catch (Exception $exception){
			$response = [
				'message' => $exception->getMessage(),
				'error' => true,
				'data'=>[]
			];
			$this->response($response, 500);
		}
	}

	/*
	 * @desc Handle Dynamic calls through HTTP methods and check if has method or not
	 * @param
	 * @return JSON
	 * */
	public function processApi()
	{
		$request = isset($_REQUEST['request']) ? $_REQUEST['request'] : 'index';
		$func = strtolower(trim(str_replace("/", "", $request)));
		if ((int) method_exists($this, $func) > 0){
			$this->$func();
		}
		else{
			// If the method not exist with in this class "Page not found".
			$response = [
				'message' => '404 Not Found',
				'error' => false,
				'data' => []
			];
			$this->response($response, 404);
		}
	}

	private function get_status_message(){
		$status = array(
					100 => 'Continue',
					101 => 'Switching Protocols',
					200 => 'OK',
					201 => 'Created',
					202 => 'Accepted',
					203 => 'Non-Authoritative Information',
					204 => 'No Content',
					205 => 'Reset Content',
					206 => 'Partial Content',
					300 => 'Multiple Choices',
					301 => 'Moved Permanently',
					302 => 'Found',
					303 => 'See Other',
					304 => 'Not Modified',
					305 => 'Use Proxy',
					306 => '(Unused)',
					307 => 'Temporary Redirect',
					400 => 'Bad Request',
					401 => 'Unauthorized',
					402 => 'Payment Required',
					403 => 'Forbidden',
					404 => 'Not Found',
					405 => 'Method Not Allowed',
					406 => 'Not Acceptable',
					407 => 'Proxy Authentication Required',
					408 => 'Request Timeout',
					409 => 'Conflict',
					410 => 'Gone',
					411 => 'Length Required',
					412 => 'Precondition Failed',
					413 => 'Request Entity Too Large',
					414 => 'Request-URI Too Long',
					415 => 'Unsupported Media Type',
					416 => 'Requested Range Not Satisfiable',
					417 => 'Expectation Failed',
					500 => 'Internal Server Error',
					501 => 'Not Implemented',
					502 => 'Bad Gateway',
					503 => 'Service Unavailable',
					504 => 'Gateway Timeout',
					505 => 'HTTP Version Not Supported');
		return ($status[$this->_code])? $status[$this->_code]: $status[500];
	}

	public function get_request_method(){
		return $_SERVER['REQUEST_METHOD'];
	}

	private function inputs(){
		switch($this->get_request_method()){
			case "POST":
				$this->_request = $this->cleanInputs($_POST);
				break;
			case "GET":
			case "DELETE":
				$this->_request = $this->cleanInputs($_GET);
				break;
			case "PUT":
				parse_str(file_get_contents("php://input"),$this->_request);
				$this->_request = $this->cleanInputs($this->_request);
				break;
			default:
				$this->response('',406);
				break;
		}
	}

	private function cleanInputs($data){
		$clean_input = array();
		if(is_array($data)){
			foreach($data as $k => $v){
				$clean_input[$k] = $this->cleanInputs($v);
			}
		}else{
			if(get_magic_quotes_gpc()){
				$data = trim(stripslashes($data));
			}
			$data = strip_tags($data);
			$clean_input = trim($data);
		}
		return $clean_input;
	}

	private function set_headers(){
		header("HTTP/1.1 ".$this->_code." ".$this->get_status_message());
		header("Content-Type:".$this->_content_type);
	}
}
?>
