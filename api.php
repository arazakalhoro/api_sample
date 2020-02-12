<?php
require_once("includes/Rest.inc.php");

class API extends REST
{
	public function __construct()
	{
		// Initializing parent constructor
		try {
			parent::__construct();
		} catch (Exception $exception){
			$response = [
				'message' => $exception->getMessage(),
				'error'=>true,
				'data'=>[]
			];
			$this->response($response, $exception->getCode());
		}
	}

	// Your api function continue from here
	public function index(){
		$response = [
			'message' => 'Welcome to my api',
			'error'=>false,
			'data'=>[]
		];
		$this->response($response, 200);
	}

}
// Initialize API Class
$api = new API;
$api->processApi();

?>
