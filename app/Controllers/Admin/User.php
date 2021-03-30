<?php

namespace App\Controllers\Admin;

use App\Models\M_User;
use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

class User extends BaseController
{
    protected $m_user;
    use ResponseTrait;

    function __construct()
    {
        setTitle('Kelas');

        $this->m_user   = new M_User();
    }

    public function index()
    {
        return view("{$this->private}/absent/user");
    }

    public function ajax_user()
    {
        if (!$this->request->isAJAX()) {
            die('denied!');
        }

        $draw       = $this->request->getPost('draw');
        $length     = $this->request->getPost('length');
        $start      = $this->request->getPost('start');
        $search     = $this->request->getPost('search')['value'];

        $output     = [
            'draw'              => $draw,
            'recordsTotal'      => 0,
            'recordsFiltered'   => 0,
            'data'              => []
        ];

        $data   = $this->m_user;
        if ($search != '') {
            $data->like('nama', $search);
        }
        $query  = $data->orderBy('nama', 'ASC')->findAll($length, $start);

        if ($search != '') {
            $jum    = $this->m_user->like('nama', $search)->countAllResults();
            $output['recordsTotal'] = $output['recordsFiltered']  = $jum;
        } else {
            $output['recordsTotal'] = $output['recordsFiltered']  = $this->m_user->countAllResults();
        }

        $nomor_urut = $start + 1;
        foreach ($query as $v) {
            unset($v['password']);
            $param = base64_encode(json_encode($v));
            $button = '<a href="javascript:;" class="btn btn-primary" style="margin:2px 0px; width:50px" onclick="call_modal(\'edit\',  \'' . $param . '\')"><i class="fa fa-edit"></i></a>&nbsp;';
            $button .= '<a href="#" class="btn btn-danger" style="margin:2px 0px; width:50px" onclick="call_modal(\'delete\',  \'' . base64_encode(json_encode($v['id_user'])) . '\')"><i class="fa fa-times"></i></a></a>';
            $output['data'][]   = [
                $nomor_urut,
                $v['nama'],
                $v['username'],
                $v['level'],
                $button,
            ];
            $nomor_urut++;
        }

        $output[csrf_token()]   = csrf_hash();
        echo json_encode($output);
    }


    public function ajax_save_user()
    {
        if (!$this->request->isAJAX()) {
            die('denied!');
        }
        $post   = $this->request->getJSON();

        $id_user              = $post->id_user ?? null;
        $nama                 = $post->nama ?? null;
        $username             = $post->username ?? null;
        $level                = $post->level ?? null;
        $password             = $post->password ?? null;

        $data = [
            'nama'          => $nama,
            'username'      => $username,
            'level'         => $level,
        ];

        if ($id_user != '' || $id_user != null) {
            $data['id_user'] = $id_user;
        }
        if ($password != '' || $password != null) {
            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            $data['password'] = $password_hash;
        }

        $save = $this->m_user->save($data);

        $output = [];
        if (!$save) {
            $output['errors']   = $this->m_user->errors();
        }

        $output[csrf_token()]   = csrf_hash();
        echo json_encode($output);
    }

    public function ajax_delete_user()
    {
        if (!$this->request->isAJAX()) {
            die('denied!');
        }
        $id     = $this->request->getPost('id');
        $delete = $this->m_user->delete($id);

        if ($delete) {
            echo json_encode([csrf_token() => csrf_hash()]);
        }
    }
}
