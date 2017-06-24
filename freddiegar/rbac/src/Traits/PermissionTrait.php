<?php

namespace FreddieGar\Rbac\Traits;

use FreddieGar\Rbac\Contracts\Repositories\PermissionRepository;
use FreddieGar\Rbac\Contracts\Repositories\RolePermissionRepository;
use FreddieGar\Rbac\Contracts\Repositories\UserRoleRepository;
use Illuminate\Support\Facades\Auth;

/**
 * Trait PermissionTrait
 * @package FreddieGar\Rbac\Traits
 */
trait PermissionTrait
{
    /**
     * @return array
     */
    static public function getRoles()
    {
        $roles = [];
        /** @var UserRoleRepository $userRoleRepository */
        $userRoleRepository = app(UserRoleRepository::class);
        $userRoles = $userRoleRepository->roles(Auth::id());
        foreach ($userRoles as $userRole) {
            array_push($roles, $userRole['role_id']);
        }
        return $roles;
    }

    /**
     * @return array
     */
    static public function getPermissions()
    {
        $permission_ids = [];
        $roles = static::getRoles();

        foreach ($roles as $role) {
            $permission_ids = array_merge(static::getPermissionsFromRole($role), $permission_ids);
        }

        /** @var PermissionRepository $permissionRepository */
        $permissionRepository = app(PermissionRepository::class);
        return array_map(function ($permission) {
            return $permission['slug'];
        }, $permissionRepository->getSlugById($permission_ids));
    }

    /**
     * @param $role_id
     * @return array
     */
    static public function getPermissionsFromRole($role_id)
    {
        $permissions = [];
        /** @var RolePermissionRepository $rolePermissionRepository */
        $rolePermissionRepository = app(RolePermissionRepository::class);
        $rolePermissions = $rolePermissionRepository->findByRole($role_id);

        foreach ($rolePermissions as $rolePermission) {
            if ($rolePermission['granted']) {
                if (!$rolePermission['permission_id'] && $rolePermission['parent_id']) {
                    $permissions = array_merge($permissions, static::getPermissionsFromRole($rolePermission['parent_id']));
                } else {
                    array_push($permissions, $rolePermission['permission_id']);
                }
            }
        }

        return $permissions;
    }

    /**
     * @param $permission
     * @return bool
     */
    static public function can($permission)
    {
        return in_array($permission, static::getPermissions());
    }
}
