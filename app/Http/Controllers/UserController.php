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

    /**
     * @param int $id
     * @return array
     */
    public function read($id)
    {
        return $this->manager()->read($id);
    }

    /**
     * @param int $id
     * @return array
     */
    public function update($id)
    {
        return $this->manager()->requestValidate()->update($id);
    }
}
