<?php

namespace FreddieGar\Base\Traits;

use FreddieGar\Base\Constants\FilterType;
use FreddieGar\Base\Constants\OperatorType;
use FreddieGar\Base\Constants\Pattern;
use Carbon\Carbon;

/**
 * Trait FilterTrait
 * @package FreddieGar\Base\Traits
 */
trait FilterTrait
{
    static public $FILTER_SMART_NAME = 'q';

    static private $FILTER_METHOD_PREFIX = 'filterBy%s';

    static private $FILTER_SMART_TYPE = 'SmartSearch';

    static private $FILTER_BY_DEFAULT = [
        FilterType::TEXT,
        FilterType::EMAIL,
    ];

    /**
     * @var array
     */
    private $filtersToApply = [];

    /**
     * @return array
     */
    protected function filterToApply()
    {
        return $this->filtersToApply ?: [];
    }

    /**
     * @param array $filter
     * @param string $value
     * @param string $operator
     * @param string $whereType
     * @return $this
     */
    private function setFilterToApply($filter, $whereType, $value = '', $operator = null)
    {
        $operator = $operator ?: OperatorType::EQUALS;
//        if (!is_null($filter->morphs)) {
//            $model = $this->model;
//            $morphsTypes = $filter->morphs['types'];
//            $column = $filter->morphs['column'];
//
//            foreach ($morphsTypes as $morphType) {
//                $this->query->orWhere(function ($query) use (
//                    $morphType,
//                    $filter,
//                    $value,
//                    $operator,
//                    $model,
//                    $column
//                ) {
//                    $relatedIds = $morphType::where($filter['field'], $operator, $value)
//                        ->select($column)
//                        ->get()
//                        ->map(function ($information) use ($column) {
//                            return $information->{$column};
//                        })
//                        ->toArray();
//
//                    $query->whereIn($model::morphColumnId(), $relatedIds)
//                        ->where($model::morphColumnType(), $morphType);
//                });
//            }
//
//            return true;
//        }

//        if (strpos($filter['field'], self::$FILTER_SEPARATOR) !== false) {
//            $explode = explode(self::$FILTER_SEPARATOR, $filter['field']);
//
//             For instance: bidder.email
//            $relation = $explode[0]; // bidder
//            $column = $explode[1]; // email
//
//             El filtro es alternativo en la consulta global, es decir: or
//            $whereType = ($this->existSmartSearch()) ? 'whereHas' : 'orWhereHas';
//            $this->query->{$whereType}($relation, function ($query) use ($column, $value, $operator) {
//                 El filtro es complementario dentro de la consulta, es decir: and
//                $query->where($column, $operator, $value);
//            });
//
//            return true;
//        }

        $this->filtersToApply[][$whereType] = sprintf('%s|%s|%s', $filter['field'], $operator, $value);

        return $this;
    }

    /**
     * @return $this
     */
    public function applyFilters()
    {
        self::applyFilter([
            'field' => self::$FILTER_SMART_NAME,
            'type' => self::$FILTER_SMART_TYPE,
        ]);

        foreach (static::filters() as $field => $filter) {
            self::applyFilter(array_merge(compact('field'), $filter));
        }

        return $this;
    }

    /**
     * @param array $filter
     * @param string $whereType
     */
    private function applyFilter($filter, $whereType = 'where')
    {
        call_user_func([$this, sprintf(self::$FILTER_METHOD_PREFIX, ucfirst($filter['type']))], $filter, $whereType);
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    /**
     * @param array $filter
     * @param string $whereType
     */
    private function filterBySelect($filter, $whereType = 'where')
    {
        $value = static::requestInput($filter['field']);

        if (!empty($value)) {
            self::setFilterToApply($filter, $whereType, $value);
        }
    }

    /**
     * @param array $filter
     * @param string $whereType
     */
    private function filterByText($filter, $whereType = 'where')
    {
        $value = static::requestInput($filter['field']);

        if (!empty($value)) {
            self::setFilterToApply($filter, $whereType, sprintf(Pattern::QUERY_LIKE, $value), OperatorType::LIKE);
        }
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    /**
     * @param array $filter
     * @param string $whereType
     */
    private function filterByEmail($filter, $whereType = 'where')
    {
        self::filterByText($filter, $whereType);
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    /**
     * @param array $filter
     * @param string $whereType
     */
    private function filterByNumber($filter, $whereType = 'where')
    {
        $value = static::requestInput($filter['field']);

        if (!empty($value)) {
            self::setFilterToApply($filter, $whereType, $value);
        }
    }

    /**
     * @param array $filter
     * @param string $whereType
     */
    private function filterByDate($filter, $whereType = 'where')
    {
        $value = static::requestInput($filter['field']);

        if (!empty($value)) {
            if ($valueMinFormatted = Carbon::parse($value)) {
                $valueMaxFormatted = Carbon::parse($value)
                    ->hour(23)
                    ->minute(59)
                    ->second(59);

                self::doBetween($filter, $whereType, $valueMinFormatted, $valueMaxFormatted);
            }
        }
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    /**
     * @param array $filter
     * @param string $whereType
     */
    private function filterByBetween($filter, $whereType = 'where')
    {
        $valueMin = static::requestInput($filter['field'] . FilterType::BETWEEN_MIN_SUFFIX);
        $valueMax = static::requestInput($filter['field'] . FilterType::BETWEEN_MAX_SUFFIX);

        $valueMinFormatted = Carbon::parse($valueMin);
        $valueMaxFormatted = Carbon::parse($valueMax)
            ->hour(23)
            ->minute(59)
            ->second(59);

        if (!empty($valueMin) && !empty($valueMax) && $valueMinFormatted && $valueMaxFormatted) {
            self::doBetween($filter, $whereType, $valueMinFormatted, $valueMaxFormatted);
        } elseif (!empty($valueMin) && $valueMinFormatted) {
            self::setFilterToApply($filter, $whereType, $valueMinFormatted, OperatorType::MAJOR_EQUALS);
        } elseif (!empty($valueMax) && $valueMaxFormatted) {
            self::setFilterToApply($filter, $whereType, $valueMaxFormatted, OperatorType::MINOR_EQUALS);
        }

        self::filterByDate($filter, $whereType);
    }

    /**
     * @param array $filter
     * @param string $whereType
     * @param string $min
     * @param string $max
     */
    private function doBetween($filter, $whereType, $min, $max)
    {
        self::setFilterToApply($filter, $whereType, $min, OperatorType::MAJOR_EQUALS);
        self::setFilterToApply($filter, 'where', $max, OperatorType::MINOR_EQUALS);
    }

    /**
     * @param array $filter
     */
    public function filterBySmartSearch($filter)
    {
        $value = static::requestInput($filter['field']);

        if (empty($value)) {
            return;
        }

        $smartFilterTypes = self::$FILTER_BY_DEFAULT;

        if (strtotime($value)) {
            $smartFilterTypes = [
                FilterType::DATE,
                FilterType::BETWEEN
            ];
        }

        if (is_numeric($value)) {
            $smartFilterTypes = [
                FilterType::NUMBER,
            ];
        }

        if (strpos($value, '@') !== false) {
            $smartFilterTypes = [
                FilterType::EMAIL
            ];
        }

        foreach (static::filters() as $field => $filter) {
            if (!in_array($filter['type'], $smartFilterTypes)) {
                continue;
            }
            static::requestAddInput($field, $value);
            self::applyFilter(array_merge(compact('field'), $filter), 'orWhere');
        }
    }
}
