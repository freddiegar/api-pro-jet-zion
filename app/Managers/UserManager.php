<?php

namespace App\Managers;

use App\Constants\UserStatus;
use App\Contracts\Repositories\UserRepository;
use App\Entities\UserEntity;
use App\Models\User;
use FreddieGar\Base\Constants\BlameColumn;
use FreddieGar\Base\Constants\FilterType;
use FreddieGar\Base\Contracts\Commons\ManagerContract;
use FreddieGar\Base\Contracts\Interfaces\CRUDSInterface;
use FreddieGar\Base\Traits\FilterTrait;
use FreddieGar\Base\Traits\ManagerRelationshipTrait;
use FreddieGar\Rbac\Contracts\Commons\UserRelationshipInterface;
use FreddieGar\Rbac\Entities\RoleEntity;
use Illuminate\Http\Request;

/**
 * Class UserManager
 * @package App\Managers
 */
class UserManager extends ManagerContract implements CRUDSInterface, UserRelationshipInterface
{
    use FilterTrait;
    use ManagerRelationshipTrait;

    /**
     * UserManager constructor.
     * @param Request $request
     * @param UserRepository $repository
     */
    public function __construct(Request $request, UserRepository $repository)
    {
        $this->request($request);
        $this->repository($repository);
    }

    /**
     * @return User
     */
    public function model()
    {
        return new User();
    }

    /**
     * @return UserRepository
     */
    protected function userRepository()
    {
        return parent::repository();
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function read($id)
    {
        $user = $this->model()->getFromCacheId($id, function () use ($id) {
            return $this->userRepository()->findById($id);
        });

        return UserEntity::load($user)->toArray();
    }

    /**
     * @inheritdoc
     */
    public function update($id)
    {
        $userEntity = UserEntity::load($this->requestInput());
        if ($this->requestInput('password')) {
            $userEntity->setPassword($this->requestInput('password'));
        }

        $this->userRepository()->updateById($id, $userEntity->toArray(true));

        return $this->read($id);
    }

    /**
     * @inheritdoc
     */
    public function delete($id)
    {
        $user = $this->read($id);
        $this->userRepository()->deleteById($id);
        return $user;
    }

    /**
     * @inheritdoc
     */
    public function show()
    {
        $tag = makeTagNameCache($this->filterToApply());

        $users = $this->model()->getFromCacheTag($tag, function () {
            return $this->userRepository()->findWhere($this->filterToApply());
        });

        return UserEntity::toArrayMultiple($users);
    }

    /**
     * @inheritdoc
     */
    public function roles($user_id)
    {
        $tag = makeTagNameCache([__METHOD__, $user_id]);

        $roles = $this->model()->getFromCacheTag($tag, function () use ($user_id) {
            return $this->userRepository()->roles($user_id);
        });

        return RoleEntity::toArrayMultiple($roles);
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
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
