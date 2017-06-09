<?php

namespace App\Managers;

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
        $user = UserEntity::load($this->repository()->getUserPasswordByUsername($this->requestInput('username')));
        if (!Hash::check($this->requestInput('password'), $user->password())) {
            throw new UnauthorizedException(trans('login.error.credentials'));
        }
        $this->repository()->updateUserLastLogin($user, $this->request());
        $user = UserEntity::load($this->repository()->getUserApiToken($user->id()));

        return [
            'token' => $user->apiToken()
        ];
    }

    /**
     * @return array
     */
    protected function rules()
    {
        $rules = [];

        if ($this->requestMethod() == 'POST') {
            $rules = [
                'username' => 'required|max:255',
                'password' => 'required|max:255',
            ];
        }

        return $rules;
    }
}
