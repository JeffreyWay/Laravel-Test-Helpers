<?php namespace Way\Tests;

require_once 'TestFacade.php';

class Assert extends TestFacade {
    protected $aliases = array(
        'eq'       => 'assertEquals',
        'has'      => 'assertContains',
        'type'     => 'assertInternalType',
        'instance' => 'assertInstanceOf'
    );

    protected function getMethod($methodName)
    {
        // If an alias is registered,
        // that takes precendence.
        if ($this->isAnAlias($methodName))
        {
            return $this->aliases[$methodName];
        }

        return 'assert' . ucwords($methodName);
    }
}
