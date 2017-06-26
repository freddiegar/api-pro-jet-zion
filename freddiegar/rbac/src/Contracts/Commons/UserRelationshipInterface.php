<?php

namespace FreddieGar\Rbac\Contracts\Commons;

/**
 * Interface UserRelationshipInterface
 * @package FreddieGar\Rbac\Contracts\Commons
 */
interface UserRelationshipInterface
{
    /**
     * @param $user_id
     * @return mixed
     */
    public function createdBy($user_id);

    /**
     * @param $user_id
     * @return mixed
     */
    public function updatedBy($user_id);

    /**
     * @param $user_id
     * @return array
     */
    public function roles($user_id);
}
