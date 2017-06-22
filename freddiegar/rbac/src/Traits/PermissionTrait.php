<?php

namespace FreddieGar\Rbac\Traits;

//use Illuminate\Database\Eloquent\Relations\HasMany;

use FreddieGar\Rbac\Contracts\Repositories\PermissionRepository;
use FreddieGar\Rbac\Contracts\Repositories\UserRoleRepository;
use Illuminate\Support\Facades\Auth;

trait PermissionTrait
{
    public function getRoles() {
        return UserRoleRepository::roles(Auth::id());
    }

    public function getPermissions() {
        return PermissionRepository::findByRole();
    }
}
