<?php

namespace App\Controllers\Admin;

use App\Models\M_User;
use App\Models\M_Absent;
use App\Models\M_Siswa;
use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Absent extends BaseController
{
    protected $m_user;
    protected $m_absent;
    protected $m_student;
    use ResponseTrait;

    function __construct()
    {
        setTitle('User');

        $this->m_user   = new M_User();
        $this->m_absent   = new M_Absent();
        $this->m_student   = new M_Siswa();
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
        $data['student'] = $this->m_student->where('id_kelas', $id)->find();
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
        $selected_date   = $this->request->getPost('selected_date');

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
        $query  = $data->join('kelas', 'id_kelas')->like('tgl', $selected_date)->orderBy('id', 'DESC')->findAll($length, $start);

        if ($search != '') {
            $jum    = $this->m_absent->join('siswa s', 'nis')->join('kelas', 'id_kelas')->where('id_kelas', $id_kelas)->like('nis', $search)->like('tgl', $selected_date)->countAllResults();
            $output['recordsTotal'] = $output['recordsFiltered']  = $jum;
        } else {
            $output['recordsTotal'] = $output['recordsFiltered']  = $this->m_absent->join('siswa s', 'nis')->join('kelas', 'id_kelas')->like('tgl', $selected_date)->where('id_kelas', $id_kelas)->countAllResults();
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
            'tgl'       => $tgl
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

    public function export()
    {
        // if (!$this->request->isAJAX()) {
        //     die('denied!');
        // }
        $get   = $this->request->getGet();

        $id_kelas         = $get['id_kelas'] ?? null;
        $kelas            = $get['kelas'] ?? null;
        $date             = $get['date'] ?? null;

        $absent = $this->m_absent->join('siswa', 'nis')->where([
            'id_kelas' => $id_kelas,
            'MONTH(tgl)' => date_format(date_create($date), 'm'),
            'YEAR(tgl)' => date_format(date_create($date), 'Y')
        ])->findAll();
        $student = $this->m_student->where('id_kelas', $id_kelas)->findAll();

        $spreadsheet = new Spreadsheet();
        // tulis header/nama kolom 
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'ABSENSI KEHADIRAN SISWA')
            ->setCellValue('A2', "SMK AS-SU'UDIYYAH")
            ->setCellValue('A4', 'Kelas')
            ->setCellValue('B4', $kelas)
            ->setCellValue('A5', 'Tahun')
            ->setCellValue('B5',  date_format(date_create($date), 'F-Y'))
            ->setCellValue('A6', 'No')
            ->setCellValue('B6', 'Nama Siswa')
            ->setCellValue('C6', 'Tanggal');

        $max_date = substr(date("Y-m-t", strtotime($date)), -2);

        $letter = 'C';
        for ($i = 1; $i <= $max_date; $i++) {
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue($letter . '7', $i);
            if ($this->isWeekend('2021-03-' . $i)) {
                $spreadsheet->getActiveSheet()->getStyle($letter . '7')
                    ->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
            }
            $letter++;
        }

        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue($letter++ . '7', 'S');
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue($letter++ . '7', 'I');
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue($letter++ . '7', 'A');

        $column = 8;
        // tulis data mobil ke cell
        $no = 1;
        foreach ($student as $data) {
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $column, $no)
                ->setCellValue('B' . $column, $data['nama']);

            $student_absent = $this->search_student($data['nis'], $absent);
            $letter1 = 'C';
            $a = 0;
            $s = 0;
            $iz = 0;
            $h = 0;
            for ($i = 1; $i <= $max_date + 3; $i++) {
                $tgl = $this->search_tgl($i, $student_absent);
                // echo substr($tgl['tgl'], 8, 2).' ';
                if ($this->isWeekend(substr($date, 0, 8) . $i)) {
                    $spreadsheet->setActiveSheetIndex(0)
                        ->setCellValue($letter1 . $column, 'Libur');

                    $spreadsheet->getActiveSheet()->getStyle($letter1 . $column)
                        ->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
                } else if ($i == substr($tgl['tgl'], 8, 2)) {
                    $spreadsheet->setActiveSheetIndex(0)
                        ->setCellValue($letter1 . $column, strtoupper(substr($tgl['status'], 0, 1)));
                    if ($tgl['status'] == 'sakit') {
                        $s++;
                    } else if ($tgl['status'] == 'hadir') {
                        $h++;
                    } else {
                        $iz++;
                    }
                } else {
                    $spreadsheet->setActiveSheetIndex(0)
                        ->setCellValue($letter1 . $column, 'A');
                    $a++;
                }

                // SAKIT, IZIN, ALFA
                if ($i == $max_date + 1) {
                    $spreadsheet->setActiveSheetIndex(0)
                        ->setCellValue($letter1 . $column, $s);
                } else if ($i == $max_date + 2) {
                    $spreadsheet->setActiveSheetIndex(0)
                        ->setCellValue($letter1 . $column, $iz);
                } else if ($i == $max_date + 3) {
                    $spreadsheet->setActiveSheetIndex(0)
                        ->setCellValue($letter1 . $column, $a);
                }

                $letter1++;
            }

            // print_r($student_absent);

            $column++;
            $no++;
        }

        // tulis dalam format .xlsx
        $writer = new Xlsx($spreadsheet);
        $fileName = 'Data Absensi ' . $kelas . ' ' . date('F-Y', strtotime($date));


        // Redirect hasil generate xlsx ke web client
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $fileName . '.xlsx');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');

        // $output['spreadsheet'] = $spreadsheet;
        // $output['fileName'] = $fileName;
        // $output[csrf_token()]   = csrf_hash();
        // echo json_encode($output);
    }

    function search_student($id, $array)
    {
        $arr = [];
        foreach ($array as $key => $val) {
            if ($val['nis'] === $id) {
                // return $val;
                $arr[] = $val;
            }
        }
        return $arr;
    }
    function search_tgl($id, $array)
    {
        $arr = [];
        foreach ($array as $key => $val) {
            if (substr($val['tgl'], 8, 2) == $id) {
                return $val;
            }
        }
        return ['tgl' => '2020-12-43', 'status' => 'alfa'];
    }
    function isWeekend($date)
    {
        return (date('N', strtotime($date)) >= 6);
    }
}
