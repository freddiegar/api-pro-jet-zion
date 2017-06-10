<?php

namespace App\Traits;

trait LoaderTrait
{
    /**
     * @param array $properties
     * @return static
     */
    public static function load(array $properties)
    {
        $entity = new static();

        foreach ($properties as $property => $value) {
            if (in_array($property, $entity->fields())) {
                $setter = setter($property);
                method_exists($entity, $setter) ? $entity->{$setter}($value) : $entity->{$property} = $value;
            }
        }

        return $entity;
    }
}
