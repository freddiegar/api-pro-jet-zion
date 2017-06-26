<?php

namespace FreddieGar\Base\Contracts\Commons;

use FreddieGar\Base\Constants\HttpMethod;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\ProvidesConvenienceMethods;

/**
 * Class ManagerContract
 * @package FreddieGar\Base\Contracts\Commons
 */
abstract class ManagerContract
{
    use ProvidesConvenienceMethods;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var mixed
     */
    protected $repository;

    /**
     * Model that manage manager
     * @return mixed
     */
    abstract protected function model();

    /**
     * @param Request $request
     * @return Request
     */
    final protected function request(Request $request = null)
    {
        if (!is_null($request)) {
            $this->request = $request;
        }

        return $this->request;
    }

    /**
     * @return string
     */
    final protected function requestMethod()
    {
        return $this->request()->method();
    }

    /**
     * @return string
     */
    final protected function requestIp()
    {
        return $this->request()->ip();
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    final protected function requestInput($name = null, $default = null)
    {
        return $name ? $this->request()->input($name, $default) : $this->request()->all();
    }

    /**
     * @param array $keys
     * @return array
     */
    final protected function requestExcept(array $keys = [])
    {
        return $this->request()->except($keys);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return void
     */
    final protected function requestAddInput($name, $value = null)
    {
        $this->request()->merge([$name => $value]);
    }

    /**
     * Valid data in request
     * return $this
     */
    final public function requestValidate()
    {
        $this->validate($this->request(), $this->removeRulesThatNotApply($this->rules()), $this->messages());

        return $this;
    }

    /**
     * Remove rules to fieds that are not in request
     * @param $rules
     * @return mixed
     */
    final private function removeRulesThatNotApply($rules)
    {
        if ($this->requestMethod() !== HttpMethod::PATCH) {
            return $rules;
        }

        foreach ($rules as $field => $_rules) {
            if (!$this->requestInput($field)) {
                unset($rules[$field]);
            }
        }

        return $rules;
    }

    /**
     * @return string
     */
    final private function name()
    {
        return snake_case(str_replace('Manager', '', class_basename(static::class)), '-');
    }

    /**
     * @param mixed $repository
     * @return mixed
     */
    final protected function repository($repository = null)
    {
        if (!is_null($repository)) {
            $this->repository = $repository;
        }

        return $this->repository;
    }

    /**
     * @param int $id
     * @param string $relationship
     * @return array
     */
    final public function relationship($id, $relationship)
    {
        $entity = static::read($id);
        $method = camel_case($relationship);
//        dd($entity, $relationship, $method);
//        $relation_id = isset($entity[$relationship . '_id']) ? $entity[$relationship . '_id'] : $entity['id'];

        return array_merge([self::name() => $entity], [$relationship => static::{$method}($id)]);
    }

    /**
     * @return array
     * @codeCoverageIgnore
     */
    protected function rules()
    {
        return [];
    }

    /**
     * @return array
     */
    protected function messages()
    {
        return [];
    }

    /**
     * @return array
     * @codeCoverageIgnore
     */
    protected function filters()
    {
        return [];
    }

    /**
     * @param string $relationship
     * @param array $arguments
     */
    public function __call($relationship, array $arguments = [])
    {
        throw new ModelNotFoundException(trans('exceptions.relationship_not_found', compact('relationship')));
    }
}
