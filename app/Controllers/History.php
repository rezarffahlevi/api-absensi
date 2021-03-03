<?php

namespace App\Controllers;

use App\Models\M_Absent;
use CodeIgniter\API\ResponseTrait;

class History extends BaseController
{
    use ResponseTrait;

    public function __construct()
    {
        $this->m_absent = new M_Absent();;
    }

    public function index()
    {
        if ($this->isValidToken()) {
            $get = $this->request->getGet();

            $page = isset($get['page']) ? $get['page'] : 1;
            $limit = isset($get['limit']) ? $get['limit'] : 10;

            $start = ($page > 1) ? ($page * $limit) - $limit : 0;

            $output = [
                'code'          => $this->constant->success,
                'message'       => 'success',
                'data'          =>  $this->m_absent->like('tgl',  date("Y-m-d"))->findAll($start, $limit),
            ];
        } else {
            $output = [
                'code' => $this->constant->error,
                'message' => 'failed authentication',
                'data' =>  null
            ];
        }
        return $this->respond($output, 200);
    }
}
