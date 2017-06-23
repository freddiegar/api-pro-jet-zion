<?php

namespace FreddieGar\Rbac\Contracts\Repositories;

/**
 * Interface RolePermissionRepository
 * @package FreddieGar\Rbac\Contracts\Repositories
 */
interface RolePermissionRepository
{
    /**
     * @param array $role_permission_permission
     * @return array
     */
    static public function create($role_permission_permission);

    /**
     * @param int $id
     * @return array
     */
    static public function findById($id);

    /**
     * @param int $id
     * @param array $role_permission_permission
     * @return bool
     */
    static public function updateById($id, $role_permission_permission);

    /**
     * @param int $id
     * @return bool
     */
    static public function deleteById($id);

    /**
     * @param array $filters
     * @return array
     */
    static public function findWhere($filters);

    /**
     * @param int $role_id
     * @return array
     */
    static public function findByRole($role_id);
}
