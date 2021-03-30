<?php

namespace App\Controllers;

use \Firebase\JWT\JWT;
use App\Models\M_Siswa;
use App\Models\M_User;
use App\Models\M_Kelas;
use CodeIgniter\API\ResponseTrait;

class Auth extends BaseController
{
    use ResponseTrait;
    protected $auth;

    public function __construct()
    {
        $this->m_siswa = new M_Siswa();
        $this->m_user = new M_User();
        $this->m_kelas = new M_Kelas();
        $this->auth = service('auth');
    }

    public function index()
    {
        if ($this->auth->logged()) {
            return redirect()->to(base_url('/admin/home'));
        }
        helper('form');
        setTitle('Login');
        return view("{$this->public}/auth/login");
    }

    public function login_attemp()
    {
        $login          = FALSE;
        $redirect       = redirect()->to(base_url('/auth'));
        $notif          = ['type' => 'warning', 'msg' => 'Invalid Credential'];
        $user           = [];

        if ($this->request->getMethod(TRUE) == 'POST') {
            $username   = $this->request->getPost('username');
            $password   = $this->request->getPost('password');
            $remember   = $this->request->getPost('remember');

            // define validations
            $rules = [
                'username'  => 'required',
                'password'  => 'required|min_length[3]',
            ];
            $messages = [
                'username'  => [
                    'required'      => 'Silahkan masukkan username.',
                ],
                'password'  => [
                    'required'      => 'Silahkan masukkan password.',
                    'min_length'    => 'Silahkan masukkan password dengan min. {param} karakter.'
                ],
            ];

            if ($this->validate($rules, $messages)) {
                $notif  = ['type' => 'warning', 'msg' => 'Username atau password anda salah.'];
                $user   = $this->m_user->where(['username' => $username])->first();
                if (!empty($user)) {
                    if (!is_null($user) && password_verify($password, $user['password'])) {
                        $notif      = [];
                        $notif[]    = ['type' => 'success', 'msg' => 'Berhasil login.'];
                        $redirect   = redirect()->to(base_url('admin/home'));
                        $login = TRUE;
                    } else {
                        $user       = [];
                    }
                }
            } else {
                $notif['msg']   = parseErrorValidation($this->validator->getErrors());
            }

            if ($login == TRUE) {
                $user = $this->auth->login($user);
                session()->set('kelas', $this->m_kelas->findAll());

                // remember me
                if ($remember == "1") {
                    $remember = [
                        'name'      => 'remember',
                        'value'     => 'true',
                        'expire'    => \time() + 3600,
                        'httponly'  => FALSE
                    ];
                    $user_id = [
                        'name'      => 'user_id',
                        'value'     => base64_encode($user['id']),
                        'expire'    => \time() + 3600,
                        'httponly'  => FALSE
                    ];

                    $redirect->setCookie($remember)->setCookie($user_id);
                }
            }
        }

        return $redirect->withInput()->with('notif', $notif);
    }

    public function logout_attempt()
    {
        $redirect = redirect()->to(base_url('/auth'));

        if (!$this->auth->logged()) {
            return $redirect;
        }

        $this->auth->logout();

        return $redirect
            ->deleteCookie('user_id')
            ->deleteCookie('remember')
            ->with('notif', ['type' => 'success', 'msg' => 'Anda berhasil logout.']);
    }


    // ==== API ==== //
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
            $isRegister = false;
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
        } else if (false) {
            // } else if (is_null($cek_login)) {
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
                'message' => 'Invalid username / password',
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
            if (!$save) {
                $output['code'] = $this->constant->error;
                $output['error'] = $save->getMessage();
            }
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
