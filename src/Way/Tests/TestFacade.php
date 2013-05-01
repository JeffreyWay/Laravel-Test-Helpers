<?php namespace Way\Tests;

abstract class TestFacade extends \PHPUnit_Framework_Assert {

    /**
     * Singleton
     * @var array
     */
    protected static $instance = array();

    /**
     * Intialization
     *
     * @param string $methodName
     * @param array $args
     */
    protected function fire($methodName, $args)
    {
        $methodName = $this->getMethod($methodName);

        // If the assertion exists, run it
        // Otherwise, throw an exception.
        if (method_exists(__CLASS__, $methodName))
        {
            return $this->callAssertion(__CLASS__, $methodName, $args);
        }

        throw new \BadMethodCallException;
    }

    /**
     * Calls PHPUnit assertion
     *
     * @param  string  $class
     * @param  string  $methodName
     * @param  array   $args
     * @return mixed
     */
    protected function callAssertion($class, $methodName, $args)
    {
        return call_user_func_array(array($class, $methodName), $args);
    }

    /**
     * Handle dynamic, static calls to the object.
     *
     * @param  string  $methodName
     * @param  array   $args
     * @return Should
     */
    public static function __callStatic($methodName, array $args)
    {
        $instance = static::getInstance();

        return $instance->fire($methodName, $args);
    }

    /**
     * Register or get singleton
     * @return TestFacade
     */
    public static function getInstance()
    {
        $class = get_called_class();

        // We need to make sure that both Assert
        // and Should can be used simulataneously.
        if (! isset(static::$instance[$class]))
        {
            static::$instance[$class] = new static;
        }


        return static::$instance[$class];
    }

    /**
     * Determines whether called method is an alias
     * @param  string $methodName
     * @return boolean
     */
    protected function isAnAlias($methodName)
    {
        return array_key_exists($methodName, $this->aliases);
    }

    /**
     * Register new aliases
     * @param  array  $aliases
     * @return void
     */
    public function registerAliases(array $aliases)
    {
        $this->aliases = array_merge($this->aliases, $aliases);
    }

    /**
     * Calculate the correct PHPUnit assertion name
     * @param  string $methodName
     * @return string
     */
    abstract protected function getMethod($methodName);
}