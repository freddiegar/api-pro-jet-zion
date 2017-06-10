<?php

namespace App\Traits;

trait ToArrayTrait
{
    public function toArray()
    {
        $toArray = [];
        $properties = array_diff($this->fields(), $this->hiddens());

        foreach ($properties as $property) {
            if (isset($this->{$property})) {
                $getter = getter($property);
                $toArray[$property] = method_exists($this, $getter) ? $this->{$getter}() : $this->{$property};
            }
        }

        return $toArray;
    }
}
