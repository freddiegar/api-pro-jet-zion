<?php

namespace App\Managers;

use App\Constants\HttpMethod;
use App\Contracts\Commons\ManagerContract;
use App\Contracts\Repositories\LoginRepository;
use App\Entities\UserEntity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
        $userRepository = UserEntity::load($this->repository()->getUserPasswordByUsername($this->requestInput('username')));

        if (!Hash::check($this->requestInput('password'), $userRepository->password())) {
            throw new UnauthorizedException(trans('login.error.credentials'));
        }

        $user = new UserEntity();
        $user->lastIpAddress($this->requestIp());
        $user->lastLoginAt(now());
        $user->apiToken(randomHashing());

        $this->repository()->updateUserLastLogin($userRepository->id(), $user->toArray());

        return [
            UserEntity::KEY_API_TOKEN => UserEntity::load($this->repository()->getUserApiToken($userRepository->id()))->apiToken()
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
