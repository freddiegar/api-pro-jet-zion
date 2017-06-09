<?php

namespace App\Contracts\Commons;

use App\Contracts\Interfaces\BlameInterface;
use App\Traits\BlameColumnsTrait;

abstract class EntityContract implements BlameInterface
{
    use BlameColumnsTrait;

    /**
     * @return array
     */
    abstract protected function fields();

    /**
     * @param array $data
     * @param array $fields
     * @return mixed
     */
    abstract protected function load(array $data, array $fields);

    /**
     * @return array
     */
    abstract public function toArray();
}
