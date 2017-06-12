<?php

namespace App\Contracts\Repositories;

use App\Models\User;

interface UserRepository
{
    /**
     * @param array $user
     * @return array
     */
    static public function create($user);

    /**
     * @param int $id
     * @return array
     */
    static public function getById($id);

    /**
     * @param int $id
     * @param array $user
     * @return bool
     */
    static public function updateById($id, $user);

    /**
     * @param int $id
     * @return bool
     */
    static public function deleteById($id);

    /**
     * @param string $apiToken
     * @return null|User
     */
    static public function getByApiToken($apiToken);
}
