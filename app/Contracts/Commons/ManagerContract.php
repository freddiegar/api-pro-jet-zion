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

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var UserRepository
     */
    protected $repository;

    /**
     * @return Request
     */
    final protected function request()
    {
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
    final protected function requestIp()
    {
        return $this->request()->ip();
    }

    /**
     * @param $name
     * @return mixed
     */
    final protected function requestInput($name = null)
    {
        return $name ? $this->request()->input($name) : $this->request()->all();
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
        if ($this->requestIsMethod(HttpMethod::PUT)) {
            foreach ($rules as $field => $_rules) {
                if (!$this->requestInput($field)) {
                    unset($rules[$field]);
                }
            }
        }

        return $rules;
    }

    /**
     * @return mixed
     */
    final protected function repository()
    {
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
     */
    protected function filters()
    {
        return [];
    }
}
