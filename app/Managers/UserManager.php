<?php

namespace App\Managers;

use App\Contracts\Commons\ManagerContract;
use App\Contracts\Commons\SCRUDContract;
use App\Contracts\Repositories\UserRepository;
use App\Entities\UserEntity;
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
     * @return UserEntity
     */
    public function create()
    {
        $user = new UserEntity($this->request()->toArray());
        return $this->repository()->create($user);
    }

    /**
     * @return array
     */
    protected function rules()
    {
        $rules = [];

        if ($this->request()->method() == 'POST') {
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
