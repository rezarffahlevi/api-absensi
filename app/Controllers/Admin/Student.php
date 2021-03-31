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
            $jum    = $this->m_student->where('id_kelas', $id_kelas)->like('nis', $search)->countAllResults();
            $output['recordsTotal'] = $output['recordsFiltered']  = $jum;
        } else {
            $output['recordsTotal'] = $output['recordsFiltered']  = $this->m_student->where('id_kelas', $id_kelas)->countAllResults();
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
        $id           = $this->request->getPost('id');
        $id_kelas     = $this->request->getPost('id_kelas') ?? null;

        if ($id == 0) {
            $delete = $this->m_student->where('id_kelas', $id_kelas)->delete();
        } else
            $delete = $this->m_student->delete($id);

        if ($delete) {
            echo json_encode([csrf_token() => csrf_hash()]);
        }
    }

    public function import()
    {
        $file = $this->request->getFile('file_siswa');
        $id_kelas = $this->request->getPost('id_kelas');

        $data = array(
            'file_siswa'           => $file,
        );

        // ambil extension dari file excel
        $extension = $file->getClientExtension();

        // format excel 2007 ke bawah
        if ('xls' == $extension) {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
            // format excel 2010 ke atas
        } else {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        }

        $spreadsheet = $reader->load($file);
        $data = $spreadsheet->getActiveSheet()->toArray();

        $data_siswa = [];
        foreach ($data as $col => $row) {

            // lewati baris ke 0 pada file excel
            // dalam kasus ini, array ke 0 adalahpara title
            if ($col == 0) {
                continue;
            }

            $data_siswa[] = [
                'nis' => $row[0],
                'nama' => $row[1],
                'id_kelas' => $id_kelas,
                'password' => password_hash($row[0] == '' ? $row[0] : $row[3], PASSWORD_BCRYPT),
            ];
        }

        // print_r($data_siswa);

        $notif[]          = ['type' => 'success', 'msg' => 'Import data siswa berhasil'];

        $save = $this->m_student->insertBatch($data_siswa);
        if ($save < 0) {
            $notif[0]['msg']   = $this->m_student->errors();
        }
        return redirect()->back()->withInput()->with('notif', $notif);
    }
}
