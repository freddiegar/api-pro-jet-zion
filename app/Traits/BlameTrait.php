<?php

namespace App\Traits;

use App\Constants\BlameColumn;
use App\Constants\BlameEvent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\UnauthorizedException;

trait BlameTrait
{
    /**
     * The name of the "created by" column.
     * @var string
     */
    static private $CREATED_BY = BlameColumn::CREATED_BY;

    /**
     * The name of the "updated by" column.
     * @var string
     */
    static private $UPDATED_BY = BlameColumn::UPDATED_BY;

    /**
     * The name of the "deleted by" column.
     * @var string
     */
    static private $DELETED_BY = BlameColumn::DELETED_BY;

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
     * @return void
     */
    static protected function bootBlameTrait()
    {
        foreach (static::blameEvents() as $event) {
            if ($event === BlameEvent::SAVED) {
                static::{$event}(function (Model $model) {
                    // When model was saved enabled blame columns for anothers statements
                    $model::enableBlame();
                });
                continue;
            }

            $columns = static::blameColumnsByEvent($event);
            static::{$event}(function (Model $model) use ($columns, $event) {
                foreach ($columns as $column) {
                    if (!$column) {
                        logger("Cancel set columns in event [{$event}] to " . get_class($model));
                        continue;
                    }

                    $model->{$column} = self::getCurrentUserAuthenticated($event);
                }

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
            BlameEvent::CREATING,
            BlameEvent::UPDATING,
            BlameEvent::DELETING,
            BlameEvent::SAVED,
        ];
    }

    /**
     * @param string $event
     * @return array
     */
    static private function blameColumnsByEvent($event)
    {
        $columnByEvent = [
            // Event created set created by and updated by, this equals to Lumen
            // If this change please going see disableCreatedBy method
            BlameEvent::CREATING => [
                static::$CREATED_BY,
                static::$UPDATED_BY,
            ],
            BlameEvent::UPDATING => [
                static::$UPDATED_BY,
            ],
            BlameEvent::DELETING => [
                static::$DELETED_BY,
            ],
        ];

        return $columnByEvent[$event];
    }

    /**
     * Enable blame to all columns
     * @return void
     */
    static public function enableBlame()
    {
        static::enableCreatedBy();
        static::enableUpdatedBy();
        static::enableDeletedBy();
    }

    /**
     * Disable blame to all columns
     * @return void
     */
    static public function disableBlame()
    {
        static::disableCreatedBy();
        static::disableUpdatedBy();
        static::disableDeletedBy();
    }

    /**
     * Disable save created by column
     * @return void
     */
    static public function disableCreatedBy()
    {
        // TODO: Improve this functionality using blameColumnsByEvent() to determine columns dynamic
        static::$CREATED_BY = null;
        static::$UPDATED_BY = null;
    }

    /**
     * Disable update updated by column
     * @return void
     */
    static public function disableUpdatedBy()
    {
        static::$UPDATED_BY = null;
    }

    /**
     * Disable update deleted by column
     * @return void
     */
    static public function disableDeletedBy()
    {
        static::$DELETED_BY = null;
    }

    /**
     * Enable save created by column
     * @return void
     */
    static public function enableCreatedBy()
    {
        static::$CREATED_BY = BlameColumn::CREATED_BY;
    }

    /**
     * Enable update updated by column
     * @return void
     */
    static public function enableUpdatedBy()
    {
        static::$UPDATED_BY = BlameColumn::UPDATED_BY;
    }

    /**
     * Enable update deleted by column
     * @return void
     */
    static public function enableDeletedBy()
    {
        static::$DELETED_BY = BlameColumn::DELETED_BY;
    }

    /**
     * Set guard to use in blame
     * @param string $guard
     * @return void
     */
//    static public function setGuard($guard)
//    {
//        static::$GUARD_NAME = $guard;
//    }

    /**
     * Set user to use in blame columns
     * @param int $id
     * @return void
     */
    static public function setCurrentUserAuthenticated($id)
    {
        static::$CURRENT_USER_AUTHENTICATED = $id;
    }

    /**
     * Get guard used in blame
     * @param string $event
     * @return int
     */
    static public function getCurrentUserAuthenticated($event)
    {
        if (self::$CURRENT_USER_AUTHENTICATED) {
            return self::$CURRENT_USER_AUTHENTICATED;
        }

        if (Auth::guard(static::$GUARD_NAME)->check()) {
            self::setCurrentUserAuthenticated(Auth::guard(static::$GUARD_NAME)->id());
            return self::getCurrentUserAuthenticated($event);
        }

        throw new UnauthorizedException('User not authenticated to ' . $event . ' in ' . self::class);
    }
}
