<?php

namespace Benrowe\Laravel\Config;

interface StorageInterface
{
    /**
     * Save the specific key & value
     *
     * @param string $key
     * @param string $value
     */
    public function save($key, $value);

    /**
     * Load all of the values from storage
     * 
     * @return array
     */
    public function load();

    /**
     * Clear all of the collected config
     *
     * @return void
     */
    public function clear();
}