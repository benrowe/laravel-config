<?php

use Benrowe\Laravel\Config\Config;

/**
 * Tests to ensure invalid data is not allowed into the config (data that can't be persisted)
 */
class InvalidConfigTest extends PHPUnit_Framework_TestCase
{
    public function testNullData()
    {
        // invalid data
        $this->setExpectedException(InvalidArgumentException::class);
        new Config(null);

    }

    public function testNonScalarData()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        new Config(new stdClass);
    }

    /**
     * Ensure that non-scalar values are not valid
     */
    public function testNonScalarValue()
    {
        $cfg = new Config();
        $this->setExpectedException(InvalidArgumentException::class);
        $cfg->set('newkey', new stdClass);
    }

    /**
     * Ensure that objects in arrays are not allowed
     */
    public function testArrayNonScalar()
    {
        $cfg = new Config();
        $this->setExpectedException(InvalidArgumentException::class);
        $cfg->set('newkey', [new stdClass]);
    }

    public function testMultiDimentionalArrays()
    {
        $cfg = new Config();
        $this->setExpectedException(InvalidArgumentException::class);
        $cfg->set('newkey', [['string']]);
    }

    public function testAssocArray()
    {
        $cfg = new Config();
        $this->setExpectedException(InvalidArgumentException::class);
        $cfg->set('newkey', ['key' => 'value']);
    }
}
