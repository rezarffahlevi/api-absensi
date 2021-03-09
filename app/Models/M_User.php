<?php namespace App\Models;

use CodeIgniter\Model;

class M_User extends Model
{
    protected $table            = 'user';
    protected $primaryKey       = 'id_user';

    // protected $useSoftDeletes   = true;

    protected $allowedFields    = ['nama', 'username', 'level', 'password'];

    // protected $useTimestamps    = true;

    protected $validationRules  = [
        'id_user'           => 'required',
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