<?php

namespace App\Managers;

use FreddieGar\Base\Constants\FilterType;
use FreddieGar\Base\Contracts\Commons\ManagerContract;
use FreddieGar\Base\Contracts\Interfaces\CRUDSInterface;
use FreddieGar\Base\Traits\FilterTrait;
use FreddieGar\Rbac\Contracts\Repositories\UserRoleRepository;
use FreddieGar\Rbac\Entities\UserRoleEntity;
use Illuminate\Http\Request;

/**
 * Class UserRoleManager
 * @package App\Managers
 */
class UserRoleManager extends ManagerContract implements CRUDSInterface
{
    use FilterTrait;

    /**
     * UserRoleManager constructor.
     * @param Request $request
     * @param UserRoleRepository $repository
     */
    public function __construct(Request $request, UserRoleRepository $repository)
    {
        $this->request($request);
        $this->repository($repository);
    }

    /**
     * @return UserRoleRepository
     */
    protected function userRoleRepository()
    {
        return parent::repository();
    }

    /**
     * @return array
     */
    public function create()
    {
        $userRoleEntity = UserRoleEntity::load($this->requestInput());
        return $userRoleEntity->merge($this->userRoleRepository()->create($userRoleEntity->toArray(true)))->toArray();
    }

    /**
     * @param int $id
     * @return array
     */
    public function read($id)
    {
//        $user = User::getFromCacheId($id, function () use ($id) {
//            return $this->userRoleRepository()->findById($id);
//        });
//
//        return UserEntity::load($user)->toArray();
        return [];
    }

    /**
     * @param int $id
     * @return array
     */
    public function update($id)
    {
//        $userEntity = UserEntity::load($this->requestInput());
//        if ($this->requestInput('password')) {
//            $userEntity->setPassword($this->requestInput('password'));
//        }

//        $this->userRoleRepository()->updateById($id, $userEntity->toArray(true));

//        return $this->read($id);
        return [];
    }

    /**
     * @param int $id
     * @return array
     */
    public function delete($id)
    {
//        $user = $this->read($id);
//        $this->userRoleRepository()->deleteById($id);
//        return $user;
        return [];
    }

    /**
     * @return array
     */
    public function show()
    {
//        $tag = makeTagNameCache($this->filterToApply());
//
//        $users = User::getFromCacheTag($tag, function () {
//            return $this->userRoleRepository()->findWhere($this->filterToApply());
//        });
//
//        return UserEntity::toArrayMultiple($users);
        return [];
    }

    /**
     * @return array
     */
    protected function rules()
    {
        return [
            'user_id' => 'required|numeric',
            'role_id' => 'required|numeric',
        ];
    }

    /**
     * @return array
     */
    protected function filters()
    {
        return [
            'user_id' => [
                'type' => FilterType::NUMBER
            ],
            'role_id' => [
                'type' => FilterType::NUMBER
            ],
        ];
    }
}
