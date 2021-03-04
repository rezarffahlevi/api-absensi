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

        $nis            = $token->data->nis;
        $longitude      = $post->longitude;
        $latitude       = $post->latitude;
        $photo          = $post->photo;

        $tgl = date("Y-m-d H:i:s");

        if ($this->isValidToken()) {
            $decoded = base64_decode($photo);
            $newName = $nis . '_' . strtotime($tgl) . '.png';
            file_put_contents('images/' . $newName, $decoded);

            $data = [
                'tgl' => $tgl,
                'nis' => $nis,
                'photo' => $newName,
                'latitude' => $latitude,
                'longitude' => $longitude,
            ];

            $output = [
                'code' => $this->constant->success,
                'message' => null,
                'data' =>  null
            ];

            if (is_null($data['latitude']) || $data['latitude'] == "" || is_null($data['longitude']) || $data['longitude'] == "") {
                $output['message'] = 'Failed get your location';
            } else {
                $save = $this->m_absent->insert($data);
                if($save)
                {
                    $output['message']  = 'Success absent at ' . $tgl;
                    $output['data']     = $data;
                }
                else {
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
