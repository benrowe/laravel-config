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
     * Determine if this value is json
     *
     * @param  string $value
     * @return boolean
     */
    public function canHandle($value, $direction)
    {
        $canTo = $direction == self::DIRECTION_TO && is_string($value) && in_array($value[0], ['[', '{']);
        $canFrom = $direction == self::DIRECTION_FROM && gettype($value) == 'array' || gettype($value) == 'object';
        return $canTo xor $canFrom;
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
