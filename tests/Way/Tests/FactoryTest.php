<?php

use Way\Tests\Factory;
use Way\Tests\DataStore;
use Mockery as m;

class FactoryTest extends PHPUnit_Framework_TestCase {

    public function setUp()
    {
        m::mock('Post');
        $this->mockedDb = m::mock('\Illuminate\Database\DatabaseManager');
    }

    public function tearDown()
    {
        m::close();
    }

    public function testCanCreateFactory()
    {
        $factory = m::mock('Way\Tests\Factory', [$this->mockedDb, new DataStore])->makePartial();

        $factory->shouldReceive('getColumns')
                ->andReturn([
                    'occupation' => 'string',
                    'age'        => 'integer'
                ]);

        $factory->shouldReceive('getDataType')->andReturn('string');

        $post = $factory->fire('Post');

        $this->assertInstanceOf('Post', $post);
        $this->assertObjectHasAttribute('occupation', $post);
        $this->assertObjectHasAttribute('age', $post);
        $this->assertInternalType('string', $post->occupation);
        $this->assertInternalType('integer', $post->age);
    }

    public function testCanOverrideDefaults()
    {
        $factory = m::mock('Way\Tests\Factory', [$this->mockedDb, new DataStore])->makePartial();
        $factory->shouldReceive('getColumns')->andReturn(['email' => 'sample@example.com']);
        $factory->shouldReceive('getDataType')->andReturn('string');

        $post = $factory->fire('Post', ['email' => 'foo']);
        $this->assertEquals('foo', $post->email);
    }

}