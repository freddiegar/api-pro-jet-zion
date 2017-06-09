<?php

namespace App\Traits;

trait ToArrayTrait
{
    public function toArray()
    {
        $toArray = [];
        $fields = array_diff($this->fields(), $this->hiddens());

        foreach ($fields as $field) {
            if (isset($this->{$field})) {
                $toArray[$field] = (method_exists($this, $field)) ? $this->{$field}() : $this->{$field};
            }
        }

        return $toArray;
    }
}
