<?php

namespace App\Managers;

use App\Constants\BlameColumn;
use App\Constants\FilterType;
use App\Constants\UserStatus;
use App\Contracts\Commons\ManagerContract;
use App\Contracts\Interfaces\SCRUDInterface;
use App\Contracts\Repositories\UserRepository;
use App\Entities\UserEntity;
use App\Models\User;
use App\Traits\FilterTrait;
use Illuminate\Http\Request;

/**
 * Class UserManager
 * @package App\Managers
 */
class UserManager extends ManagerContract implements SCRUDInterface
{
    use FilterTrait;
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

        return $userEntity->merge($this->userRepository()->create($userEntity->toArray(true)))->toArray();
    }

    /**
     * @param int $id
     * @return array
     */
    public function read($id)
    {
        return UserEntity::load($this->userRepository()->getById($id))->toArray();
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
        $this->userRepository()->updateById($id, $userEntity->toArray(true));
        return $userEntity->id($id)->toArray();
    }

    /**
     * @param int $id
     * @return array
     */
    public function delete($id)
    {
        $this->userRepository()->deleteById($id);
        return (new UserEntity())->id($id)->toArray();
    }

    /**
     * @return array
     */
    public function search()
    {
        return (new UserEntity())->toArrayMultiple($this->userRepository()->where($this->filterToApply()));
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

    /**
     * @return array
     */
    protected function filters()
    {
        return [
            'username' => [
                'type' => FilterType::EMAIL
            ],
            'status' => [
                'type' => FilterType::SELECT
            ],
            BlameColumn::CREATED_BY => [
                'type' => FilterType::NUMBER
            ],
            BlameColumn::CREATED_AT => [
                'type' => FilterType::BETWEEN
            ],
        ];
    }
}
