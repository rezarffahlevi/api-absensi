<?php

namespace App\Controllers;

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

		$filename = "../public/images/" > $name;

		if (!file_exists($filename))
			$filename = "../public/images/avatar.png";

		$mime = mime_content_type($filename); //<-- detect file type
		header('Content-Length: ' . filesize($filename)); //<-- sends filesize header
		header("Content-Type: $mime"); //<-- send mime-type header
		header('Content-Disposition: inline; filename="' . $filename . '";'); //<-- sends filename header
		readfile($filename); //<--reads and outputs the file onto the output buffer
		die(); //<--cleanup
		exit; //and exit
		//  "<img src='".base_url("public/images/". $name) ."' onerror=\"this.onerror=null; this.src='".base_url("public/images/avatar.png")."'\"/>";
	}

	//--------------------------------------------------------------------

}
