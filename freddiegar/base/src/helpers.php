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

if (!function_exists('shaN')) {
    /**
     * @param $string
     * @return string
     */
    function shaN($string)
    {
        return hash('sha256', $string);
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
            Illuminate\Support\Facades\Log::info(print_r(customizeTrace((new Exception())->getTrace()), true));
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
     * @param string $alias
     */
    function resource($app, $route, $controller, $alias = null)
    {
        $alias = $alias ?: $route;

        $app->post($route, ['as' => "api.{$alias}.create", 'uses' => "{$controller}@create"]);
        $app->get("{$route}/{id}", ['as' => "api.{$alias}.read", 'uses' => "{$controller}@read"]);
        $app->put("{$route}/{id}", ['as' => "api.{$alias}.update", 'uses' => "{$controller}@update"]);
        $app->patch("{$route}/{id}", ['as' => "api.{$alias}.patch", 'uses' => "{$controller}@update"]);
        $app->delete("{$route}/{id}", ['as' => "api.{$alias}.delete", 'uses' => "{$controller}@delete"]);
        $app->get($route, ['as' => "api.{$alias}.show", 'uses' => "{$controller}@show"]);
    }
}

if (!function_exists('responseJson')) {
    /**
     * @param string $content
     * @param int $status
     * @param array $headers
     * @return string
     */
    function responseJson($content = '', $status = 200, array $headers = [])
    {
        $status = empty($content) ? \Illuminate\Http\Response::HTTP_NO_CONTENT : $status;
        $options = env('APP_JSON_PRETTY_PRINT') === true ? JSON_PRETTY_PRINT : 0;

        return response()->json($content, $status, $headers, $options);
    }
}

if (!function_exists('customizeTrace')) {
    /**
     * @param array $exceptions
     * @return array
     */
    function customizeTrace(array $exceptions)
    {
        $trace = [];
        foreach ($exceptions as $index => $exception) {
            if (!isset($exception['file']) || strpos($exception['file'], '/vendor/') !== false) {
                continue;
            }
            $trace[] = $exception['file'] . ':' . $exception['line'];
        }

        return $trace;
    }
}
