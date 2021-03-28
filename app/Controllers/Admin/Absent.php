<?php

namespace App\Controllers\Admin;

use App\Models\M_User;
use App\Models\M_Absent;
use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

class Absent extends BaseController
{
    protected $m_user;
    protected $m_absent;
    use ResponseTrait;

    function __construct()
    {
        setTitle('User');

        $this->m_user   = new M_User();
        $this->m_absent   = new M_Absent();
    }

    public function index()
    {
        $data['id'] = 0;
        $data['kelas'] = "Semua Kelas";
        return view("{$this->private}/absent/index", $data);
    }

    public function kelas($id = null)
    {
        $data['id'] = $id;
        $data['kelas'] = 'Kelas ' . $this->search_kelas($id, session('kelas'))['kelas'];
        return view("{$this->private}/absent/index", $data);
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

    public function ajax_absent()
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

        $data   = $this->m_absent->select('*, absensi.photo as pict')->join('siswa s', 'nis');
        if (!$id_kelas == 0) {
            $data->where('id_kelas', $id_kelas);
        }
        if ($search != '') {
            $data->like('nis', $search);
        }
        $query  = $data->join('kelas', 'id_kelas')->orderBy('id', 'DESC')->findAll($length, $start);

        if ($search != '') {
            $jum    = $this->m_absent->like('nis', $search)->countAllResults();
            $output['recordsTotal'] = $output['recordsFiltered']  = $jum;
        } else {
            $output['recordsTotal'] = $output['recordsFiltered']  = $this->m_absent->countAllResults();
        }

        $nomor_urut = $start + 1;
        foreach ($query as $v) {
            unset($v['password']);
            $param = base64_encode(json_encode($v));
            $button = '<a href="javascript:;" class="btn btn-info" style="margin:2px 0px; width:50px" onclick="call_modal(\'detail\',  \'' . $param . '\')"><i class="fa fa-eye"></i></a>&nbsp;';
            $button .= '<a href="javascript:;" class="btn btn-primary" style="margin:2px 0px; width:50px" onclick="call_modal(\'edit\',  \'' . $param . '\')"><i class="fa fa-edit"></i></a>&nbsp;';
            $button .= '<a href="#" class="btn btn-danger" style="margin:2px 0px; width:50px" onclick="call_modal(\'delete\',  \'' . base64_encode(json_encode($v['id'])) . '\')"><i class="fa fa-times"></i></a></a>';
            // $button .= '<a href="'.admin_url('employee/delete_user/'.$v['id']).'" class="btn btn-danger"><i class="fa fa-times"></i></a></a>';
            $class = ($v['status'] == 'alfa') ? 'danger' : ($v['status'] == 'izin' ? 'secondary' : ($v['status'] == 'sakit' ? 'warning' : 'primary'));
            $status = '<span class="badge badge-pill badge-' . $class . '">' . ucfirst($v['status']) . '</span>';
            $output['data'][]   = [
                $nomor_urut,
                $v['nis'],
                $v['nama'],
                $v['tgl'],
                $status,
                $button,
            ];
            $nomor_urut++;
        }

        $output[csrf_token()]   = csrf_hash();
        echo json_encode($output);
    }

    
    public function ajax_save_absent()
    {
        if (!$this->request->isAJAX()) {
            die('denied!');
        }
        $post   = $this->request->getJSON();

        $id             = $post->id ?? null;
        $nis            = $post->nis ?? null;
        $longitude      = $post->longitude ?? null;
        $latitude       = $post->latitude ?? null;
        $alamat         = $post->alamat ?? null;
        $status         = $post->status ?? null;
        $notes          = $post->notes ?? null;
        $photo          = $post->photo ?? null;
        $tgl            = $post->date ?? date("Y-m-d H:i:s");

        $data = [
            'nis'       => $nis,
            'status'    => $status,
            'notes'     => $notes,
        ];

        if ($id) {
            $data['id'] = $id;
        }

        $save = $this->m_absent->save($data);

        $output = [];
        if (!$save) {
            $output['errors']   = $this->m_absent->errors();
        }

        $output[csrf_token()]   = csrf_hash();
        echo json_encode($output);
    }

    public function ajax_submit_user()
    {
        if (!$this->request->isAJAX()) {
            die('denied!');
        }

        $name       = $this->request->getPost('name');
        $email      = $this->request->getPost('email');
        $address    = $this->request->getPost('address');

        $data = [
            'name'      => $name,
            'email'     => $email,
            'password'  => '1234',
            'address'   => $address,
        ];

        if ($this->request->getPost('id')) {
            $data['id'] = $this->request->getPost('id');
        }

        $save = $this->m_user->save($data);

        $output = [];
        if (!$save) {
            $output['errors']   = $this->m_user->errors();
        }

        $output[csrf_token()]   = csrf_hash();
        echo json_encode($output);
    }

    public function ajax_delete_absent()
    {
        $id     = $this->request->getPost('id');
        $delete = $this->m_absent->delete($id);

        if ($delete) {
            echo json_encode([csrf_token() => csrf_hash()]);
        }
    }
}
