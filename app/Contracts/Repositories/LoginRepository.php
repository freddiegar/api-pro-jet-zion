<?php

namespace App\Contracts\Repositories;

use App\Entities\UserEntity;

interface LoginRepository
{
    /**
     * @param int $id
     * @param array $user
     * @return bool
     */
    static public function updateUserLastLogin($id, $user);

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
