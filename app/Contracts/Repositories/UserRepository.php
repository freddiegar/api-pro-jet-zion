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
     * @param UserEntity $user
     * @return UserEntity
     */
    static public function updateLastLogin($user);

    /**
     * @param $id
     * @return UserEntity
     */
    static public function getById($id);

    /**
     * @param $status
     * @return UserEntity
     */
    static public function getByStatus($status);

    /**
     * @param $username
     * @return UserEntity
     */
    static public function getByUsername($username);

    /**
     * @param $apiToken
     * @return UserEntity
     */
    static public function getByApiToken($apiToken);

    /**
     * @return UserEntity
     */
    static public function isActive();
}
