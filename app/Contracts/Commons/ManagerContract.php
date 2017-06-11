<?php

namespace App\Contracts\Commons;

use App\Contracts\Repositories\UserRepository;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\ProvidesConvenienceMethods;

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
    protected function request()
    {
        return $this->request;
    }

    /**
     * @param string $method
     * @return string
     */
    protected function requestMethodIs($method)
    {
        return $this->request()->isMethod($method);
    }

    /**
     * @return string
     */
    protected function requestIp()
    {
        return $this->request()->ip();
    }

    /**
     * @param $name
     * @return mixed
     */
    protected function requestInput($name = null)
    {
        return $this->request()->input($name);
    }

    /**
     * @return mixed
     */
    protected function repository()
    {
        return $this->repository;
    }

    /**
     * @return array
     */
    protected function rules()
    {
        // @codeCoverageIgnoreStart
        return [];
        // @codeCoverageIgnoreEnd
    }

    /**
     * @return array
     */
    protected function messages()
    {
        return [];
    }

    /**
     * Valid data in request
     */
    public function applyRules()
    {
        $this->validate($this->request(), $this->rules(), $this->messages());

        return $this;
    }
}
