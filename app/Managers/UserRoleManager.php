<?php

namespace App\Managers;

use FreddieGar\Base\Constants\BlameColumn;
use FreddieGar\Base\Constants\FilterType;
use FreddieGar\Base\Contracts\Commons\ManagerContract;
use FreddieGar\Base\Contracts\Interfaces\CRUDSInterface;
use FreddieGar\Base\Traits\FilterTrait;
use FreddieGar\Rbac\Contracts\Repositories\UserRoleRepository;
use FreddieGar\Rbac\Entities\UserRoleEntity;
use FreddieGar\Rbac\Models\UserRole;
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
        $userRole = new UserRoleEntity();
        $userRole->userId($this->requestInput('user_id'));
        $userRole->roleId($this->requestInput('role_id'));
        return $userRole->merge($this->userRoleRepository()->create($userRole->toArray(true)))->toArray();
    }

    /**
     * @param int $id
     * @return array
     */
    public function read($id)
    {
        $userRole = UserRole::getFromCacheId($id, function () use ($id) {
            return $this->userRoleRepository()->findById($id);
        });

        return UserRoleEntity::load($userRole)->toArray();
    }

    /**
     * @param int $id
     * @return array
     */
    public function update($id)
    {
        $userRole = UserRoleEntity::load($this->requestInput());
        $this->userRoleRepository()->updateById($id, $userRole->toArray(true));
        return $this->read($id);
    }

    /**
     * @param int $id
     * @return array
     */
    public function delete($id)
    {
        $userRole = $this->read($id);
        $this->userRoleRepository()->deleteById($id);
        return $userRole;
    }

    /**
     * @return array
     */
    public function show()
    {
        $tag = makeTagNameCache($this->filterToApply());

        $userRoles = UserRole::getFromCacheTag($tag, function () {
            return $this->userRoleRepository()->findWhere($this->filterToApply());
        });

        return UserRoleEntity::toArrayMultiple($userRoles);
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
            BlameColumn::CREATED_BY => [
                'type' => FilterType::NUMBER
            ]
        ];
    }
}
