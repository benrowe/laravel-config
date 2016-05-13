<?php

namespace Benrowe\Laravel\Config\Modifiers;

/**
 * Json modifier for Config
 *
 * @package Benrowe\Laravel\Config\Modifiers
 */
class Json implements Modifier
{
    /**
     * Determine if this value is json
     *
     * @param  string $value
     * @return boolean
     */
    public function canHandle($value)
    {
        return is_string($value) && in_array($value[0], ['[', '{']);
    }

    public function convertTo($value)
    {
        return json_decode($value);
    }

    public function convertFrom($value)
    {
        return json_encode($value);
    }
}
