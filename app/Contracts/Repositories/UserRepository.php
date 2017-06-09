<?php

namespace App\Contracts\Repositories;

use App\Entities\UserEntity;

interface UserRepository
{
    /**
     * @param UserEntity $user
     * @return UserEntity
     */
    static public function create(UserEntity $user);

    /**
     * @param int $id
     * @return UserEntity
     */
//    static public function getById($id);

    /**
     * @param string $status
     * @return UserEntity
     */
//    static public function getByStatus($status);

    /**
     * @param string $username
     * @return UserEntity
     */
//    static public function getByUsername($username);

    /**
     * @param string $apiToken
     * @return UserEntity
     */
//    static public function getByApiToken($apiToken);

    /**
     * @return bool
     */
//    static public function isActive();
}
