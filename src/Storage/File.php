<?php

namespace Benrowe\Laravel\Config\Storage;

/**
 * File storage for config
 *
 * @package Benrowe\Laravel\Config\Storage
 */
class File implements StorageInterface
{
    /**
     * @var string path to the config file
     */
    protected $filename;

    /**
     * @var string The state of the file as an md5 checksum
     * This is used to verify if the state of the file changed before it
     * is written back
     */
    private $fileState;

    /**
     * constructor
     *
     * @param string $file the file to persist the data two
     */
    public function __construct($file)
    {
        $this->filename = $file;
    }

    /**
     * @inheritdoc
     */
    public function save($key, $value)
    {
        $data = $this->load();
        $data = $this->delKey($data, $key);
        if (is_array($value)) {
            // remove all previous keys first

            foreach ($value as $i => $arrValue) {
                $data[$key.'['.$i.']'] = $arrValue;
            }
        } else {
            $data[$key] = $value;
        }

        $content = json_encode($data);
        $this->fileState = md5($content);

        file_put_contents($this->filename, $content);
    }

    /**
     * @inheritdoc
     */
    public function load()
    {
        $content = file_get_contents($this->filename);
        $this->fileState = md5($content);


        return json_decode($content, true);
    }

    /**
     * @inheritdoc
     */
    public function clear()
    {
        $clearState = '{}';
        file_put_contents($this->filename, $clearState);
        $this->fileState = md5($clearState);
    }

    /**
     * Remove the requested key from the data, reguardless if it's a single
     * value or an array of values
     *
     * @param  array  $data [description]
     * @param  string $key  the key to delete
     * @return array
     */
    private function delKey(array $data, $key)
    {
        foreach ($data as $dataKey => $dataValue) {
            if (strpos($dataKey, $key) === 0) {
                unset($data[$dataKey]);
            }
        }
        return $data;
    }
}
