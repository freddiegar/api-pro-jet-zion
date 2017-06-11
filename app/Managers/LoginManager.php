<?php

namespace App\Managers;

use App\Constants\HttpMethod;
use App\Contracts\Commons\ManagerContract;
use App\Contracts\Repositories\LoginRepository;
use App\Entities\UserEntity;
use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;

class LoginManager extends ManagerContract
{
    /**
     * UserManager constructor.
     * @param Request $request
     * @param LoginRepository $repository
     */
    public function __construct(Request $request, LoginRepository $repository)
    {
        $this->request = $request;
        $this->repository = $repository;
    }

    /**
     * @return LoginRepository
     */
    protected function repository()
    {
        return parent::repository();
    }

    /**
     * @return array
     */
    public function login()
    {
        $user = UserEntity::load($this->repository()->getUserPasswordByUsername($this->requestInput('username')));

        if (!passwordIsValid($this->requestInput('password'), $user->password())) {
            throw new UnauthorizedException(trans('login.error.credentials'));
        }

        $user->lastIpAddress($this->requestIp());
        $user->lastLoginAt(now());
        $user->apiToken(randomHashing());

        $this->repository()->updateUserLastLogin($user->id(), $user->toArray());

        return [
            UserEntity::KEY_API_TOKEN => $user->apiToken()
        ];
    }

    /**
     * @return array
     */
    protected function rules()
    {
        $rules = [];

        if ($this->requestMethodIs(HttpMethod::POST)) {
            $rules = [
                'username' => 'required',
                'password' => 'required',
            ];
        }

        return $rules;
    }
}
