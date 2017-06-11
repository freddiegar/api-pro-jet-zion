<?php

namespace App\Http\Controllers;

use App\Managers\LoginManager;

class LoginController extends Controller
{
    /**
     * @return LoginManager
     */
    protected function manager()
    {
        return app(LoginManager::class);
    }

    /**
     * @return array
     */
    public function login()
    {
        return $this->manager()->requestValidate()->login();
    }
}
