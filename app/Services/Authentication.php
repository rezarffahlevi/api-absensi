<?php namespace App\Services;

class Authentication
{
    public function login($user_data)
    {
        session()->set('user', $user_data);
        session()->set('logged_in', TRUE);
        
        return $user_data;
    }

    public function logout()
    {
        session()->remove('user');
        session()->remove('logged_in');
    }

    public function logged()
    {
        // TODO: Validation?
        if (!session()->has('logged_in')) {
            return FALSE;
        }

        return TRUE;
    }
}
