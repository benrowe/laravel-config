<?php

namespace Benrowe\Laravel\Config\Storage;

use Predis\Client;

/**
 * Redis store for config
 *
 * @package Benrowe\Laravel\Config\Storage
 */
class Redis implements StorageInterface
{
    private $redis;
    private $hash;

    /**
     * Constructor
     *
     * @param Client $redis [description]
     * @param string $hash  [description]
     */
    public function __construct(Client $redis, $hash = 'config')
    {
        $this->redis = $redis;
        $this->hash = $hash;
    }

    /**
     * @inheritdoc
     */
    public function save($key, $value)
    {
        $this->delKey($key);

        $this->redis->hset($this->hash, $key, $value);

        if (is_array($value)) {
            foreach ($value as $i => $arrValue) {
                $k = $key.'['.$i.']';
                $this->redis->hset($this->hash, $k, $arrValue);
            }
            return;
        }
        $this->redis->hset($this->hash, $key, $value);
    }

    /**
     * @inheritdoc
     */
    public function load()
    {
        return $this->redis->hgetall($this->hash);
    }

    /**
     * @inheritdoc
     */
    public function clear()
    {
        $this->redis->del($this->hash);
    }

    private function delKey($key)
    {
        $keys = $this->redis->hkeys($this->hash);
        foreach ($keys as $k) {
            if (strpos($k, $key) === 0) {
                $this->redis->hdel($k);
            }
        }
    }
}
