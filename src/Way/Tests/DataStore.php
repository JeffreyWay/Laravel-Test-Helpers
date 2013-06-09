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

        return $this->random($strings);
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
     * Get random decimal
     *
     * @return integer
     */
    public function getDecimal()
    {
        return $this->getInteger() + .50;
    }
    
    /**
     * Get random float
     *
     * @return float
     */
    public function getFloat()
    {
        return $this->getDecimal();
    }

    
    /**
     * Get boolean
     *
     * @return boolean
     */
    public function getBoolean()
    {
        return false;
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

        return $this->name = $this->random($names);
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
        $random = $this->getInteger();

        return "{$name}-{$random}@example.com";
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
     * Get some random Street name
     *
     * @return string
     */
    public function getStreet()
    {
        $streets = array('Baker', 'First', 'Main', 'Second', 'Broad');

        return $this->random($streets);
    }

    /**
     * Get some random street extension
     *
     * @return string
     */
    public function getStreetExtension()
    {
        $extensions = array('Ave', 'St', 'Circle', 'Road');

        return $this->random($extensions);
    }

    /**
     * Get some city name
     *
     * @return string
     */
    public function getCity()
    {
        $cities = array('Nashville', 'Chattanooga', 'London', 'San Francisco', 'Bucksnort');

        return $this->random($cities);
    }

    /**
     * Get some random state
     *
     * @return string
     */
    public function getState()
    {
        $states = array('TN', 'WA', 'MA', 'CA');

        return $this->random($states);
    }

    /**
     * Get some random zip code
     *
     * @return string
     */
    public function getZip()
    {
        $zips = array(37121, 42198, 34189, 37115);

        return $this->random($zips);
    }

    /**
     * Get dummy website address
     *
     * @return string
     */
    public function getWebsite()
    {
        return 'http://example.com';
    }

    /**
     * Get random address
     *
     * @return string
     */
    public function getAddress()
    {
        $address = $this->getInteger() . ' ' . $this->getStreet() . ' ' . $this->getStreetExtension() . PHP_EOL;
        $address .= $this->getCity() . ', ' . $this->getState() . ' ' . $this->getZip();

        return $address;
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

    /**
     * Return random item from provided array
     *
     * @param  array $arr
     * @return string
     */
    protected function random(array $arr)
    {
        return $arr[array_rand($arr, 1)];
    }

}
