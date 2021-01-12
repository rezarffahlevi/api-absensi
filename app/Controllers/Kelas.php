<?php

namespace App\Controllers;

use App\Models\M_Kelas;
use CodeIgniter\API\ResponseTrait;

class Kelas extends BaseController
{
    use ResponseTrait;

    public function __construct()
    {
        $this->m_kelas = new M_Kelas();;
    }

    public function index()
    {
        if ($this->isValidToken()) {
            $output = [
                'status' => 200,
                'message' => 'success',
                'data' =>  $this->m_kelas->findAll()
            ];
        } else {
            $output = [
                'status' => 401,
                'message' => 'failed authentication',
                'data' =>  null
            ];
        }
        return $this->respond($output, 200);
    }
}
