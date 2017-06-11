<?php

namespace App\Contracts\Repositories;

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
     * @return array
     */
    static public function getUserPasswordByUsername($username);

    /**
     * @param $id
     * @return array
     */
    static public function getUserApiToken($id);
}
