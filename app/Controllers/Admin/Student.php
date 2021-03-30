<?php

namespace App\Controllers\Admin;

use App\Models\M_Siswa;
use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

class Student extends BaseController
{
    protected $m_student;
    use ResponseTrait;

    function __construct()
    {
        setTitle('Kelas');

        $this->m_student   = new M_Siswa();
    }

    public function index()
    {
        $data['id'] = 0;
        $data['kelas'] = "Semua Kelas";
        return view("{$this->private}/absent/student", $data);
    }

    public function kelas($id = null)
    {
        $data['id'] = $id;
        $data['kelas'] = 'Kelas ' . $this->search_kelas($id, session('kelas'))['kelas'];
        return view("{$this->private}/absent/student", $data);
    }

    function search_kelas($id, $array)
    {
        foreach ($array as $key => $val) {
            if ($val['id_kelas'] === $id) {
                return $val;
            }
        }
        return null;
    }

    public function ajax_student()
    {
        if (!$this->request->isAJAX()) {
            die('denied!');
        }

        $draw       = $this->request->getPost('draw');
        $length     = $this->request->getPost('length');
        $start      = $this->request->getPost('start');
        $search     = $this->request->getPost('search')['value'];
        $id_kelas   = $this->request->getPost('id');

        $output     = [
            'draw'              => $draw,
            'recordsTotal'      => 0,
            'recordsFiltered'   => 0,
            'data'              => []
        ];

        $data   = $this->m_student->join('kelas', 'id_kelas');
        if (!$id_kelas == 0) {
            $data->where('id_kelas', $id_kelas);
        }
        if ($search != '') {
            $data->like('nis', $search);
        }
        $query  = $data->orderBy('nama', 'ASC')->findAll($length, $start);

        if ($search != '') {
            $jum    = $this->m_student->like('nis', $search)->countAllResults();
            $output['recordsTotal'] = $output['recordsFiltered']  = $jum;
        } else {
            $output['recordsTotal'] = $output['recordsFiltered']  = $this->m_student->countAllResults();
        }

        $nomor_urut = $start + 1;
        foreach ($query as $v) {
            $param = base64_encode(json_encode($v));
            $button = '<a href="javascript:;" class="btn btn-primary" style="margin:2px 0px; width:50px" onclick="call_modal(\'edit\',  \'' . $param . '\')"><i class="fa fa-edit"></i></a>&nbsp;';
            $button .= '<a href="#" class="btn btn-danger" style="margin:2px 0px; width:50px" onclick="call_modal(\'delete\',  \'' . base64_encode(json_encode($v['nis'])) . '\')"><i class="fa fa-times"></i></a></a>';
            $output['data'][]   = [
                $nomor_urut,
                $v['nis'],
                $v['nama'],
                $v['kelas'],
                $button,
            ];
            $nomor_urut++;
        }

        $output[csrf_token()]   = csrf_hash();
        echo json_encode($output);
    }


    public function ajax_save_student()
    {
        if (!$this->request->isAJAX()) {
            die('denied!');
        }
        $post   = $this->request->getJSON();

        $nis                  = $post->nis ?? null;
        $nama                 = $post->nama ?? null;
        $id_kelas             = $post->id_kelas ?? null;
        $password             = $post->password ?? null;
        $type                 = $post->type ?? null;

        $data = [
            'nis'           => $nis,
            'nama'          => $nama,
            'id_kelas'      => $id_kelas,
            'photo'         => null
        ];

        if ($password != '' || $password != null) {
            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            $data['password'] = $password_hash;
        }

        if ($type == 'add') {
            $save = $this->m_student->insert($data);
            if ($save < 0) {
                $output['errors']   = $this->m_student->errors();
            }
        } else {
            $save = $this->m_student->update($nis, $data);
            if (!$save) {
                $output['errors']   = $this->m_student->errors();
            }
        }

        $output = [];

        $output[csrf_token()]   = csrf_hash();
        $output['save']   = $save;
        echo json_encode($output);
    }

    public function ajax_delete_student()
    {
        $id     = $this->request->getPost('id');
        $delete = $this->m_student->delete($id);

        if ($delete) {
            echo json_encode([csrf_token() => csrf_hash()]);
        }
    }
}
