<?php

namespace App\Constants;

/**
 * Interface OperatorType
 * @package App\Constants
 */
interface OperatorType
{
    const EQUALS = '=';
    const LIKE = 'like';
    const MINOR = '<';
    const MINOR_EQUALS = '<=';
    const MAJOR = '>';
    const MAJOR_EQUALS = '>=';
}
