<?php

namespace FreddieGar\Rbac\Entities;

use FreddieGar\Base\Contracts\Commons\EntityContract;

/**
 * Class PermissionEntity
 * @package FreddieGar\Rbac\Entities
 */
class PermissionEntity extends EntityContract
{
    protected $id;
    protected $slug;
    protected $description;

    /**
     * @inheritdoc
     */
    protected function fields()
    {
        return array_merge([
            'id',
            'slug',
            'description',
        ], $this->blames());
    }

    /**
     * @inheritdoc
     */
    protected function hiddens()
    {
        return array_merge([
            'slug'
        ], $this->blames());
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
     * @param string $slug
     * @return static|string
     */
    public function slug($slug = null)
    {
        if (!is_null($slug)) {
            $this->slug = $slug;
            return $this;
        }

        return $this->slug;
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
