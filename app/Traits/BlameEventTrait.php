<?php

namespace App\Traits;

use App\Constants\BlameEvent;
use Illuminate\Database\Eloquent\Model;

trait BlameEventTrait
{
    /**
     * The "booting" method of the model.
     * @return void
     */
    static protected function bootBlameEventTrait()
    {
        foreach (static::blameEvents() as $event) {
            if ($event === BlameEvent::SAVED) {
                static::{$event}(function () {
                    // When model is saving, it enable blame columns for next process
                    static::enableBlame();
                    // Unregister and reload previuous events setup
                    static::flushEventListeners();
                    static::clearBootedModels();
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
}
