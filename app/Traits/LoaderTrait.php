<?php

namespace App\Traits;

trait LoaderTrait
{
    /**
     * @param array $data
     * @return static
     */
    public static function load(array $data)
    {
        $entity = new static();

        foreach ($data as $field => $value) {
            if (in_array($field, $entity->fields())) {
                $method = (strpos($field, '_') !== false) ? camel_case($field) : $field;
                if (method_exists($entity, $method)) {
                    // Set property within method
                    $entity->{$method}($value);
                } else {
                    // Set property derectly
                    $entity->{$field} = $value;
                }
            }
        }

        return $entity;
    }
}
