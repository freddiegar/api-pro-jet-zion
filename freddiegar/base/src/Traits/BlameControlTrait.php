<?php

namespace FreddieGar\Base\Traits;

use FreddieGar\Base\Constants\BlameColumn;
use FreddieGar\Base\Constants\Event;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\UnauthorizedException;

/**
 * Trait BlameControlTrait
 * @package FreddieGar\Base\Traits
 */
trait BlameControlTrait
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
    static protected function bootBlameControlTrait()
    {
        foreach (static::blameEvents() as $event) {
            if ($event === Event::SAVED) {
                static::{$event}(function () {
                    // When model is saving, it enable blame columns for next process
                    static::enableBlame();
                });
                continue;
            }

            if ($columns = static::blameColumnsByEvent($event)) {
                static::{$event}(function (Model $model) use ($columns, $event) {
                    foreach ($columns as $column) {
                        $model->{$column} = static::getCurrentUserAuthenticated($event, class_basename($model));
                    }
                    return true;
                });
            }
        }
    }

    /**
     *
     */
    static public function rebootBlameControlTrait()
    {
        // Unregister and reload previuous events setup
        static::flushEventListeners();
        static::clearBootedModels();
    }

    /**
     * @return array
     */
    final static protected function blameEvents()
    {
        return [
            Event::CREATING,
            Event::UPDATING,
            Event::DELETING,
            Event::SAVED,
        ];
    }

    /**
     * @param string $event
     * @return array|null
     */
    final static protected function blameColumnsByEvent($event)
    {
        $columnByEvent = [
            Event::CREATING => [
                static::$CREATED_BY,
            ],
            Event::UPDATING => [
                static::$UPDATED_BY,
            ],
            Event::DELETING => [
                static::$DELETED_BY,
            ],
        ];

        // Events without columns are eliminate
        $columnByEvent = filterArray($columnByEvent);

        return isset($columnByEvent[$event]) ? $columnByEvent[$event] : null;
    }

    /**
     * Enable blame to all columns
     * @return void
     */
    final static public function enableBlame()
    {
        static::enableCreatedBy();
        static::enableUpdatedBy();
        static::enableDeletedBy();
    }

    /**
     * Enable save created by column
     * @return void
     */
    final static public function enableCreatedBy()
    {
        static::$CREATED_BY = BlameColumn::CREATED_BY;
        static::rebootBlameControlTrait();
    }

    /**
     * Enable update updated by column
     * @return void
     */
    final static public function enableUpdatedBy()
    {
        static::$UPDATED_BY = BlameColumn::UPDATED_BY;
        static::rebootBlameControlTrait();
    }

    /**
     * Enable update deleted by column
     * @return void
     */
    final static public function enableDeletedBy()
    {
        static::$DELETED_BY = BlameColumn::DELETED_BY;
        static::rebootBlameControlTrait();
    }

    /**
     * Disable blame to all columns
     * @return void
     */
    final static public function disableBlame()
    {
        static::disableCreatedBy();
        static::disableUpdatedBy();
        static::disableDeletedBy();
    }

    /**
     * Disable save created by column
     * @return void
     */
    final static public function disableCreatedBy()
    {
        static::$CREATED_BY = null;
        static::rebootBlameControlTrait();
    }

    /**
     * Disable update updated by column
     * @return void
     */
    final static public function disableUpdatedBy()
    {
        static::$UPDATED_BY = null;
        static::rebootBlameControlTrait();
    }

    /**
     * Disable update deleted by column
     * @return void
     */
    final static public function disableDeletedBy()
    {
        static::$DELETED_BY = null;
        static::rebootBlameControlTrait();
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
    final static public function setCurrentUserAuthenticated($id)
    {
        static::$CURRENT_USER_AUTHENTICATED = $id;
    }

    /**
     * Get guard used in blame
     * @param string $event
     * @param string $model
     * @return int
     */
    final static public function getCurrentUserAuthenticated($event, $model)
    {
        if (static::$CURRENT_USER_AUTHENTICATED) {
            return static::$CURRENT_USER_AUTHENTICATED;
        }

        if (Auth::guard(static::$GUARD_NAME)->check()) {
            static::setCurrentUserAuthenticated(Auth::guard(static::$GUARD_NAME)->id());
            return static::getCurrentUserAuthenticated($event, $model);
        }

        throw new UnauthorizedException(trans('exceptions.unauthorized', compact('event', 'model')));
    }
}
