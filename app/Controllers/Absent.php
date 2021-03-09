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
        $token = $this->getParseToken();
        $post = $this->request->getJSON();

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
            $newName = $nis . '_' . strtotime($tgl) . '.png';
            file_put_contents('images/' . $newName, $decoded);

            $data = [
                'tgl'       => $tgl,
                'nis'       => $nis,
                'status'    => $status,
                'notes'     => $notes,
                'photo'     => $newName,
                'latitude'  => $latitude,
                'longitude' => $longitude,
                'alamat'    => $alamat,
            ];

            if (isset($id))
            {
                $data['id'] = $id;
                unset($data['tgl']);
            }

            $output = [
                'code' => $this->constant->success,
                'message' => null,
                'data' =>  null
            ];

            if ($data['status'] == 'hadir' && (is_null($data['latitude']) || $data['latitude'] == "" || is_null($data['longitude']) || $data['longitude'] == "")) {
                $output['code'] = $this->constant->error;
                $output['message'] = 'Failed get your location';
            } else {
                $save = $this->m_absent->save($data);
                if ($save) {
                    $output['message']  = 'Success absent at ' . $tgl;
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
