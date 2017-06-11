<?php

namespace App\Contracts\Commons;

use App\Contracts\Interfaces\BlameInterface;
use App\Traits\BlameColumnsTrait;
use App\Traits\LoaderTrait;
use App\Traits\ToArrayTrait;

/**
 * Class EntityContract
 * @package App\Contracts\Commons
 */
abstract class EntityContract implements BlameInterface
{
    use BlameColumnsTrait;
    use LoaderTrait;
    use ToArrayTrait;

    /**
     * Properties load in entity
     * @return array
     */
    abstract protected function fields();

    /**
     * This fields are exclude from toArray method
     * return array
     */
    protected function hiddens()
    {
        return [];
    }

    /**
     * This fields are exclude from toArray method
     * return array
     */
    protected function blames()
    {
        return [
            'created_by',
            'updated_by',
            'deleted_by',
            'created_at',
            'updated_at',
            'deleted_at',
        ];
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->{$name} = $value;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->{$name} = null;
    }
}
