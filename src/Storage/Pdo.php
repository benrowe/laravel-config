<?php

namespace Benrowe\Laravel\Config\Storage;

use PDO;

/**
 * @package Benrowe\Laravel\Config\Storage
 */
class Pdo implements StorageInterface
{
    /**
     * @var PDO
     */
    private $pdo;

    /**
     * @var string
     */
    private $tableName;

    /**
     * @var array
     */
    private $sqlQueries = [
        'clear' => 'DELETE FROM %tablename%',
        'delete' => 'DELETE FROM %tablename% WHERE LOWER(LEFT(`key`, ?)) = ?',
        'load' => 'SELECT `key`, `value` FROM %tablename%',
        'save' => 'INSERT INTO %tablename% (`key`, `value`) VALUES (?,?)'
    ];

    /**
     * constructor
     *
     * @param PDO    $pdo        instance of pdo to execute the queries against
     * @param string $tableName  the table name to reference
     * @param array $sqlQueries custom queries
     */
    public function __construct(PDO $pdo, $tableName = 'config', array $sqlQueries = [])
    {
        $this->pdo = $pdo;
        $this->tableName = $tableName;
        $this->setSqlQueries($sqlQueries);
    }
    /**
     * Set the sql queries to be used
     *
     * @param array $queries additional queries to be used. Overrides the base
     *                       ones provided with this storage adapter
     */
    public function setSqlQueries(array $queries)
    {
        $this->sqlQueries = array_merge($this->sqlQueries, $queries);
    }

    /**
     * @inheritdoc
     */
    public function save($key, $value)
    {
        $this->delKey($key);

        if (is_array($value)) {
            foreach ($value as $i => $arrValue) {
                $this->saveKey($key.'['.$i.']', $arrValue);
            }
            return;
        }

        $this->saveKey($key, $value);
    }

    /**
     * Save the specific key into the database
     *
     * @param  string $key
     * @param  string|int|float $value
     */
    private function saveKey($key, $value)
    {
        $sql = $this->getSqlQuery('save');
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$key, $value]);
    }

    /**
     * @inheritdoc
     */
    public function load()
    {
        $sql = $this->getSqlQuery('load');
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $results = [];
        foreach ($stmt->fetchAll() as $row) {
            $results[$row['key']] = $row['value'];
        }
        return $results;
    }

    /**
     * @inheritdoc
     */
    public function clear()
    {
        $this->pdo->exec($this->getSqlQuery('clear'));
    }

    /**
     * Generate the requested sql statement based on the provided name
     *
     * @param  string $name the query as referenced by it's name
     * @param  array  $vars any additional variables needed to generate the sql
     * @return string
     */
    private function getSqlQuery($name, array $vars = [])
    {
        $sql = $this->sqlQueries[$name];
        $vars = array_merge(['tablename' => $this->tablename], $vars);
        foreach ($vars as $key => $value) {
            $sql = str_replace("%$key%", $value, $sql);
        }

        return $sql;
    }

    /**
     * Delete the requested key from the database storage
     *
     * @param  string $key
     */
    private function delKey($key)
    {
        $sql = $this->getSqlQuery('delete');
        $stmt = $this->pdo->prepare($sql);
        $params = [strlen($key), $key];
        $stmt->execute($params);
    }
}
