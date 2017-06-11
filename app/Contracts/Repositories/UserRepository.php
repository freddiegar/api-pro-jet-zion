<?php

namespace App\Contracts\Repositories;

interface UserRepository
{
    /**
     * @param array $user
     * @return array
     */
    static public function create($user);

    /**
     * @param int $id
     * @param array $user
     * @return bool
     */
    static public function updateById($id, $user);

    /**
     * @param int $id
     * @return array
     */
    static public function getById($id);

    /**
     * @param string $username
     * @return array
     */
//    static public function getByUsername($username);

    /**
     * @param string $apiToken
     * @return array
     */
    static public function getByApiToken($apiToken);

    /**
     * @return bool
     */
//    static public function isActive();
}
