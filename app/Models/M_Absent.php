<?php namespace App\Models;

use CodeIgniter\Model;

class M_Absent extends Model
{
    protected $table            = 'absensi';
    protected $primaryKey       = 'id';

    // protected $useSoftDeletes   = true;

    protected $allowedFields    = ['id', 'tgl', 'nis', 'photo', 'latitude', 'longitude'];

    // protected $useTimestamps    = true;

    protected $validationRules  = [
        'nis'           => 'required',
    ];

    // protected $createdField = 'created_at';
    // protected $updatedField = 'updated_at';
    // protected $deletedField = 'deleted_at';

    protected $validationMessages = [
        // 'nis' => [
        //     'required'      => 'Silahkan masukkan instansi.',
        // ],
    ];

    protected $skipValidation = false;

}