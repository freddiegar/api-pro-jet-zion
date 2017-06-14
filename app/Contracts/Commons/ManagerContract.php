<?php

namespace App\Contracts\Commons;

use App\Constants\HttpMethod;
use App\Contracts\Repositories\UserRepository;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\ProvidesConvenienceMethods;

/**
 * Class ManagerContract
 * @package App\Contracts\Commons
 */
abstract class ManagerContract
{
    use ProvidesConvenienceMethods;

    const MEDIA_TYPE_SUPPORTED = 'application/json';

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var UserRepository
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
        if (!in_array($this->requestMethod(), [HttpMethod::PUT, HttpMethod::PATCH])) {
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
