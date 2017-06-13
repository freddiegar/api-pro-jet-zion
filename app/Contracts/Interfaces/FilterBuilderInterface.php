<?php

namespace App\Contracts\Interfaces;

use Illuminate\Database\Query\Builder;

/**
 * Interface FilterBuilderInterface
 * @package App\Contracts\Interfaces
 */
interface FilterBuilderInterface
{
    /**
     * @param mixed $query
     * @param array $filters
     * @return Builder
     */
    static public function builder($query, $filters);
}
