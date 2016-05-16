<?php

namespace Benrowe\Laravel\Config\Modifiers;

interface Modifier
{
    const DIRECTION_TO = 'to';
    const DIRECTION_FROM = 'from';

    /**
     * @return boolean
     */
    public function canHandle($value, $direction);

    /**
     * Convert the string|array value to the desired result
     * @param  string|array $value
     * @return mixed
     */
    public function convertTo($value);

    /**
     * Conver the value from it's modified form, back into an array
     * @param  mixed $value [description]
     * @return string
     */
    public function convertFrom($value);
}