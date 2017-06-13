<?php

namespace App\Traits;

use App\Constants\FilterType;
use App\Constants\OperatorType;
use App\Constants\Pattern;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

trait FilterTrait
{
    static private $FILTER_METHOD_PREFIX = 'filterBy%s';

//    static private $FILTER_SMART_NAME = 'smart_search_filter';

//    static private $SMART_SEARCH_TYPE = 'smart_search';

//    static private $FILTER_SEPARATOR = '.';

//    static private $FILTER_BY_DEFAULT = [
//        FilterType::TEXT,
//    ];

//    static private $SMART_SEARCH_INPUT = '__from_smart_search_filter';

    /**
     * @var Model
     */
//    protected $model;

    /**
     * @var array
     */
    protected $filtersToApply = [];

    /**
     * @var bool
     */
//    protected $smartSearch = true;

    /**
     * @var object
     */
//    protected $filters;

    /**
     * @param $id
     * @return mixed
     */
//    private function getRequestValue($id)
//    {
//        return $this->request->input(str_replace(self::FILTER_SEPARATOR, '_', $id));
//    }

    /**
     * @param $id
     * @param $value
     */
//    private function mergeFilterInRequest($id, $value)
//    {
//        $this->request->merge([str_replace(self::FILTER_SEPARATOR, '_', $id) => $value]);
//    }

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
     * @return bool
     */
    private function queryScope($filter, $whereType, $value = '', $operator = null)
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

        return true;
    }

    /**
     * @return bool
     */
//    private function isSmartSearch()
//    {
//        return !empty($this->request->input(self::SMART_SEARCH_INPUT));
//    }

    /**
     * @return $this
     */
    public function applyFilters()
    {
//        if ($this->smartSearch && $this->isSmartSearch()) {
//            $filters = new \stdClass();
//            $filters->{self::SMART_SEARCH_FILTER} = (object)[
//                'id' => self::SMART_SEARCH_FILTER,
//                'label' => '',
//                'type' => self::SMART_SEARCH_TYPE,
//                'value' => '',
//            ];
//        }

        foreach (static::filters() as $field => $filter) {
            $this->applyFilter(array_merge($filter, compact('field')));
        }

        return $this;
    }

    /**
     * @param array $filter
     * @param string $whereType
     */
    public function applyFilter($filter, $whereType = 'where')
    {
        $filterBy = sprintf(self::$FILTER_METHOD_PREFIX, ucfirst(camel_case($filter['type'])));
        call_user_func([$this, $filterBy], $filter, $whereType);
    }

    /**
     * @param array $filter
     * @param string $whereType
     */
    public function filterBySelect($filter, $whereType = 'where')
    {
        $value = $this->requestInput($filter['field']);

        if (!empty($value)) {
            $this->queryScope($filter, $whereType, $value);
        }
    }

    /**
     * @param array $filter
     * @param string $whereType
     */
    public function filterByText($filter, $whereType = 'where')
    {
        $value = $this->requestInput($filter['field']);

        if (!empty($value)) {
            $this->queryScope($filter, $whereType, sprintf(Pattern::QUERY_LIKE, $value), OperatorType::LIKE);
        }
    }

    /**
     * @param array $filter
     * @param string $whereType
     */
    public function filterByEmail($filter, $whereType = 'where')
    {
        $this->execText($filter, $whereType);
    }

    /**
     * @param array $filter
     * @param string $whereType
     */
    public function filterByNumber($filter, $whereType = 'where')
    {
        $this->execText($filter, $whereType);
    }

    /**
     * @param array $filter
     * @param string $whereType
     */
    public function filterByDate($filter, $whereType = 'where')
    {
        $value = $this->requestInput($filter['field']);

        if (!empty($value)) {
            $valueFormatted = Carbon::parse($value);

            if ($valueFormatted) {
                $this->queryScope($filter, $whereType, $valueFormatted);
            }
        }
    }

    /**
     * @param array $filter
     * @param string $whereType
     */
    public function filterByBetween($filter, $whereType = 'where')
    {
        $valueMin = $this->requestInput($filter['field'] . FilterType::BETWEEN_MIN_SUFFIX);
        $valueMax = $this->requestInput($filter['field'] . FilterType::BETWEEN_MAX_SUFFIX);

        $valueMinFormatted = Carbon::parse($valueMin);
        $valueMaxFormatted = Carbon::parse($valueMax)
            ->hour(23)
            ->minute(59)
            ->second(59);

        if (!empty($valueMin) && !empty($valueMax)) {
            if ($valueMinFormatted && $valueMaxFormatted) {
                $this->queryScope($filter, $whereType, $valueMinFormatted, OperatorType::MAJOR_EQUALS);
                $this->queryScope($filter, $whereType, $valueMaxFormatted, OperatorType::MINOR_EQUALS);

            }
        } elseif (!empty($valueMin)) {
            if ($valueMinFormatted) {
                $this->queryScope($filter, $whereType, $valueMinFormatted, OperatorType::MAJOR_EQUALS);
            }
        } elseif (!empty($valueMax)) {
            if ($valueMaxFormatted) {
                $this->queryScope($filter, $whereType, $valueMaxFormatted, OperatorType::MINOR_EQUALS);
            }
        }
    }

    /**
     * @param array $filter
     */
//    public function filterBySmartSearch($filter)
//    {
//        $smartSearchValue = $this->requestInput($filter['field']);
//
//        if (empty($smartSearchValue)) {
//            return;
//        }
//
//        $smartFilterTypes = self::DEFAULT_FILTERS;
//
//        if (strtotime($smartSearchValue)) {
//            $smartFilterTypes = [
//                FilterType::DATE,
//                FilterType::BETWEEN
//            ];
//        }
//
//        if (is_numeric($smartSearchValue)) {
//            $smartFilterTypes = [
//                FilterType::NUMBER,
//            ];
//
//            if (!$this->isSmartSearch()) {
//                array_push($smartFilterTypes, FilterType::SELECT);
//            }
//        }
//
//        if (strpos($smartSearchValue, '@') !== false) {
//            $smartFilterTypes = [
//                FilterType::EMAIL
//            ];
//        }
//
//        $queryGlobal = clone $this->query;
//
//        $this->query->where(function ($query) use ($smartFilterTypes, $smartSearchValue, $queryGlobal) {
//            $this->query = $query;
//
//            foreach ($this->filters as $filter) {
//                if (!in_array($filter->type, $smartFilterTypes)) {
//                    continue;
//                }
//
//                $this->mergeFilterInRequest($filter['field'], $smartSearchValue);
//                $this->applyFilter($filter, 'orWhere');
//            }
//
//            $this->query = $queryGlobal;
//        });
//    }
}
