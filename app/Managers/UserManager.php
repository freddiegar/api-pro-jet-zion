<?php

namespace App\Managers;

use App\Constants\UserStatus;
use App\Contracts\Repositories\UserRepository;
use App\Entities\UserEntity;
use App\Models\User;
use FreddieGar\Base\Constants\BlameColumn;
use FreddieGar\Base\Constants\FilterType;
use FreddieGar\Base\Contracts\Commons\ManagerContract;
use FreddieGar\Base\Contracts\Interfaces\CacheControlInterface;
use FreddieGar\Base\Contracts\Interfaces\CRUDSInterface;
use FreddieGar\Base\Traits\CacheControlTrait;
use FreddieGar\Base\Traits\FilterTrait;
use Illuminate\Http\Request;

/**
 * Class UserManager
 * @package App\Managers
 */
class UserManager extends ManagerContract implements CRUDSInterface, CacheControlInterface
{
    use FilterTrait;
    use CacheControlTrait;

    /**
     * UserManager constructor.
     * @param Request $request
     * @param UserRepository $repository
     */
    public function __construct(Request $request, UserRepository $repository)
    {
        $this::setTag(User::class);
        $this->request($request);
        $this->repository($repository);
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
        if (self::existLabel($id)) {
            return UserEntity::load($this->getByLabel($id))->toArray();
        }

        return UserEntity::load($this->userRepository()->findById($id))->toArray();
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

        return $this->read($id);
    }

    /**
     * @param int $id
     * @return array
     */
    public function delete($id)
    {
        $user = $this->read($id);
        $this->userRepository()->deleteById($id);
        return $user;
    }

    /**
     * @return array
     */
    public function show()
    {
        $tag = makeTagNameCache($this->filterToApply());

        if (self::existTag($tag)) {
            self::getByTag($tag);
        }

        return UserEntity::toArrayMultiple($this->userRepository()->findWhere($this->filterToApply()));
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
