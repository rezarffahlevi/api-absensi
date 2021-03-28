<?php

namespace App\Controllers;

use App\Models\M_Absent;
use CodeIgniter\API\ResponseTrait;

class Absent extends BaseController
{
    use ResponseTrait;

    public function __construct()
    {
        $this->m_absent = new M_Absent();
    }

    public function save()
    {
        $token  = $this->getParseToken();
        $post   = $this->request->getJSON();
        $next   = false;

        $id             = $post->id ?? null;
        $longitude      = $post->longitude ?? null;
        $latitude       = $post->latitude ?? null;
        $alamat         = $post->alamat ?? null;
        $status         = $post->status ?? null;
        $notes          = $post->notes ?? null;
        $photo          = $post->photo ?? null;
        $tgl            = $post->date ?? date("Y-m-d H:i:s");

        if ($this->isValidToken()) {
            $nis     = $token->data->nis;
            $decoded = base64_decode($photo);
            $new_name = $nis . '_' . strtotime($tgl) . '.png';
            file_put_contents('images/' . $new_name, $decoded);

            $data = [
                'tgl'       => $tgl,
                'nis'       => $nis,
                'status'    => $status,
                'notes'     => $notes,
                'photo'     => $new_name,
                'latitude'  => $latitude,
                'longitude' => $longitude,
                'alamat'    => $alamat,
            ];

            $output = [
                'code'      => $this->constant->success,
                'message'   => null,
                'data'      =>  null
            ];

            if (isset($id)) {
                $data['id'] = $id;
                unset($data['tgl']);
            }

            $isAbsent = $this->m_absent->where('nis', $nis)->like('tgl', date('Y-m-d'))->countAllResults();

            if ($data['status'] == 'hadir') {
                if (is_null($data['latitude']) || $data['latitude'] == "" || is_null($data['longitude']) || $data['longitude'] == "") {
                    $output['code']     = $this->constant->error;
                    $output['message']  = 'Gagal mendapatkan alamat';
                } else if (date("H") < 7) {
                    $output['code'] = $this->constant->error;
                    $output['message'] = 'Absen dimulai jam 7-8 AM';
                } else {
                    $next = true;
                }
            } else {
                if ($data['status'] == 'izin' && (is_null($data['notes']) || $data['notes'] == '')) {
                    $output['code'] = $this->constant->error;
                    $output['message'] = 'Wajib mengisi alasan';
                } else if ($data['status'] == 'sakit' && (is_null($data['photo']) || $data['photo'] == '')) {
                    $output['code'] = $this->constant->error;
                    $output['message'] = 'Wajib mengupload surat dokter';
                } else {
                    $next = true;
                }
            }

            if ($isAbsent > 0) {
                // if ($isAbsent == 1 && date('H') < 11) {
                //     $output['code']     = $this->constant->error;
                //     $output['message']  = 'Anda sudah absen hari ini';
                //     $next = false;
                // }
                // else if ($isAbsent == 1 && date('H') >= 12 && date('H') <= 14) {
                //     $next = true;
                // } else if ($isAbsent == 2 && date('H') > 14 && date('H') < 21) {
                //     $next = true;
                // } 
                // else {
                    $output['code']     = $this->constant->error;
                    $output['message']  = 'Anda sudah absen hari ini';
                    $next = false;
                // }
            }


            if (isset($id)) {
                $next = true;
            }

            if ($next) {
                $save = $this->m_absent->save($data);
                if ($save) {
                    $is_late = $isAbsent < 1 && date('H') > 8;
                    $late = date_diff(date_create($tgl), date_create(date("Y-m-d") . ' 08:00:00'));
                    $msg_late  = "Absen berhasil, anda terlambat " . ($late->h < 1  ? "" : $late->h . " jam ") . $late->i . " menit " . $late->s . " detik";
                    
                    $output['code'] = $this->constant->success;
                    $output['message']  = $is_late ? $msg_late : 'Absen berhasil pada ' . $tgl;
                    $output['data']     = $data;
                } else {
                    $output['code'] = $this->constant->error_array;
                    $output['message']  = 'failed absent ' . $tgl;
                    $output['error'] = $save->getMessage();
                }
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
