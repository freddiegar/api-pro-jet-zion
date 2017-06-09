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
     * @return array
     */
    protected function requestToArray()
    {
        return $this->request()->toArray();
    }

    /**
     * @return string
     */
    protected function requestMethod()
    {
        return $this->request()->method();
    }

    /**
     * @return mixed
     */
    protected function requestInput($name)
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
     * Valid data in request
     */
    public function applyRules()
    {
        $this->validate($this->request(), $this->rules(), $this->messages());

        return $this;
    }
}
