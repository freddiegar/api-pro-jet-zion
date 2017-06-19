<?php

namespace FreddieGar\Base\Traits;

use Closure;
use Illuminate\Support\Facades\Cache;

/**
 * Trait CacheControlTrait
 * @package FreddieGar\Base\Traits
 */
trait CacheControlTrait
{
    /**
     * @var bool
     */
    static private $ENABLED_CACHE = true;

    /**
     * @var string
     */
    static private $TAG = null;

    /**
     * @param int $id
     * @return string
     */
    final static private function label($id)
    {
        return sprintf('%s:%d', static::tag(), $id);
    }

    /**
     * @return string
     */
    final static private function tag()
    {
        return static::$TAG ?: get_called_class();
    }

    /**
     * @param $id
     * @return bool
     */
    static public function hasInCacheId($id)
    {
        return Cache::has(self::label($id));
    }

    /**
     * @param $tag
     * @return bool
     */
    static public function hasInCacheTag($tag)
    {
        return Cache::tags(self::tag())->has($tag);
    }

    /**
     * @param $tag
     */
    final static public function setTag($tag)
    {
        static::$TAG = $tag;
    }

    /**
     * @param int $id
     * @param mixed $value
     */
    final static public function setCacheById($id, $value)
    {
        Cache::forever(self::label($id), $value);
    }

    /**
     * @param $tag
     * @param $value
     */
//    final static public function setCacheByTag($tag, $value)
//    {
//        Cache::tags(self::tag())->forever($tag, $value);
//    }

    /**
     * @param $id
     * @return mixed
     */
    final static public function getCacheById($id)
    {
        return Cache::get(self::label($id));
    }

    /**
     * @param $tag
     * @return mixed
     */
    final static public function getCacheByTag($tag)
    {
        return Cache::tags(self::tag())->get($tag);
    }

    /**
     * @param int $id
     * @param Closure $value
     * @return mixed
     */
    final static public function getFromCacheId($id, Closure $value)
    {
        if (static::hasEnableCache()) {
            if (static::hasInCacheId($id)) {
                $cache = static::getCacheById($id);
            } else {
                $cache = Cache::rememberForever(self::label($id), $value);
            }
        } else {
            $cache = $value();
        }
        return $cache;
    }

    /**
     * @param string $tag
     * @param Closure $value
     * @return mixed
     */
    final static public function getFromCacheTag($tag, Closure $value)
    {
        if (static::hasEnableCache()) {
            if (static::hasInCacheTag($tag)) {
                $cache = static::getCacheByTag($tag);
            } else {
                $cache = Cache::tags(self::tag())->rememberForever($tag, $value);
            }
        } else {
            $cache = $value();
        }
        return $cache;
    }

    /**
     * @param $id
     */
    final static public function unsetByLabel($id)
    {
        Cache::forget(self::label($id));
    }

    /**
     * Eraser cacge by tag
     */
    final static public function unsetByTag()
    {
        Cache::tags(self::tag())->flush();
    }

    /**
     * Enable cache
     */
    final static public function enableCache()
    {
        static::$ENABLED_CACHE = true;
        self::rebootCacheControlTrait();
    }

    /**
     * Enable cache
     */
    final static public function disableCache()
    {
        static::$ENABLED_CACHE = false;
        self::rebootCacheControlTrait();
    }

    /**
     * @return bool
     */
    final static private function hasEnableCache()
    {
        return static::$ENABLED_CACHE === true;
    }

    /**
     *
     */
    final static private function rebootCacheControlTrait()
    {
        static::flushEventListeners();
        static::clearBootedModels();
    }
}
