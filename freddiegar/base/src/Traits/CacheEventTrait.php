<?php

namespace FreddieGar\Base\Traits;

use FreddieGar\Base\Constants\Event;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait BlameEventTrait
 * @package FreddieGar\Base\Traits
 */
trait CacheEventTrait
{
    /**
     * The "booting" method of the model.
     * @return void
     */
    static protected function bootCacheEventTrait()
    {
        if (static::isEnableCache()) {
            static::{Event::CREATED}(function (Model $model) {
                /** @noinspection PhpUndefinedFieldInspection */
                static::setByLabel($model->id, $model->toArray());
                static::unsetByTag();
            });

            static::{Event::UPDATED}(function (Model $model) {
                /** @noinspection PhpUndefinedFieldInspection */
                static::setByLabel($model->id, $model->toArray());
                static::unsetByTag();
            });
        }

        static::{Event::DELETED}(function (Model $model) {
            /** @noinspection PhpUndefinedFieldInspection */
            static::unsetByLabel($model->id);
            static::unsetByTag();
        });
    }
}
