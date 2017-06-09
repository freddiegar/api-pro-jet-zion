<?php

namespace App\Contracts\Commons;

use App\Contracts\Interfaces\BlameInterface;
use App\Traits\BlameColumnsTrait;
use App\Traits\LoaderTrait;
use App\Traits\ToArrayTrait;

abstract class EntityContract implements BlameInterface
{
    use BlameColumnsTrait;
    use LoaderTrait;
    use ToArrayTrait;

    /**
     * @return array
     */
    abstract protected function fields();

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
