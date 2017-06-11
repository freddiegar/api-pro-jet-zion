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

        return UserEntity::load($this->repository()->create($user->toArray()))->toArray(true);
    }

    /**
     * @param int $id
     * @return array
     */
    public function read($id)
    {
        return UserEntity::load($this->repository()->getById($id))->toArray(true);
    }

    /**
     * @param int $id
     * @return array
     */
    public function update($id)
    {
        $user = UserEntity::load($this->requestInput());
        if ($this->requestInput('password')) {
            $user->setPassword($this->requestInput('password'));
        }
        $this->repository()->updateById($id, $user->toArray());
        return $user->id($id)->toArray(true);
    }

    /**
     * @param int $id
     * @return array
     */
    public function delete($id)
    {
        $this->repository()->deleteById($id);
        return (new UserEntity())->id($id)->toArray(true);
    }

    /**
     * @return array
     */
    protected function rules()
    {
        $required = $this->requestIsMethod(HttpMethod::POST) ? 'required|' : '';

        return [
            'username' => $required . 'max:255',
            'password' => $required . 'max:255',
            'status' => 'in:' . implode(',', [
                    UserStatus::ACTIVE,
                    UserStatus::INACTIVE,
                    UserStatus::SUSPENDED,
                    UserStatus::BLOCKED,
                ]),
        ];
    }
}
