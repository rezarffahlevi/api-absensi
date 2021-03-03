<?php

namespace App\Models;

use CodeIgniter\Model;

class M_Kelas extends Model
{
    protected $table            = 'kelas';
    protected $primaryKey       = 'id_kelas';

    // protected $useSoftDeletes   = true;

    protected $allowedFields    = ['id_kelas', 'nama'];

    // protected $useTimestamps    = true;

    protected $validationRules  = [
        'id_kelas'           => 'required',
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
