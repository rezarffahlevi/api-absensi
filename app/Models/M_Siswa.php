<?php namespace App\Models;

use CodeIgniter\Model;

class M_Siswa extends Model
{
    protected $table            = 'siswa';
    protected $primaryKey       = 'nis';

    // protected $useSoftDeletes   = true;

    protected $allowedFields    = ['nis', 'nama', 'id_kelas', 'password', 'photo'];

    // protected $useTimestamps    = true;

    protected $validationRules  = [
        // 'nis'           => 'required',
    ];

    // protected $createdField = 'created_at';
    // protected $updatedField = 'updated_at';
    // protected $deletedField = 'deleted_at';

    protected $validationMessages = [
        // 'nis' => [
        //     'required'      => 'Silahkan masukkan instansi.',
        // ],
    ];

    protected $skipValidation = true;

}