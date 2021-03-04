<?php namespace App\Controllers;

use \Firebase\JWT\JWT;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class Home extends BaseController
{
	use ResponseTrait;
	
	public function index()
	{
		// return view('welcome_message');
		
		$output = [
			'status' => 200,
			'message' => 'This is home!',
			'data' => null
		];
		return $this->respond($output, 200);
	}

	public function img()
	{
		$get = $this->request->getGet();
		$name = $get['name'];
		header('Content-Type: image/jpg');
		return "<img src='".base_url("public/images/". $name) ."' onerror=\"this.onerror=null; this.src='".base_url("public/images/avatar.png")."'\"/>";
	}

	//--------------------------------------------------------------------

}
