<?php

namespace App\Contracts\Repositories;

interface LoginRepository
{
    /**
     * @param string $username
     * @return array
     */
    static public function getUserPasswordByUsername($username);

    /**
     * @param mixed $id
     * @param array $user
     * @return bool
     */
    static public function updateUserLastLogin($id, $user);
}
