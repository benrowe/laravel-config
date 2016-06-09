<?php

namespace Benrowe\Laravel\Config;

/**
 * Facilitates the conversion of hierarchical data into a flat structure
 *
 * @package Benrowe\Laravel\Config
 */
trait StorageConverterTrait
{
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
}
