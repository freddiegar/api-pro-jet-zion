<?php

namespace FreddieGar\Base\Traits;

/**
 * Trait ToArrayTrait
 * @package FreddieGar\Base\Traits
 */
trait ToArrayTrait
{
    /**
     * @param bool $includeHiddens
     * @return array
     */
    public function toArray($includeHiddens = false)
    {
        $toArray = [];
        $properties = $includeHiddens ? static::fields() : array_diff(static::fields(), static::hiddens());

        foreach ($properties as $property) {
            if (isset($this->{$property})) {
                $getter = getter($property);
                $toArray[$property] = method_exists($this, $getter) ? $this->{$getter}() : $this->{$property};
            }
        }

        return $toArray;
    }

    /**
     * @param array $dataSets
     * @param bool $includeHiddens
     * @return array
     */
    static public function toArrayMultiple(array $dataSets, $includeHiddens = false)
    {
        $toArrayMultiple = [];

        foreach ($dataSets as $dataSet) {
            $toArrayMultiple[] = static::load($dataSet)->toArray($includeHiddens);
        }

        return $toArrayMultiple;
    }
}
