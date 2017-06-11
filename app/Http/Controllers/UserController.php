<?php

namespace App\Http\Controllers;

use App\Managers\UserManager;

class UserController extends Controller
{
    /**
     * @return UserManager
     */
    protected function manager()
    {
        return app(UserManager::class);
    }
    /**
     * @return array
     */
    public function create()
    {
        return $this->manager()->requestValidate()->create();
    }
}
