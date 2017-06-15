<?php

namespace App\Managers;

use App\Contracts\Repositories\LoginRepository;
use App\Entities\UserEntity;
use FreddieGar\Base\Constants\HttpMethod;
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
        $userEntity = UserEntity::load($this->loginRepository()->getUserPasswordByUsername($this->requestInput('username')));

        if (!passwordIsValid($this->requestInput('password'), $userEntity->password())) {
            throw new UnauthorizedException(trans('exceptions.credentials'));
        }

        $userEntity->lastIpAddress($this->requestIp());
        $userEntity->lastLoginAt(now());
        $userEntity->apiToken(randomHashing());

        $this->loginRepository()->updateUserLastLogin($userEntity->id(), $userEntity->toArray());

        return [
            UserEntity::KEY_API_TOKEN => $userEntity->apiToken()
        ];
    }

    /**
     * @return array
     */
    protected function rules()
    {
        $rules = [];

        if ($this->requestIsMethod(HttpMethod::POST)) {
            $rules = [
                'username' => 'required',
                'password' => 'required',
            ];
        }

        return $rules;
    }
}
