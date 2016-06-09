<?php

namespace Benrowe\Laravel\Config\Modifiers;

/**
 * Modifier Interface
 *
 * All modifiers must implement from this interface to be compatiable
 *
 * @package Benrowe\Laravel\Config\Modifiers
 */
interface Modifier
{
    const DIRECTION_TO = 'to';
    const DIRECTION_FROM = 'from';

    /**
     * Determine if this modifier can handle converting to, based on the
     * supplied value
     *
     * @param string $value
     * @return boolean
     */
    public function canHandleTo($value);

    /**
     * Determine if this modifier can handle converting to, based on the
     * supplied value
     *
     * @param  mixed $value the external value to sniff
     * @return boolean
     */
    public function canHandleFrom($value);

    /**
     * Convert the string|array value to the desired result
     * @param  string|array $value
     * @return mixed
     */
    public function convertTo($value);

    /**
     * Conver the value from it's modified form, back into an array
     * @param  mixed $value
     * @return string
     */
    public function convertFrom($value);
}
