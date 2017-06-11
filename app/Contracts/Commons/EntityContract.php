<?php

namespace App\Contracts\Commons;

use App\Constants\BlameColumn;
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
            BlameColumn::CREATED_BY,
            BlameColumn::UPDATED_BY,
            BlameColumn::DELETED_BY,
            BlameColumn::CREATED_AT,
            BlameColumn::UPDATED_AT,
            BlameColumn::DELETED_AT,
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

    /**
     * @return string
     */
    public function __toString()
    {
        return json_encode($this->toArray(), 0);
    }
}
