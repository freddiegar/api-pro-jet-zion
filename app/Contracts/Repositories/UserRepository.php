<?php

namespace App\Contracts\Repositories;

use App\Models\User;

/**
 * Interface UserRepository
 * @package App\Contracts\Repositories
 */
interface UserRepository
{
    /**
     * @return User
     */
    static public function model();

    /**
     * @param array $user
     * @return array
     */
    static public function create($user);

    /**
     * @param int $id
     * @return array
     */
    static public function findById($id);

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

    /**
     * @param array $filters
     * @return array
     */
    static public function findWhere($filters);

    /**
     * @param $user_id
     * @return mixed
     */
    static public function createdBy($user_id);

    /**
     * @param $user_id
     * @return mixed
     */
    static public function updatedBy($user_id);

    /**
     * @param int $user_id
     * @return array
     */
    static public function roles($user_id);
}
