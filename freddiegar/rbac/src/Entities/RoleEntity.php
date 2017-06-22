<?php

namespace FreddieGar\Rbac\Entities;

use FreddieGar\Base\Contracts\Commons\EntityContract;

/**
 * Class RoleEntity
 * @package App\Entities
 */
class RoleEntity extends EntityContract
{
    protected $id;
    protected $description;

    /**
     * @inheritdoc
     */
    protected function fields()
    {
        return array_merge([
            'id',
            'description',
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
     * @param string $description
     * @return static|string
     */
    public function description($description = null)
    {
        if (!is_null($description)) {
            $this->description = $description;
            return $this;
        }

        return $this->description;
    }
}
