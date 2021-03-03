<?php

namespace App\Controllers;

use \Firebase\JWT\JWT;
use App\Models\M_Siswa;
use CodeIgniter\API\ResponseTrait;

class Auth extends BaseController
{
    use ResponseTrait;

    public function __construct()
    {
        $this->m_siswa = new M_Siswa();
    }

    public function login()
    {
        $post = $this->request->getJSON();

        $nis        = $post->nis;
        $password   = $post->password;
        $cek_login  = $this->m_siswa->where(['nis' => $nis])->first();

        if (!is_null($cek_login) && password_verify($password, $cek_login['password'])) {
            unset($cek_login['password']);
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
            $output = [
                'code' => $this->constant->success,
                'message' => $isRegister ? 'new user register' : 'success login',
                'data' => [
                    "token" => $token,
                    "expireAt" => $expire_claim,
                    "isNewUser" => $isRegister,
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
            unset($data['password']);
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
                'code' => $this->constant->success,
                'message' => 'new register',
                'data' => [
                    "token" => $token,
                    "expireAt" => $expire_claim,
                    "isNewUser" => true,
                    "user" => $data
                ]
            ];
            return $this->respond($output, 200);
        } else {
            $output = [
                'code' => $this->constant->error,
                'message' => 'Wrong username / password',
                'data' => null
            ];
            return $this->respond($output, 200);
        }
    }

    public function update()
    {
        $token = $this->getParseToken();
        $post = $this->request->getJSON();

        $nis            = $token->data->nis;
        $nama           = $post->nama;
        $id_kelas       = $post->idKelas;

        if ($this->isValidToken()) {
            $data = [
                'nama' => $nama,
                'id_kelas' => $id_kelas,
            ];
            
            $output = [
                'code' => $this->constant->success,
                'message' => 'success',
                'data' =>  $data
            ];

            $save = $this->m_siswa->update($nis, $data);
            if(!$save)
            {
                $output['code'] = $this->constant->error;
                $output['error'] = $save->getMessage();
            }
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
