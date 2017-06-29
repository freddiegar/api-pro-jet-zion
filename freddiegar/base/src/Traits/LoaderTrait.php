<?php

namespace FreddieGar\Base\Traits;

/**
 * Trait LoaderTrait
 * @package FreddieGar\Base\Traits
 */
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
        $entity = ($newEntitiy) ? new static() : $entity ?: new static();

        foreach ($properties as $property => $value) {
            if (in_array($property, $entity->fields())) {
                $setter = setter($property);
                method_exists($entity, $setter) ? $entity->{$setter}($value) : $entity->{$property} = $value;
            }
        }

        return $entity;
    }

    /**
     * @param array $dataSets
     * @return array
     */
    static public function loadMultiple(array $dataSets)
    {
        $loadMultiple = [];

        foreach ($dataSets as $dataSet) {
            $loadMultiple[] = static::load($dataSet);
        }

        return $loadMultiple;
    }

    /**
     * @param array $newProperties
     * @return static
     */
    public function merge(array $newProperties)
    {
        return static::load($newProperties, false);
    }
}
