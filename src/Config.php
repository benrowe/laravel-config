<?php

namespace Benrowe\Laravel\Config;

use Benrowe\Laravel\Config\Modifiers\Collection;
use Benrowe\Laravel\Config\Modifiers\Modifier;
use Benrowe\Laravel\Config\Storage\StorageInterface;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Arr;
use InvalidArgumentException;

/**
 * Config class
 * Transforms a flattened key/value array configuration into a multi-dimensional
 * config handler
 *
 * @package Benrowe\Laravel\Config
 */
class Config implements Repository
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
     * @var \Illuminate\Support\Arr
     */
    private $arrHelper;

    private $storage;

    /**
     * constructor
     * The initial data
     *
     * @param array|StorageInterface $data the flattened data
     * @param Arr|null $arrHelper the array helper
     */
    public function __construct($data = [], Arr $arrHelper = null)
    {
        // test $data is valid
        if (!is_array($data) && !($data instanceof StorageInterface)) {
            $msg = '$data must be either an array or an implementation of ';
            $msg .= StorageInterface::class;
            throw new \InvalidArgumentException($msg);
        }

        if ($arrHelper === null) {
            $arrHelper = new Arr;
        }
        $this->arrHelper = $arrHelper;

        if ($data instanceof StorageInterface) {
            $this->storage = $data;
            $data = $this->storage->load();
        }

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
    public function set($key, $value = null)
    {
        $value = $this->modifiers->convert(
            $key,
            $value,
            Modifier::DIRECTION_FROM
        );
        // verify the array
        if (!$this->isValidValue($value)) {
            $msg = 'Value for "'.$key.'" is invalid. ';
            $msg .= 'Must be scalar or an array of scalar values';
            throw new InvalidArgumentException($msg);
        }

        $this->arrHelper->set($this->data, $key, $value);
        $this->storage && $this->storage->save($key, $value);
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
            $this->arrHelper->get($this->data, $key, $default)
        );
    }

    /***
      * Get all of the configuration data in it's hierarchical state
      */
    public function all()
    {
        return $this->data;
    }

    /**
     * Remove an item from the configuration
     *
     * @param  string $key
     * @return boolean
     */
    public function forget($key)
    {
        if ($this->has($key)) {
            $this->arrHelper->forget($this->data, $key);
            return true;
        }
        return false;
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
            $this->storage && $this->storage->clear();
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
    public function has($key)
    {
        return $this->arrHelper->has($this->data, $key);
    }

    /**
     * Prepend a value onto the key.
     *
     * If that existing key is not  an array it will be converted into an array
     * and the the value will be the first element of the array
     *
     * @param  string $key
     * @param  mixed $value
     * @return void
     */
    public function prepend($key, $value)
    {
        $existing = $this->getAsArray($key);
        array_unshift($existing, $value);
        $this->set($key, $existing);
    }

    /**
     * Push a value onto the key
     *
     * If that existing key is not  an array it will be converted into an array
     * and the the value will be the first element of the array
     *
     * @param  string $key
     * @param  mixed $value
     * @return void
     */
    public function push($key, $value)
    {
        $existing = $this->getAsArray($key);
        array_push($existing, $value);
        $this->set($key, $existing);
    }

    /**
     * Get the value, as an array
     *
     * @param  string $key
     * @return array any existing value will be converted to the first element
     *               of the array
     */
    private function getAsArray($key)
    {
        $value = $this->get($key);
        if (!is_array($value)) {
            $value = !is_null($value) ? [$value] : [];
        }
        return $value;
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
            $this->arrHelper->set($newData, $key, $value);
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
     * @param string|null $prefix
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
     * @param  string|null $prefix
     * @return array
     */
    private function encodeArray($key, array $value, $prefix = null)
    {
        $data = [];
        if (!$this->arrHelper->isAssoc($value)) {
            foreach ($value as $index => $val) {
                $data[$prefix.$key.'['.$index.']'] = $val;
            }
            return $data;
        }
        return $this->dataEncode($value, $prefix.$key.self::KEY_DELIMITER);
    }

    /**
     * Validate the value as safe for this object
     *
     * @param  mixed  $value the value to test
     * @return boolean
     */
    private function isValidValue($value)
    {
        return
            is_scalar($value) ||
            (
                is_array($value) &&
                !$this->arrHelper->isAssoc($value) &&
                count($value) === count(array_filter($value, 'is_scalar'))
            );
    }
}
