<?php

namespace FreddieGar\Rbac\Contracts\Repositories;

/**
 * Interface RoleRepository
 * @package FreddieGar\Rbac\Contracts\Repositories
 */
interface RoleRepository
{
    /**
     * @param array $role
     * @return array
     */
    static public function create($role);

    /**
     * @param int $id
     * @return array
     */
    static public function findById($id);

    /**
     * @param int $id
     * @param array $role
     * @return bool
     */
    static public function updateById($id, $role);

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
}
