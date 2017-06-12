<?php

namespace App\Managers;

use App\Constants\HttpMethod;
use App\Constants\UserStatus;
use App\Contracts\Commons\ManagerContract;
use App\Contracts\Interfaces\SCRUDInterface;
use App\Contracts\Repositories\UserRepository;
use App\Entities\UserEntity;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Class UserManager
 * @package App\Managers
 */
class UserManager extends ManagerContract implements SCRUDInterface
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
    protected function userRepository()
    {
        return parent::repository();
    }

    /**
     * @return array
     */
    public function create()
    {
        $userEntity = new UserEntity();
        $userEntity->username($this->requestInput('username'));
        $userEntity->setPassword($this->requestInput('password'));
        $userEntity->status(UserStatus::ACTIVE);
        $userEntity->type(User::class);

        return $userEntity->reload($this->userRepository()->create($userEntity->toArray()))->toArray(true);
    }

    /**
     * @param int $id
     * @return array
     */
    public function read($id)
    {
        return UserEntity::load($this->userRepository()->getById($id))->toArray(true);
    }

    /**
     * @param int $id
     * @return array
     */
    public function update($id)
    {
        $userEntity = UserEntity::load($this->requestInput());
        if ($this->requestInput('password')) {
            $userEntity->setPassword($this->requestInput('password'));
        }
        $this->userRepository()->updateById($id, $userEntity->toArray());
        return $userEntity->id($id)->toArray(true);
    }

    /**
     * @param int $id
     * @return array
     */
    public function delete($id)
    {
        $this->userRepository()->deleteById($id);
        return (new UserEntity())->id($id)->toArray(true);
    }

    /**
     * @return array
     */
    protected function rules()
    {
        return [
            'username' => 'required|max:255',
            'password' => 'required|max:255',
            'status' => 'in:' . implode(',', [
                    UserStatus::ACTIVE,
                    UserStatus::INACTIVE,
                    UserStatus::SUSPENDED,
                    UserStatus::BLOCKED,
                ]),
        ];
    }
}
