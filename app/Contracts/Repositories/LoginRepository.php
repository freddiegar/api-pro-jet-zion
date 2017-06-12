<?php

namespace App\Contracts\Repositories;

interface LoginRepository
{
    /**
     * Model used in blame
     * @return mixed
     */
    static public function model();

    /**
     * @param string $username
     * @return array
     */
    static public function getUserPasswordByUsername($username);

    /**
     * @param int $id
     * @param array $user
     * @return bool
     */
    static public function updateUserLastLogin($id, $user);
}
