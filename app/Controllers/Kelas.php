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
                'code' => $this->constant->success,
                'message' => 'success',
                'data' =>  $this->m_kelas->findAll(),
            ];
        } else {
            $output = [
                'code' => $this->constant->error_auth,
                'message' => 'failed authentication',
                'data' =>  null
            ];
        }
        return $this->respond($output, 200);
    }
}
