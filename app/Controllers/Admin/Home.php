<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Home extends BaseController
{
    protected $auth;

    public function __construct()
    {
        $this->auth = service('auth');
    }
    

    public function index()
    {
        setTitle('Home');
        return view("{$this->private}/dashboard/index", ['data' => [], 'user' => []]);
    }

}
