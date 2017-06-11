<?php

namespace App\Managers;

use App\Constants\HttpMethod;
use App\Constants\UserStatus;
use App\Contracts\Commons\ManagerContract;
use App\Contracts\Commons\SCRUDContract;
use App\Contracts\Repositories\UserRepository;
use App\Entities\UserEntity;
use App\Models\User;
use Illuminate\Http\Request;

class UserManager extends ManagerContract implements SCRUDContract
{
    /**
     * UserManager constructor.
     * @param Request $request
     * @param UserRepository $repository
     */
    public function __construct(Request $request, UserRepository $repository)
    {
        $this->request = $request;
        $this->repository = $repository;
    }

    /**
     * @return UserRepository
     */
    protected function repository()
    {
        return parent::repository();
    }

    /**
     * @return array
     */
    public function create()
    {
        $user = new UserEntity();
        $user->status(UserStatus::ACTIVE);
        $user->username($this->requestInput('username'));
        $user->setPassword($this->requestInput('password'));
        $user->type(User::class);

        return UserEntity::load(
            $this->repository()->create($user->toArray())
        )->toArray(true);
    }

    /**
     * @return array
     */
    public function read($id)
    {
        return UserEntity::load($this->repository()->getById($id))->toArray(true);
    }

    /**
     * @return array
     */
    protected function rules()
    {
        $rules = [];

        if ($this->requestMethodIs(HttpMethod::POST)) {
            $rules = [
//                'status' => 'required|in:' . implode(',', [
//                        UserStatus::ACTIVE,
//                        UserStatus::INACTIVE,
//                        UserStatus::SUSPENDED,
//                        UserStatus::BLOCKED,
//                    ]),
                'username' => 'required|max:255',
                'password' => 'required|max:255',
            ];
        }

        return $rules;
    }
}
