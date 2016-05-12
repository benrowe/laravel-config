<?php

namespace Benrowe\Laravel\Config;

use Illuminate\Support\Arr;

/**
 * Config class
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

    /**
     * constructor
     * The initial data
     *
     * @param array $data the flattened data
     */
    public function __construct($data)
    {
        $this->data = $this->dataDecode($data);
    }

    /**
     * [flatten description]
     * @return [type] [description]
     */
    public function flatten()
    {
        return $this->dataEncode($this->data);
    }

    /**
     * [set description]
     * @param [type] $key   [description]
     * @param [type] $value [description]
     */
    public function set($key, $value)
    {
        $this->data = Arr::set($this->data, $key, $value);
    }

    /**
     * Get the configuration value based on it's key
     *
     * @param  [type] $key     [description]
     * @param  [type] $default [description]
     * @return [type]          [description]
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->data, $key, $default);
    }

    /**
     * [forget description]
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
    public function forget($key)
    {
        return Arr::forget($this->data, $key);
    }

    /**
     * [exists description]
     * @param  [type] $key [description]
     * @return [type]      [description]
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
     * [dataEncode description]
     *
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    private function dataEncode($data)
    {
        return $data;
    }
}
