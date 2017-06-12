<?php

namespace App\Http\Controllers;

use App\Managers\LoginManager;

/**
 * Class LoginController
 * @package App\Http\Controllers
 */
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
