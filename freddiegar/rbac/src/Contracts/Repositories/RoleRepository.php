<?php

namespace FreddieGar\Rbac\Contracts\Repositories;

use FreddieGar\Rbac\Models\Role;

/**
 * Interface RoleRepository
 * @package FreddieGar\Rbac\Contracts\Repositories
 */
interface RoleRepository
{
    /**
     * @return Role
     */
    static public function model();

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

    /**
     * @param $role_id
     * @return mixed
     */
    static public function createdBy($role_id);

    /**
     * @param $role_id
     * @return mixed
     */
    static public function updatedBy($role_id);

    /**
     * @param $role_id
     * @return array
     */
    static public function users($role_id);

    /**
     * @param $role_id
     * @return array
     */
    static public function permissions($role_id);
}
