<?php

namespace FreddieGar\Rbac\Contracts\Commons;

/**
 * Interface RoleRelationshipInterface
 * @package FreddieGar\Rbac\Contracts\Commons
 */
interface RoleRelationshipInterface
{
    /**
     * @param $role_id
     * @return mixed
     */
    public function createdBy($role_id);

    /**
     * @param $role_id
     * @return mixed
     */
    public function updatedBy($role_id);

    /**
     * @param $role_id
     * @return array
     */
    public function users($role_id);

    /**
     * @param $role_id
     * @return array
     */
    public function permissions($role_id);
}
