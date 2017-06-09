<?php

namespace App\Traits;

trait LoaderTrait
{
    /**
     * @param array $data
     * @return static
     */
    public static function load(array $data)
    {
        $entity = new static();

        foreach ($data as $field => $value) {
            if (in_array($field, $entity->fields())) {
                $method = (strpos($field, '_') !== false) ? camel_case($field) : $field;
                file_put_contents('/var/www/log/log.log', __METHOD__ . ': ' . print_r("{$field} use: ", true), FILE_APPEND);
                if (method_exists($entity, $method)) {
                    file_put_contents('/var/www/log/log.log', 'Method'. "\n", FILE_APPEND);
                    // Set property within method
                    $entity->{$method}($value);
                } else {
                    // Set property derectly
                    file_put_contents('/var/www/log/log.log', 'Property'. "\n", FILE_APPEND);
                    $entity->{$field} = $value;
                }
            }
        }

        return $entity;
    }
}
