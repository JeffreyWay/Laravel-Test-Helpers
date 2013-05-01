<?php namespace Way\Tests;

use \Illuminate\Database\DatabaseManager;

class Factory {

    /**
     * Model instance
     */
    protected $class;

    /**
     * Pluralized form of classname
     * @var string
     */
    protected $tableName;

    /**
     * Registered name field value
     *
     * @var string
     */
    protected $name;

    /**
     * DB Layer
     *
     * @var Illuminate\Database\DatabaseManager
     */
    protected $db;

    /**
     * Remembers table fields for factories
     *
     * @var array
     */
    protected static $columns;

    /**
     * Whether models are being
     * saved to the DB
     *
     * @var boolean
     */
    protected static $isSaving = false;

    /**
     * Constructor
     *
     * @param $db
     */
    public function __construct(DatabaseManager $db = null)
    {
        $this->db = $db ?: \App::make('db');
    }

    /*
     * Create a factory AND save
     // * it to the DB.
     *
     * @param  string $class
     * @param  array  $columns
     * @return boolean
     */
    public static function create($class, array $columns = array())
    {
        static::$isSaving = true;

        $instance = static::make($class, $columns);

        $instance->save();

        return $instance;
    }

    /**
     * Create factory and return its attributes as an array
     *
     * @param  string $class
     * @param  array  $columns
     * @return array
     */
    public static function attributesFor($class, $columns = array())
    {
        return static::make($class, $columns)->toArray();
    }

    /**
     * Create a new factory. Factory::post() is equivalent
     * to Factory::make('Post'). Use this method when you need to
     * specify a namespace: Factory::make('Models\Post');
     *
     * You can also override fields. This is helpful for
     * testing validations: Factory::make('Post', ['title' => null])
     *
     * @param  $class
     * @param  array $columns Overrides
     * @return object
     */
    public static function make($class, $columns = array())
    {
        $instance = new static;

        return $instance->fire($class, $columns);
    }

    /**
     * Set dummy data on fields
     *
     * @param $class Name of class to create factory for
     * @param $overrides
     *
     * @return object
     */
    public function fire($class, array $overrides = array())
    {
        $this->class = new $class; // Post
        $this->tableName = str_plural($class); // Posts

        // First, we dynamically fetch the fields for the table
        $columns = $this->getColumns($this->getTableName());

        // Then, we set dummy value on the model.
        $this->setColumns($columns);

        // Finally, if they specified any overrides, like
        // Factory::make('Post', ['title' => null]),
        // we'll make those take precendence.
        $this->applyOverrides($overrides);

        // And then return the new class
        return $this->class;
    }

    /**
     * Determine what the table name is. So, Factory('Post') will
     * look for a table name, called posts. If the class has a
     * namespace, we will strip off everything but the file bit.
     * So Factory::make('Models\Post') will still look for a posts table.
     *
     * @return string
     */
    protected function getTableName()
    {
        if (str_contains($this->tableName, '\\'))
        {
            $tableName = substr(strrchr($this->tableName, '\\'), 1);

            return $tableName;
        }

        return $this->tableName;
    }

    /**
     * If overrides are set, then
     * override default values with them.
     *
     * @return void
     */
    protected function applyOverrides(array $overrides)
    {
        foreach ($overrides as $field => $value)
        {
           $this->class->$field = $value;
        }
    }

    /**
     * Fetch the table fields for the class.
     *
     * @param  string $tableName
     * @return array
     */
    public function getColumns($tableName)
    {
        // We only want to fetch the table details
        // once. We'll store these fields with a
        // $columns property for future fetching.
        if (isset(static::$columns[$this->tableName]))
        {
            return static::$columns[$this->tableName];
        }

        // This will only run the first time the factory is created.
        return static::$columns[$this->tableName] = $this->db->getDoctrineSchemaManager()->listTableDetails($tableName)->getColumns();
    }

    /**
     * Set fields for object
     *
     * @param array $columns
     */
    protected function setColumns(Array $columns)
    {
        foreach($columns as $key => $col)
        {
            $this->class->$key = $this->setColumn($key, $col);
        }
    }

    /**
     * Set single column
     *
     * @param string $name
     * @param string $col
     */
    protected function setColumn($name, $col)
    {
        if ($name === 'id') return;

        // Do we need to create a relationship?
        // Look for a field, like author_id or author-id
        if (static::$isSaving and preg_match('/([A-z]+)[-_]id$/i', $name, $matches))
        {
            // If found, create the relationship
            return $this->createRelationship($matches[1]);
        }

        // We'll first try to get special fields
        // That way, we can insert appropriate
        // values, like an email or first name
        if (preg_match('/name|email|phone|age/i', $name, $matches))
        {
            $method = $matches[0];
        }

        // If we couldn't, we'll instead grab
        // the datatype for the field, and
        // generate a value to fit that.
        else
        {
            $method = $this->getDataType($col);
        }

        // Create the method name to call and call it
        $method = 'get' . ucwords($method);

        if (method_exists($this, $method))
        {
            return $this->{$method}();
        }

        throw new \Exception('Could not calculate correct fixture method.');
    }

    /**
     * Create a new factory and return its id
     *
     * @param  string $class
     * @return id
     */
    protected function createRelationship($class)
    {
        return static::create($class)->id;
    }

    /**
     * Calculate the data type for the field
     *
     * @param  string $col
     * @return string
     */
    public function getDataType($col)
    {
        return $col->getType()->getName();
    }

    /**
     * Get random string
     *
     * @return string
     */
    protected function getString()
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
    protected function getInteger()
    {
        return rand(1, 100);
    }

    /**
     * Get random name
     *
     * @return string
     */
    protected function getName()
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
    protected function getEmail()
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
    protected function getPhone()
    {
        return '555-55-5555';
    }

    /**
     * Get random age
     *
     * @return string
     */
    protected function getAge()
    {
        return $this->getInteger();
    }

    /**
     * Get some random Lorem text
     *
     * @return string
     */
    protected function getText()
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
    protected function getDatetime()
    {
        return date('Y-m-d H:i:s');
    }

    /**
     * Handle dynamic factory creation calls,
     * like Factory::user() or Factory::post()
     *
     * @param  string $class The model to mock
     * @param  array $args
     * @return object
     */
    public static function __callStatic($class, $overrides)
    {
        // A litle weird. TODO
        $overrides = isset($overrides[0]) ? $overrides[0] : $overrides;
        $instance = new static;

        return $instance->fire($class, $overrides);
    }

}
