<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\UnauthorizedException;

trait BlameTrait
{
    /**
     * The name of the event creating.
     *
     * @var string
     */
    static private $CREATING = 'creating';

    /**
     * The name of the event updating.
     *
     * @var string
     */
    static private $UPDATING = 'updating';

    /**
     * The name of the event deleting.
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
        foreach (static::blameEvents() as $event) {
            $columns = static::blameColumnsByEvent($event);
            static::{$event}(function (Model $model) use ($columns, $event) {
                foreach ($columns as $column) {
                    if (!$column) {
                        logger('Cancel set column in ' . $event);
                        continue;
                    }

                    if (!Auth::guard(static::$GUARD_NAME)->check() && !self::$CURRENT_USER_AUTHENTICATED) {
                        throw new UnauthorizedException('User not authenticated to ' . $event . ' in ' . self::class);
                    }

                    $model->{$column} = self::getCurrentUserAuthenticated();
                }

                $model::enableBlame();

                return true;
            });
        }
    }

    /**
     * @return array
     */
    static private function blameEvents()
    {
        return [
            static::$CREATING,
            static::$UPDATING,
            static::$DELETING,
        ];
    }

    /**
     * @param null $event
     * @return array|string
     */
    static private function blameColumnsByEvent($event = null)
    {
        $columnByEvent = [
            // Event created set created by and updated by, this equals to Lumen
            // Vien disableCreatedBy
            static::$CREATING => [
                static::$CREATED_BY,
                static::$UPDATED_BY,
            ],
            static::$UPDATING => [
                static::$UPDATED_BY,
            ],
            static::$DELETING => [
                static::$DELETED_BY,
            ],
        ];

        return isset($columnByEvent[$event])
            ? $columnByEvent[$event]
            : $columnByEvent;
    }

    /**
     * Enable blame to all columns
     */
    static public function enableBlame()
    {
        static::enableCreatedBy();
        static::enableUpdatedBy();
        static::enableDeletedBy();
    }

    /**
     * Disable blame to all columns
     */
    static public function disableBlame()
    {
        static::disableCreatedBy();
        static::disableUpdatedBy();
        static::disableDeletedBy();
    }

    /**
     * Disable save created_by column
     */
    static public function disableCreatedBy()
    {
        // TODO: Improve this functionality using blameColumnsByEvent() to determine columns dynamic
        static::$CREATED_BY = null;
        static::$UPDATED_BY = null;
    }

    /**
     * Disable update updated_by column
     */
    static public function disableUpdatedBy()
    {
        static::$UPDATED_BY = null;
    }

    /**
     * Disable update deleted_by column
     */
    static public function disableDeletedBy()
    {
        static::$DELETED_BY = null;
    }

    /**
     * Enable save created_by column
     */
    static public function enableCreatedBy()
    {
        static::$DELETED_BY = 'created_by';
    }

    /**
     * Enable update updated_by column
     */
    static public function enableUpdatedBy()
    {
        static::$DELETED_BY = 'updated_by';
    }

    /**
     * Enable update deleted_by column
     */
    static public function enableDeletedBy()
    {
        static::$DELETED_BY = 'deleted_by';
    }

    /**
     * Set guard to use blame
     * @param string $guard
     */
    static public function setGuard($guard)
    {
        static::$GUARD_NAME = $guard;
    }

    /**
     * Set user to use in blame columns
     * @param int $id
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
        return self::$CURRENT_USER_AUTHENTICATED
            ? self::$CURRENT_USER_AUTHENTICATED
            : Auth::guard(static::$GUARD_NAME)->id();
    }
}
