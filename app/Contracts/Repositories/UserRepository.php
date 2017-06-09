<?php

namespace App\Contracts\Repositories;

use App\Entities\UserEntity;

interface UserRepository
{
    /**
     * @param array $user
     * @return mixed|UserEntity
     */
    static public function create($user);

    /**
     * @param int $id
     * @return mixed|UserEntity
     */
//    static public function getById($id);

    /**
     * @param string $username
     * @return mixed|UserEntity
     */
//    static public function getByUsername($username);

    /**
     * @param string $apiToken
     * @return mixed|UserEntity
     */
    static public function getByApiToken($apiToken);

    /**
     * @return bool
     */
//    static public function isActive();
}
