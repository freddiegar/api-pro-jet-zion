<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\UnauthorizedException;

trait BlameTrait
{
    /**
     * The name of the action creating.
     *
     * @var string
     */
    static private $CREATING = 'creating';

    /**
     * The name of the action updating.
     *
     * @var string
     */
    static private $UPDATING = 'updating';

    /**
     * The name of the action deleting.
     *
     * @var string
     */
    static private $DELETING = 'deleting';

    /**
     * The name of the "created by" column.
     *
     * @var string
     */
    static private $CREATED_BY = 'created_by';

    /**
     * The name of the "updated by" column.
     *
     * @var string
     */
    static private $UPDATED_BY = 'updated_by';

    /**
     * The name of the "deleted by" column.
     *
     * @var string
     */
    static private $DELETED_BY = 'deleted_by';

    /**
     * By default is that user guard logged
     * @var string
     */
    static private $GUARD_NAME = null;

    /**
     * By default is that user id logged
     * @var string
     */
    static private $CURRENT_USER_AUTHENTICATED = null;

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    static protected function bootBlameTrait()
    {
        foreach (static::blameActions() as $action) {
            $columns = static::blameColumnsByAction($action);
            static::{$action}(function ($model) use ($columns, $action) {
                foreach ($columns as $column) {
                    if (!$column) {
                        continue;
                    }

                    if (!Auth::guard(static::$GUARD_NAME)->check() && !self::$CURRENT_USER_AUTHENTICATED) {
                        throw new UnauthorizedException('User not authenticated to ' . $action . ' in ' . self::class);
                    }

                    $model->{$column} = self::$CURRENT_USER_AUTHENTICATED
                        ? self::$CURRENT_USER_AUTHENTICATED
                        : Auth::guard(static::$GUARD_NAME)->id();
                }

                return true;
            });
        }
    }

    /**
     * @return array
     */
    static private function blameActions()
    {
        return [
            static::$CREATING,
            static::$UPDATING,
            static::$DELETING,
        ];
    }

    /**
     * @param null $action
     * @return array|string
     */
    static private function blameColumnsByAction($action = null)
    {
        $columnByAction = [
            // Action created set created by and updated by, this equals to Lumen
            // Vien disableCreatedBy
            static::$CREATING => [
                static::$CREATED_BY,
                static::$UPDATED_BY
            ],
            static::$UPDATING => [
                static::$UPDATED_BY
            ],
            static::$DELETING => [
                static::$DELETED_BY
            ],
        ];

        return isset($columnByAction[$action])
            ? $columnByAction[$action]
            : $columnByAction;
    }

    /**
     * Disable actions for columns by blame
     */
    static public function disableBlame()
    {
        static::disableCreatedBy();
        static::disableUpdatedBy();
        static::disableDeletedBy();
    }

    /**
     * Disable created_by column
     */
    static public function disableCreatedBy()
    {
        // TODO: Improve this functionality using blameColumnsByAction() to determine columns dynamic
        static::$CREATED_BY = null;
        static::$UPDATED_BY = null;
    }

    /**
     * Disable updated_by column
     */
    static public function disableUpdatedBy()
    {
        static::$UPDATED_BY = null;
    }

    /**
     * Disable deleted_by column
     */
    static public function disableDeletedBy()
    {
        static::$DELETED_BY = null;
    }

    /**
     * Set guard to use blame
     * @param $guard
     */
    static public function setGuard($guard)
    {
        static::$GUARD_NAME = $guard;
    }

    /**
     * Set user to use in blame columns
     * @param $id
     */
    static public function setCurrentUserAuthenticated($id)
    {
        static::$CURRENT_USER_AUTHENTICATED = $id;
    }

    /**
     * Get guard used in blame
     * @return string
     */
    static public function getCurrentUserAuthenticated()
    {
        return static::$CURRENT_USER_AUTHENTICATED;
    }
}
