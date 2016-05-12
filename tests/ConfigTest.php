<?php

use Benrowe\Laravel\Config\Config;

class ConfigTest extends PHPUnit_Framework_TestCase
{
    private $data = [
        'fooo' => 'bar',
        'foo.other' => 'bar',
        'foo.bar.something' => 'something',
        'foo.bar.list[0]' => 'VALUE:foo.bar.list[0]',
        'foo.bar.list[1]' => 'VALUE:foo.bar.list[1]',
        'foo.bar.list[2]' => 'VALUE:foo.bar.list[2]',
    ];

    /**
     * Test the ability to get values out
     * @return [type] [description]
     */
    public function testGet()
    {
        $cfg = new Config($this->data);
        $this->assertSame($cfg->get('fooo'), $this->data['fooo']);
        $this->assertSame($cfg->get('foo.other'), $this->data['foo.other']);
        $this->assertSame($cfg->get('foo.bar.something'), $this->data['foo.bar.something']);
        $this->assertNull($cfg->get('madeup'));

        $this->assertSame(count($cfg->get('foo.bar')), 2);
    }

    public function testGetCallback()
    {
        // $cfg = new Config(['callback' => '']);
        // $rand = rand();
    }

    public function testFlatten()
    {
        $data = $this->data;
        $cfg = new Config($data);
        $cfg->set('person.name', 'ben');
        $cfg->set('person.age', '100');
        $data['person.name'] = 'ben';
        $data['person.age'] = '100';

        $this->assertSame($cfg->flatten(), $data);
    }


    public function testForget()
    {
        $cfg = new Config($this->data);
        $cfg->forget('foo');
        $this->assertNull($cfg->get('foo'));

        $cfg->forget('foo.bar.something');
        $this->assertSame(count($cfg->get('foo.bar')), 1);

        $cfg = new Config($this->data);
        $cfg->forget('foo.bar');
        $this->assertSame(count($cfg->get('foo.bar', [])), 0);
    }

    public function testClear()
    {
        $cfg = new Config($this->data);
        $this->assertSame($cfg->get('foo'), $this->data['foo']);
        $this->assertTrue($cfg->clear());
        $this->assertFalse($cfg->clear());
        $this->assertNull($cfg->get('foo'));
    }

    public function testExists()
    {
        $cfg = new Config($this->data);
        $this->assertTrue($cfg->exists('foo'));
        $this->assertTrue($cfg->exists('foo.other'));
        $this->assertTrue($cfg->exists('foo.bar'));
        $this->assertTrue($cfg->exists('foo.bar'));
    }

    public function testSet()
    {
        $cfg = new Config($this->data);
        $newValue = rand();
        $cfg->set('foo', $newValue);
        $this->assertSame($cfg->get('foo'), $newValue);
        $cfg->set('foo.bar', $newValue);
        $this->assertSame($cfg->get('foo.bar'), $newValue);

    }

    public function testPush()
    {

    }

    public function testAll()
    {

    }
}
