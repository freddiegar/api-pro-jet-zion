<?php

namespace FreddieGar\Base\Traits;

use FreddieGar\Base\Constants\Event;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait BlameEventTrait
 * @package FreddieGar\Base\Traits
 */
trait BlameEventTrait
{
    /**
     * The "booting" method of the model.
     * @return void
     */
    static protected function bootBlameEventTrait()
    {
        foreach (static::blameEvents() as $event) {
            if ($event === Event::SAVED) {
                static::{$event}(function () {
                    // When model is saving, it enable blame columns for next process
                    static::enableBlame();
                    static::rebootBlameEventTrait();
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
    static public function rebootBlameEventTrait()
    {
        // Unregister and reload previuous events setup
        static::flushEventListeners();
        static::clearBootedModels();
    }
}
