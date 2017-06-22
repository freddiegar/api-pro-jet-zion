<?php

namespace FreddieGar\Rbac\Entities;

use FreddieGar\Base\Contracts\Commons\EntityContract;

/**
 * Class UserRoleEntity
 * @package FreddieGar\Rbac\Entities
 */
class UserRoleEntity extends EntityContract
{
    protected $id;
    protected $user_id;
    protected $role_id;

    /**
     * @inheritdoc
     */
    protected function fields()
    {
        return array_merge([
            'id',
            'user_id',
            'role_id',
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
     * @param string $user_id
     * @return static|string
     */
    public function userId($user_id = null)
    {
        if (!is_null($user_id)) {
            $this->user_id = $user_id;
            return $this;
        }

        return $this->user_id;
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
}
