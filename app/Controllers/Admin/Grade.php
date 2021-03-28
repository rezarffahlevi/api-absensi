<?php

namespace App\Controllers\Admin;

use App\Models\M_Kelas;
use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

class Grade extends BaseController
{
    protected $m_kelas;
    use ResponseTrait;

    function __construct()
    {
        setTitle('Kelas');

        $this->m_kelas   = new M_Kelas();
    }

    public function index()
    {
        return view("{$this->private}/absent/grade");
    }

    public function ajax_grade()
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

        $data   = $this->m_kelas;
        if ($search != '') {
            $data->like('kelas', $search);
        }
        $query  = $data->orderBy('kelas', 'ASC')->findAll($length, $start);

        if ($search != '') {
            $jum    = $this->m_kelas->like('kelas', $search)->countAllResults();
            $output['recordsTotal'] = $output['recordsFiltered']  = $jum;
        } else {
            $output['recordsTotal'] = $output['recordsFiltered']  = $this->m_kelas->countAllResults();
        }

        $nomor_urut = $start + 1;
        foreach ($query as $v) {
            $param = base64_encode(json_encode($v));
            $button = '<a href="javascript:;" class="btn btn-primary" style="margin:2px 0px; width:50px" onclick="call_modal(\'edit\',  \'' . $param . '\')"><i class="fa fa-edit"></i></a>&nbsp;';
            $button .= '<a href="#" class="btn btn-danger" style="margin:2px 0px; width:50px" onclick="call_modal(\'delete\',  \'' . base64_encode(json_encode($v['id_kelas'])) . '\')"><i class="fa fa-times"></i></a></a>';
            $output['data'][]   = [
                $nomor_urut,
                $v['kelas'],
                $button,
            ];
            $nomor_urut++;
        }

        $output[csrf_token()]   = csrf_hash();
        echo json_encode($output);
    }

    
    public function ajax_save_grade()
    {
        if (!$this->request->isAJAX()) {
            die('denied!');
        }
        $post   = $this->request->getJSON();

        $id_kelas             = $post->id_kelas ?? null;
        $kelas                = $post->kelas ?? null;

        $data = [
            'kelas'          => $kelas,
        ];

        if ($id_kelas != '' || $id_kelas != null) {
            $data['id_kelas'] = $id_kelas;
        }

        $save = $this->m_kelas->save($data);

        $output = [];
        if (!$save) {
            $output['errors']   = $this->m_kelas->errors();
        }

        session()->set('kelas', $this->m_kelas->findAll());
        
        $output[csrf_token()]   = csrf_hash();
        echo json_encode($output);
    }

    public function ajax_delete_grade()
    {
        $id     = $this->request->getPost('id');
        $delete = $this->m_kelas->delete($id);

        if ($delete) {
            session()->set('kelas', $this->m_kelas->findAll());
            
            echo json_encode([csrf_token() => csrf_hash()]);
        }
    }
}
