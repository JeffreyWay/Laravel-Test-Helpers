<?php

use Way\Tests\DataStore;

class DataStoreTest extends PHPUnit_Framework_TestCase {

    protected $store;

    public function setUp()
    {
        $this->store = new DataStore;
    }

    public function testTitle()
    {
        $this->assertRegExp(
            '/My [A-z]+ Title/',
            $this->store->getTitle()
        );
    }
}

