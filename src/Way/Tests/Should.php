<?php namespace Way\Tests;

class Should extends TestFacade {
    protected $aliases = array(
        'have' => 'assertContains',
        'eq'   => 'assertEquals'
    );

    protected function getMethod($methodName)
    {
        // If an alias is registered,
        // that takes precendence.
        if ($this->isAnAlias($methodName))
        {
            return $this->aliases[$methodName];
        }

        // If the method begins with "be" or "have," then strip
        // that off. The remainder is the correct assertion name.
        // beTrue => True ... haveCount => Count
        else if (preg_match('/^(?:be|have)(.+)$/i', $methodName, $matches))
        {
            return 'assert' . ucwords($matches[1]);
        }

        // If all else fails, just pluralize the word
        // equal => assertEquals
        return 'assert' . ucwords($methodName) . 's';
    }
}