<?php

namespace App\Traits;

use App\Exceptions\ProJetZionException;
use Illuminate\Support\Facades\Auth;

trait BlameTrait
{
    /**
     * The name of the action creating.
     *
     * @var string
     */
    protected static $CREATING = 'creating';

    /**
     * The name of the action updating.
     *
     * @var string
     */
    protected static $UPDATING = 'updating';

    /**
     * The name of the action deleting.
     *
     * @var string
     */
    protected static $DELETING = 'deleting';

    /**
     * The name of the "created by" column.
     *
     * @var string
     */
    protected static $CREATED_BY = 'created_by';

    /**
     * The name of the "updated by" column.
     *
     * @var string
     */
    protected static $UPDATED_BY = 'updated_by';

    /**
     * The name of the "deleted by" column.
     *
     * @var string
     */
    protected static $DELETED_BY = 'deleted_by';

    /**
     * By default is that user guard logged
     * @var string
     */
    protected static $GUARD_NAME = null;

    /**
     * By default is that user id logged
     * @var string
     */
    protected static $CURRENT_USER_AUTHENTICATED = null;

    /**
     * Indicates if the model can blade columns
     * @var bool
     */
    protected static $BLAME_ENABLE = true;


    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function bootBlameTrait()
    {
        foreach (static::blameActionByColumn() as $column => $action) {
            $column = static::blameColumnByAction($action);
            if (is_string($column)) {
                static::{$action}(function ($model) use ($column, $action) {
                    if (!static::$BLAME_ENABLE) {
                        return true;
                    }

                    if (!Auth::guard(static::$GUARD_NAME)->check() && !self::$CURRENT_USER_AUTHENTICATED) {
                        throw new ProJetZionException('User not authenticated to ' . $action . ' in ' . self::class);
                    }

                    $model->{$column} = self::$CURRENT_USER_AUTHENTICATED
                        ? self::$CURRENT_USER_AUTHENTICATED
                        : Auth::guard(static::$GUARD_NAME)->id();

                    return true;
                });
            }
        }
    }

    /**
     * @param null $column
     * @return array|string
     */
    private static function blameActionByColumn($column = null)
    {
        $actionByColumn = [
            static::$CREATED_BY => static::$CREATING,
            static::$UPDATED_BY => static::$UPDATING,
            static::$DELETED_BY => static::$DELETING,
        ];

        return isset($actionByColumn[$column])
            ? $actionByColumn[$column]
            : $actionByColumn;
    }

    /**
     * @param null $action
     * @return array|string
     */
    private static function blameColumnByAction($action = null)
    {
        $columnByAction = [
            static::$CREATING => static::$CREATED_BY,
            static::$UPDATING => static::$UPDATED_BY,
            static::$DELETING => static::$DELETED_BY,
        ];

        return isset($columnByAction[$action])
            ? $columnByAction[$action]
            : $columnByAction;
    }

    /**
     * Disable actions for columns blame
     */
    public static function disableBlame()
    {
        static::$BLAME_ENABLE = false;
    }

    /**
     * Disable set for created_by column
     */
    public static function disableCreatedBy()
    {
        static::$CREATED_BY = false;
    }

    /**
     * Disable set for updated_by column
     */
    public static function disableUpdatedBy()
    {
        static::$UPDATED_BY = false;
    }

    /**
     * Disable set for updated_by column
     */
    public static function disableDeletedBy()
    {
        static::$DELETED_BY = false;
    }

    /**
     * Set guard to use in register
     *
     * @param $guard
     */
    public static function setGuard($guard)
    {
        static::$GUARD_NAME = $guard;
    }

    /**
     * Set guard to use in register
     *
     * @param $user_id
     *
     * @internal param $guard
     */
    public static function setCurrentUserAuthenticated($user_id)
    {
        static::$CURRENT_USER_AUTHENTICATED = $user_id;
    }

    /**
     * Set guard to use in register
     * @return string
     * @internal param $guard
     */
    public static function getCurrentUserAuthenticated()
    {
        return static::$CURRENT_USER_AUTHENTICATED;
    }
}
