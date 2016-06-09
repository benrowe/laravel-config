<?php

/**
 * @author Ben Rowe <ben.rowe.83@gmail.com>
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */

namespace Benrowe\Laravel\Config\Modifiers;

/**
 * Json modifier for Config
 *
 * @package Benrowe\Laravel\Config\Modifiers
 */
class Json implements Modifier
{
    /**
     * Determine if this value is json (array or object)
     *
     * @param  string $value
     * @return boolean
     */
    public function canHandleFrom($value)
    {
        return gettype($value) == 'array' || gettype($value) == 'object';
    }

    /**
     * Determine if we can convert this string into json
     * @param  string $value
     * @return boolean
     */
    public function canHandleTo($value)
    {
        return is_string($value) && in_array($value[0], ['[', '{']);
    }

    /**
     * Convert the string into the json object/array
     *
     * @param  string|array $value
     * @return mixed
     */
    public function convertTo($value)
    {
        return json_decode($value);
    }

    /**
     * Convert the complex object back to a string
     *
     * @param  mixed $value
     * @return string
     */
    public function convertFrom($value)
    {
        return json_encode($value);
    }
}
