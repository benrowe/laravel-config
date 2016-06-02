<?php

namespace Benrowe\Laravel\Config\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Config Facades
 *
 * @package Benrowe\Laravel\Config\Facade
 */
class Config extends Facade
{
    /**
     * Define service name for facade
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'config-runtime';
    }
}
