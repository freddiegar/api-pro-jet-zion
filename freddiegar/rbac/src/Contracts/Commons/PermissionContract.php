<?php

namespace FreddieGar\Rbac\Contracts\Commons;

/**
 * Interface PermissionContract
 * @package FreddieGar\Rbac\Contracts\Commons
 */
interface PermissionContract
{
    /**
     * @return array
     */
    static public function getRoles();

    /**
     * @return array
     */
    static public function getPermissions();

    /**
     * @param $role_id
     * @return array
     */
    static public function getPermissionsFromRole($role_id);

    /**
     * @param $permission
     * @return bool
     */
    static public function can($permission);
}
