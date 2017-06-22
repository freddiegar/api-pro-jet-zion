<?php

namespace FreddieGar\Rbac\Entities;

use FreddieGar\Base\Contracts\Commons\EntityContract;

/**
 * Class RolePermissionEntity
 * @package FreddieGar\Rbac\Entities
 */
class RolePermissionEntity extends EntityContract
{
    protected $id;
    protected $role_id;
    protected $permission_id;
    protected $parent_id;
    protected $granted;

    /**
     * @inheritdoc
     */
    protected function fields()
    {
        return array_merge([
            'id',
            'role_id',
            'permission_id',
            'parent_id',
            'granted',
        ], $this->blames());
    }

    /**
     * @inheritdoc
     */
    protected function hiddens()
    {
        return $this->blames();
    }

    /**
     * @param int $id
     * @return static|int
     */
    public function id($id = null)
    {
        if (!is_null($id)) {
            $this->id = $id;
            return $this;
        }

        return $this->id;
    }

    /**
     * @param string $role_id
     * @return static|string
     */
    public function roleId($role_id = null)
    {
        if (!is_null($role_id)) {
            $this->role_id = $role_id;
            return $this;
        }

        return $this->role_id;
    }

    /**
     * @param string $permission_id
     * @return static|string
     */
    public function permissionId($permission_id = null)
    {
        if (!is_null($permission_id)) {
            $this->permission_id = $permission_id;
            return $this;
        }

        return $this->permission_id;
    }

    /**
     * @param string $parent_id
     * @return static|string
     */
    public function parentId($parent_id = null)
    {
        if (!is_null($parent_id)) {
            $this->parent_id = $parent_id;
            return $this;
        }

        return $this->parent_id;
    }

    /**
     * @param string $granted
     * @return static|string
     */
    public function granted($granted = null)
    {
        if (!is_null($granted)) {
            $this->granted = $granted;
            return $this;
        }

        return $this->granted;
    }
}
