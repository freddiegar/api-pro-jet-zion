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

if (!function_exists('logger')) {
    /**
     * @param mixed $log
     * @return boolean
     */
    function logger($log)
    {
        if (isDevelopment()) {
            return Illuminate\Support\Facades\Log::info(print_r($log, 1));
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
