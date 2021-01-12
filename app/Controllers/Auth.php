<?php

namespace App\Controllers;

use \Firebase\JWT\JWT;
use CodeIgniter\RESTful\ResourceController;
use App\Models\M_Siswa;
use CodeIgniter\API\ResponseTrait;

class Auth extends BaseController
{
    use ResponseTrait;

    public function __construct()
    {
        $this->m_siswa = new M_Siswa();;
    }

    public function login()
    {
        $nis      = $this->request->getPost('nis');
        $password   = $this->request->getPost('password');

        $cek_login = $this->m_siswa->where(['nis' => $nis])->first();

        if (!is_null($cek_login) && password_verify($password, $cek_login['password'])) {
            unset($cek_login['password']);
            $temp = $cek_login['photo'];
            unset($cek_login['photo']);
            $secret_key = $this->secret_key;
            $issuer_claim = "THE_CLAIM"; // this can be the servername. Example: https://domain.com
            $audience_claim = "THE_AUDIENCE";
            $issuedat_claim = time(); // issued at
            $notbefore_claim = $issuedat_claim + 10; //not before in seconds
            $expire_claim = $issuedat_claim + 3600; // expire time in seconds
            $token = array(
                "iss" => $issuer_claim,
                "aud" => $audience_claim,
                "iat" => $issuedat_claim,
                "nbf" => $notbefore_claim,
                "exp" => $expire_claim,
                "data" => $cek_login
            );

            $token = JWT::encode($token, $secret_key);
            $isRegister = is_null($cek_login['nama']);
            $cek_login['photo'] = $temp;
            $output = [
                'status' => 200,
                'message' => $isRegister ? 'new register' : 'success login',
                'data' => [
                    "token" => $token,
                    "expireAt" => $expire_claim,
                    "isRegister" => $isRegister,
                    "user" => $cek_login
                ]
            ];

            return $this->respond($output, 200);
        } else if (is_null($cek_login)) {
            $password_hash = password_hash($password, PASSWORD_BCRYPT);

            $data = [
                'nis' => $nis,
                'password' => $password_hash,
            ];

            $save = $this->m_siswa->insert($data);

            $secret_key = $this->secret_key;
            $issuer_claim = "THE_CLAIM"; // this can be the servername. Example: https://domain.com
            $audience_claim = "THE_AUDIENCE";
            $issuedat_claim = time(); // issued at
            $notbefore_claim = $issuedat_claim + 10; //not before in seconds
            $expire_claim = $issuedat_claim + 3600; // expire time in seconds
            $token = array(
                "iss" => $issuer_claim,
                "aud" => $audience_claim,
                "iat" => $issuedat_claim,
                "nbf" => $notbefore_claim,
                "exp" => $expire_claim,
                "data" => $data
            );
            $token = JWT::encode($token, $secret_key);

            $output = [
                'status' => 200,
                'message' => 'new register',
                'data' => [
                    "token" => $token,
                    "expireAt" => $expire_claim,
                    "isRegister" => true,
                    "user" => $data
                ]
            ];
            return $this->respond($output, 200);
        } else {
            $output = [
                'status' => 401,
                'message' => 'failed authentication',
                'data' => null
            ];
            return $this->respond($output, 200);
        }
    }

    public function update()
    {
        // return $this->isValidToken();

        $token      = $this->getParseToken();
        $nama      = $this->request->getPost('nama');
        $id_kelas   = $this->request->getPost('idKelas');

        if ($this->isValidToken()) {
            $data = [
                'nama' => $nama,
                'id_kelas' => $id_kelas,
            ];

            $save = $this->m_siswa->update($token->data->nis, $data);

            $output = [
                'status' => 200,
                'message' => 'success',
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
