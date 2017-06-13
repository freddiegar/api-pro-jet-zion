<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Interfaces\FilterBuilderInterface;

/**
 * Class EloquentFilterBuilder
 * @package App\Repositories\Eloquent
 */
abstract class EloquentFilterBuilder implements FilterBuilderInterface
{
    /**
     * @inheritdoc
     */
    final static public function builder($query, array $filters)
    {
        foreach ($filters as $index => $where) {
            foreach ($where as $whereType => $arguments) {
                list($field, $operator, $value) = explode('|', $arguments);
                $query->{$whereType}($field, $operator, $value);
            }
        }

        return $query;
    }
}
