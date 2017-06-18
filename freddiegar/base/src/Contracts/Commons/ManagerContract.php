<?php

namespace FreddieGar\Base\Contracts\Commons;

use FreddieGar\Base\Constants\HttpMethod;
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
     * @param string $method
     * @return string
     */
    final protected function requestIsMethod($method)
    {
        return $this->request()->isMethod($method);
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
     * @return array
     */
    abstract protected function rules();

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
}
