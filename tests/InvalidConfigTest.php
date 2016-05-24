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

    public function testObjData()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        new Config(new stdClass);
    }

    public function testNonStringForValue()
    {
        $cfg = new Config();
        $this->setExpectedException(InvalidArgumentException::class);
        $cfg->set('newkey', new stdClass);
    }
}
