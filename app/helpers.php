<?php

if (!function_exists('isDevelopment')) {
    /**
     * @return bool
     */
    function isDevelopment()
    {
        return in_array(env('APP_ENV'), ['local', 'testing']);
    }
}

if (!function_exists('hashing')) {
    /**
     * @param $string
     * @return string
     */
    function hashing($string)
    {
        return app('hash')->make($string);
    }
}

if (!function_exists('randomHashing')) {
    /**
     * @param int $length
     * @return string
     */
    function randomHashing($length = 64)
    {
        return hashing(str_random($length));
    }
}

if (!function_exists('pretty')) {
    /**
     * @param mixed $var
     * @return string
     */
    function pretty($var)
    {
        return print_r($var, 1);
    }
}

if (!function_exists('now')) {
    /**
     * @return int
     */
    function now()
    {
        return (new \Carbon\Carbon())->toDateTimeString();
    }
}

if (!function_exists('method')) {
    /**
     * @param string $property
     * @return string
     */
    function method($property)
    {
        return (strpos($property, '_') !== false) ? camel_case($property) : $property;
    }
}

if (!function_exists('setter')) {
    /**
     * @param string $property
     * @return string
     */
    function setter($property)
    {
        return method($property);
    }
}

if (!function_exists('getter')) {
    /**
     * @param string $property
     * @return string
     */
    function getter($property)
    {
        return method($property);
    }
}

if (!function_exists('ll')) {
    /**
     * @param array ...$args
     * @return bool
     */
    function ll(...$args)
    {
        if (isDevelopment()) {
            foreach ($args as $arg) {
                Illuminate\Support\Facades\Log::info(print_r($arg, true));
            }
            return true;
        }

        return true;
    }
}

if (!function_exists('passwordIsValid')) {
    /**
     * @param $actual
     * @param $expected
     * @return boolean
     */
    function passwordIsValid($actual, $expected)
    {
        return Illuminate\Support\Facades\Hash::check($actual, $expected);
    }
}

if (!function_exists('filterArray')) {
    /**
     * @param $array
     * @return array
     */
    function filterArray($array)
    {
        foreach ($array as &$value) {
            if (is_array($value)) {
                $value = filterArray($value);
            }
        }

        return array_filter($array, function ($item) {
            return !empty($item) || $item === false || $item === 0;
        });
    }
}

if (!function_exists('resource')) {
    /**
     * @param mixed $app
     * @param string $route
     * @param string $controller
     */
    function resource($app, $route, $controller)
    {
        $app->post($route, ['as' => "api.{$route}.create", 'uses' => "{$controller}@create"]);
        $app->get("{$route}/{id}", ['as' => "api.{$route}.read", 'uses' => "{$controller}@read"]);
        $app->put("{$route}/{id}", ['as' => "api.{$route}.update", 'uses' => "{$controller}@update"]);
        $app->delete("{$route}/{id}", ['as' => "api.{$route}.delete", 'uses' => "{$controller}@delete"]);
        $app->get($route, ['as' => "api.{$route}.show", 'uses' => "{$controller}@show"]);
    }
}
