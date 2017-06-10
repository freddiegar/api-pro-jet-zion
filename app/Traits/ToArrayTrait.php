<?php

namespace App\Traits;

trait ToArrayTrait
{
    /**
     * @param bool $excludeHiddens
     * @return array
     */
    public function toArray($excludeHiddens = false)
    {
        $toArray = [];
        $properties = ($excludeHiddens) ? array_diff($this->fields(), $this->hiddens()) : $this->fields();

        foreach ($properties as $property) {
            if (isset($this->{$property})) {
                $getter = getter($property);
                $toArray[$property] = method_exists($this, $getter) ? $this->{$getter}() : $this->{$property};
            }
        }

        return $toArray;
    }
}
