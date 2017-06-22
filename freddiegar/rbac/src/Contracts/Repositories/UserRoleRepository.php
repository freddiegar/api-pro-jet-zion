<?php

namespace FreddieGar\Rbac\Contracts\Repositories;

/**
 * Interface UserRoleRepository
 * @package FreddieGar\Rbac\Contracts\Repositories
 */
interface UserRoleRepository
{
    /**
     * @param array $user_role
     * @return array
     */
    static public function create($user_role);

    /**
     * @param int $id
     * @return array
     */
    static public function findById($id);

    /**
     * @param int $user_id
     * @return array
     */
//    static public function findByUser($user_id);

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
     * @param int $user_id
     * @return array
     */
    static public function roles($user_id);

    /**
     * @param int $role_id
     * @return array
     */
    static public function users($role_id);
}
