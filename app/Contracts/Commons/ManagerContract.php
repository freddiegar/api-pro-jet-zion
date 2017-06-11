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
    protected function requestIsMethod($method)
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
        return $name ? $this->request()->input($name) : $this->request()->all();
    }

    /**
     * Valid data in request
     */
    public function requestValidate()
    {
        $this->validate($this->request(), $this->rules(), $this->messages());

        return $this;
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
    abstract protected function rules();

    /**
     * @return array
     */
    protected function messages()
    {
        return [];
    }
}
