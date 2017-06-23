<?php

namespace App\Managers;

use App\Contracts\Repositories\LoginRepository;
use App\Entities\UserEntity;
use FreddieGar\Base\Contracts\Commons\ManagerContract;
use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;

/**
 * Class LoginManager
 * @package App\Managers
 */
class LoginManager extends ManagerContract
{
    /**
     * UserManager constructor.
     * @param Request $request
     * @param LoginRepository $repository
     */
    public function __construct(Request $request, LoginRepository $repository)
    {
        $this->request($request);
        $this->repository($repository);
    }

    /**
     * @return LoginRepository
     */
    protected function loginRepository()
    {
        return parent::repository();
    }

    /**
     * @return array
     */
    public function login()
    {
        $user = UserEntity::load($this->loginRepository()->getUserPasswordByUsername($this->requestInput('username')));

        if (!passwordIsValid($this->requestInput('password'), $user->password())) {
            throw new UnauthorizedException(trans('exceptions.credentials'));
        }

        $user->lastIpAddress($this->requestIp());
        $user->lastLoginAt(now());
        $user->apiToken(randomHashing());

        $this->loginRepository()->updateUserLastLogin($user->id(), $user->toArray());

        return [
            UserEntity::KEY_API_TOKEN => $user->apiToken()
        ];
    }

    /**
     * @return array
     */
    protected function rules()
    {
        return [
            'username' => 'required',
            'password' => 'required',
        ];
    }
}
