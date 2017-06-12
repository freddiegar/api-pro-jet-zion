<?php

namespace App\Traits;

trait LoaderTrait
{
    /**
     * @param array $properties
     * @param bool $newEntitiy
     * @return static
     */
    static public function load(array $properties, $newEntitiy = true)
    {
        static $entity;
        $entity = ($newEntitiy) ? new static() : $entity;

        foreach ($properties as $property => $value) {
            if (in_array($property, $entity->fields())) {
                $setter = setter($property);
                method_exists($entity, $setter) ? $entity->{$setter}($value) : $entity->{$property} = $value;
            }
        }

        return $entity;
    }

    /**
     * @param array $newProperties
     * @return static
     */
    public function reload(array $newProperties)
    {
        return static::load($newProperties, false);
    }
}
