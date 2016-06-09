<?php

namespace Benrowe\Laravel\Config\Modifiers;

/**
 * Boolean modifier for Config
 *
 * @package Benrowe\Laravel\Config\Modifiers
 */
class Boolean implements Modifier
{
    /**
     * Determine if this value is boolean
     *
     * @param  string $value
     * @return boolean
     */
    public function canHandleFrom($value)
    {
        return is_bool($value);
    }

    /**
     * Determine if we can convert this string into a boolean
     * @param  string $value
     * @return boolean
     */
    public function canHandleTo($value)
    {
        return $value === 'true' || $value === 'false';
    }

    /**
     * Convert the string into the boolean
     *
     * @param  string|array $value
     * @return boolean
     */
    public function convertTo($value)
    {
        return $value === 'true';
    }

    /**
     * Convert the complex object back to a string
     *
     * @param  bool $value
     * @return string
     */
    public function convertFrom($value)
    {
        return $value === true ? 'true' : 'false';
    }
}
