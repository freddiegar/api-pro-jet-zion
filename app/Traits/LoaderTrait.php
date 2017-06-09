<?php

namespace App\Traits;

trait LoaderTrait
{
    protected function load(array $data, array $fields)
    {
        foreach ($data as $name => $value) {
            if (in_array($name, $fields)) {
                $this->{$name} = $value;
            }
        }
    }
}
