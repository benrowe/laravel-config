<?php

use Benrowe\Laravel\Config\Config;

/**
 * Tests for Config class
 */
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
        $cfg->forget('fooo');
        $this->assertNull($cfg->get('fooo'));

        $cfg->forget('foo.bar.something');
        $this->assertSame(count($cfg->get('foo.bar')), 1);

        $cfg = new Config($this->data);
        $cfg->forget('foo.bar');
        $this->assertSame(count($cfg->get('foo.bar', [])), 0);
    }

    public function testClear()
    {
        $cfg = new Config($this->data);
        $this->assertSame($cfg->get('fooo'), $this->data['fooo']);
        $this->assertTrue($cfg->clear());
        $this->assertFalse($cfg->clear());
        $this->assertNull($cfg->get('foo'));
    }

    public function testHas()
    {
        $cfg = new Config($this->data);
        $this->assertTrue($cfg->has('fooo'));
        $this->assertTrue($cfg->has('foo.other'));
        $this->assertTrue($cfg->has('foo.bar'));
        $this->assertTrue($cfg->has('foo.bar'));
        $this->assertFalse($cfg->has('madeup'));
    }

    public function testSet()
    {
        $cfg = new Config($this->data);
        $newValue = rand();
        $cfg->set('fooo', $newValue);
        $this->assertSame($cfg->get('fooo'), $newValue);
        $cfg->set('foo.bar', $newValue);
        $this->assertSame($cfg->get('foo.bar'), $newValue);
    }

    public function testPrepend()
    {
        $cfg = new Config(['existing' => 'value']);

        // non-existing key
        $cfg->prepend('prepend', 'value');
        $this->assertSame($cfg->get('prepend')[0], 'value');

        $cfg->prepend('prepend', 'value');
        $cfg->prepend('prepend', 'value');
        $cfg->prepend('prepend', 'latest value');

        // add a few more values
        $this->assertSame(count($cfg->get('prepend')), 4);
        $this->assertSame($cfg->get('prepend')[0], 'latest value');

        $cfg->prepend('existing', '2nd value');
        $this->assertSame(count($cfg->get('existing')), 2);
        $this->assertSame($cfg->get('existing')[0], '2nd value');
        $this->assertSame($cfg->get('existing')[1], 'value');
    }

    public function testPush()
    {
        $cfg = new Config(['existing' => 'value']);

        // non-existing key
        $cfg->push('push', 'value');
        $this->assertSame($cfg->get('push')[0], 'value');

        $cfg->push('push', 'value');
        $cfg->push('push', 'value');
        $cfg->push('push', 'latest value');

        // add a few more values
        $this->assertSame(count($cfg->get('push')), 4);
        $this->assertSame($cfg->get('push')[3], 'latest value');

        $cfg->push('existing', '2nd value');
        $this->assertSame(count($cfg->get('existing')), 2);
        $this->assertSame($cfg->get('existing')[0], 'value');
        $this->assertSame($cfg->get('existing')[1], '2nd value');

    }

    public function testPrependPush()
    {
        $cfg = new Config([]);
        $cfg->prepend('prepend', 'value');

        // non-existing key
        $this->assertSame($cfg->get('prepend')[0], 'value');

        $cfg->prepend('prepend', 'other Value');
        $this->assertSame(count($cfg->get('prepend')), 2);
        $this->assertSame($cfg->get('prepend')[0], 'other Value');

        // push a value onto the end
        $cfg->push('prepend', 'last value');
        $this->assertSame(count($cfg->get('prepend')), 3);
        $this->assertSame($cfg->get('prepend')[0], 'other Value');
    }

    public function testAll()
    {
        $cfg = new Config($this->data);
        $this->assertSame($cfg->all(), [
            'fooo' => 'bar',
            'foo' => [
                'other' => 'bar',
                'bar' => [
                    'something' => 'something',
                    'list' => [
                        'VALUE:foo.bar.list[0]',
                        'VALUE:foo.bar.list[1]',
                        'VALUE:foo.bar.list[2]'
                    ]
                ]
            ],
        ]);
    }
}
