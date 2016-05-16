<?php

namespace Benrowe\Laravel\Config;

use Illuminate\Support\Arr;
use Benrowe\Laravel\Config\Modifiers\Modifier;
use Benrowe\Laravel\Config\Modifiers\Collection;


/**
 * Config class
 * Transforms a flattened key/value array configuration into a multi-dimensional
 * config handler
 */
class Config
{
    /**
     * @var string The delimiter used in the array keys to specify the heirachy
     */
    const KEY_DELIMITER = '.';

    /**
     * @var string the pattern to match array keys
     */
    const ARRAY_PATTERN = "/\[([0-9]+)\]$/";

    /**
     * The configuration data
     * @var array
     */
    private $data;

    public $modifiers;

    /**
     * constructor
     * The initial data
     *
     * @param array $data the flattened data
     */
    public function __construct($data)
    {
        $this->data = $this->dataDecode($data);
        $this->modifiers = new Collection;
    }

    /**
     * Reduce the configuration to a simple key/value array, despite the
     * heirachy of information
     *
     * @return array
     */
    public function flatten()
    {
        return $this->dataEncode($this->data);
    }

    /**
     * Create/Update a configuration value
     *
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value)
    {
        Arr::set($this->data, $key, $this->modifiers->convert($key, $value, Modifier::DIRECTION_FROM));
    }

    /**
     * Get the configuration value based on it's key
     *
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return $this->modifiers->convert(
            $key,
            Arr::get($this->data, $key, $default)
        );
    }

    /**
     * From an item from the configuration
     *
     * @param  string $key
     * @return boolean
     */
    public function forget($key)
    {
        return Arr::forget($this->data, $key);
    }

    /**
     * Clear all of the settings from the configuration
     *
     * @return boolean
     */
    public function clear()
    {
        if (!empty($this->data)) {
            $this->data = [];
            return true;
        }
        return false;
    }

    /**
     * Check if a configuration setting exists
     *
     * @param  string $key
     * @return boolean
     */
    public function exists($key)
    {
        return Arr::has($this->data, $key);
    }

    /**
     * Converts the flat key/value from the storage engine
     * to a heirachy structure based on the key sytax
     *
     * @param  array $data
     * @return array
     */
    private function dataDecode($data)
    {
        // preprocess the keys into a unique list where the array values are
        // stored against the same key

        $data = $this->unpackArray($data);

        $newData = [];
        foreach ($data as $key => $value) {
            Arr::set($newData, $key, $value);
        }

        return $newData;
    }

    /**
     * unpack the keys that are structured for arrays so that they no
     * longer have the [] syntax at the end. Rather they're now a proper
     * array.
     *
     * @param  array $data [description]
     * @return array
     */
    private function unpackArray($data)
    {
        $arrKeys = array_filter($data, function ($val) {
            return preg_match(self::ARRAY_PATTERN, $val);
        });
        foreach ($arrKeys as $key => $value) {
            $newKey = preg_replace(self::ARRAY_PATTERN, '', $key);
            if (!isset($data[$newKey])) {
                $data[$newKey] = [];
            }
            $data[$newKey][] = $value;
            unset($data[$key]);
        }
        return $data;
    }

    /**
     * Flatten a multi-dimensional array into a linear key/value list
     *
     * @param  array $data
     * @return array
     */
    private function dataEncode($data, $prefix = null)
    {
        $newData = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $newData = array_merge(
                    $newData,
                    $this->encodeArray($key, $value, $prefix)
                );
                continue;
            }
            $newData[$prefix.$key] = $value;
        }
        return $newData;
    }

    /**
     * Encode the array of values against the provided key
     *
     * @param  string $key
     * @param  array  $value  either an associative or keyed array
     * @param  string $prefix
     * @return array
     */
    private function encodeArray($key, array $value, $prefix = null)
    {
        $data = [];
        if (!Arr::isAssoc($value)) {
            foreach ($value as $index => $val) {
                $data[$prefix.$key.'['.$index.']'] = $val;
            }
            return $data;
        }
        return $this->dataEncode($value, $prefix.$key.self::KEY_DELIMITER);
    }
}
