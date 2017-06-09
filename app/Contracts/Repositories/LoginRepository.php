<?php

namespace App\Contracts\Repositories;

use App\Entities\UserEntity;
use Illuminate\Http\Request;

interface LoginRepository
{
    /**
     * @param UserEntity $user
     * @param Request $request
     * @return bool
     */
    static public function updateUserLastLogin(UserEntity $user, Request $request);

    /**
     * @param string $username
     * @return mixed|UserEntity
     */
    static public function getUserPasswordByUsername($username);

    /**
     * @param $id
     * @return mixed|UserEntity
     */
    static public function getUserApiToken($id);
}
