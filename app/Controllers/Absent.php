<?php

namespace App\Controllers;

use \Firebase\JWT\JWT;
use CodeIgniter\RESTful\ResourceController;
use App\Models\M_Absent;
use CodeIgniter\API\ResponseTrait;

class Absent extends BaseController
{
    use ResponseTrait;

    public function __construct()
    {
        $this->m_absent = new M_Absent();
        date_default_timezone_set("Asia/Jakarta");
    }

    public function save()
    {
        // return $this->isValidToken();

        $token      = $this->getParseToken();
        $nis      = $this->request->getPost('nis');
        $photo   = $this->request->getPost('photo');
        $latitude   = $this->request->getPost('latitude');
        $longitude   = $this->request->getPost('longitude');

        $tgl = date("Y-m-d H:i:s");

        if ($this->isValidToken()) {
            
            $decoded = base64_decode($photo);
            $newName = $nis . '_' . strtotime($tgl) . '.png';
            file_put_contents('images/' . $newName, $decoded);

            $data = [
                'tgl' => $tgl,
                'nis' => $nis,
                'photo' => $newName,
                'latitude' => $latitude,
                'longitude' => $longitude,
            ];


            if (is_null($data['latitude']) || $data['latitude'] == "" || is_null($data['longitude']) || $data['longitude'] == "") {
                $message = 'Failed get your location';
            } else {
                $save = $this->m_absent->insert($data);
                $message = 'Success absent at ' . $tgl;
                $data['save'] = $save;
            }

            $output = [
                'status' => 200,
                'result' => $message != 'Failed get your location',
                'message' => $message,
                'data' =>  $data
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
