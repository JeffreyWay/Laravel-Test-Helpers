<?php namespace Way\Tests;

class DataStore {

    /**
     * Registered name field value
     *
     * @var string
     */
    protected $name;

    /**
     * Get random string
     *
     * @return string
     */
    public function getString()
    {
        $strings = array(
            'foo', 'bar', 'baz', 'bizz'
        );

        return $strings[array_rand($strings, 1)];
    }

    /**
     * Get random integer
     *
     * @return intger
     */
    public function getInteger()
    {
        return rand(1, 100);
    }

    /**
     * Get random name
     *
     * @return string
     */
    public function getName()
    {
        $names = array(
            'Joe', 'Frank', 'Keith', 'John', 'Jeffrey', 'Matt', 'Sarah', 'Lauren', 'Kim'
        );

        return $this->name = $names[array_rand($names, 1)];
    }

    /**
     * Get random email
     *
     * @return string
     */
    public function getEmail()
    {
        // If a name property is set on the instance, then use that name as
        // the "to" for the email address. Otherwise, get a random one.
        $name = isset($this->name)
            ? $this->name
            : $this->getName();

        $name = strtolower($name);

        return "{$name}@example.com";
    }

    /**
     * Get telephone number
     *
     * @return string
     */
    public function getPhone()
    {
        return '555-55-5555';
    }

    /**
     * Get random age
     *
     * @return string
     */
    public function getAge()
    {
        return $this->getInteger();
    }

    /**
     * Get some random Lorem text
     *
     * @return string
     */
    public function getText()
    {
        return "Lorem ipsum dolor sit amet, consectetur adipiscing elit. " .
                "Fusce tortor nulla, cursus eu pellentesque sed, accumsan " .
                "a risus. Pellentesque et commodo lectus. In ac urna.";
    }

    /**
     * Get current MySQL-formatted date
     *
     * @return string
     */
    public function getDatetime()
    {
        return date('Y-m-d H:i:s');
    }

}