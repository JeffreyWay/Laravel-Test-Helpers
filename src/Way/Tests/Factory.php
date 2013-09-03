<?php namespace Way\Tests;

use \Illuminate\Database\DatabaseManager;
use Illuminate\Support\Str;

class ModelNotFoundException extends \Exception {}

class Factory {

    /**
     * Model instance
     */
    protected $class;

    /**
     * Pluralized form of classname
     *
     * @var string
     */
    protected $tableName;

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
     * For retrieving dummy data
     *
     * @var DataStore
     */
    protected $dataStore;

    /**
     * Constructor
     *
     * @param $db
     */
    public function __construct(DatabaseManager $db = null, DataStore $dataStore = null)
    {
        $this->db = $db ?: \App::make('db');
        $this->dataStore = $dataStore ?: new DataStore;
    }

    /*
     * Create a factory AND save it to the DB.
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
    public static function attributesFor($class, array $columns = array())
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
        $this->tableName = $this->parseTableName($class);
        $this->class = $this->createModel($class);

        // First, we dynamically fetch the fields for the table
        $columns = $this->getColumns($this->tableName);

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
     * Calulate the table name
     *
     * @param  string $class
     * @return string
     */
    protected function parseTableName($class)
    {
        return $this->isNamespaced($class)
            ? snake_case(str_plural(substr(strrchr($class, '\\'), 1)))
            : snake_case(str_plural($class));
    }

    /**
     * Initialize the given model
     *
     * @param  string $class
     * @return object
     */
    protected function createModel($class)
    {
        $class = Str::studly($class);
        if (class_exists($class))
            return new $class;

        throw new ModelNotFoundException;
    }

    /**
     * Is the model namespaced?
     *
     * @param  string $class
     * @return boolean
     */
    protected function isNamespaced($class)
    {
        return str_contains($class, '\\');
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
            if ($relation = $this->hasForeignKey($key))
            {
                $this->class->$key = $this->createRelationship($relation);
                continue;
            }
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

        $method = $this->getFakeMethodName($name, $col);
        if (method_exists($this->dataStore, $method))
        {
            return $this->dataStore->{$method}();
        }

        throw new \Exception('Could not calculate correct fixture method.');
    }

    /**
     * Build the faker method
     *
     * @param  string $field
     * @return string
     */
    protected function getFakeMethodName($field, $col)
    {
        // We'll first try to get special fields
        // That way, we can insert appropriate
        // values, like an email or first name
        $method = $this->checkForSpecialField($field);

        // If we couldn't, we'll instead grab
        // the datatype for the field, and
        // generate a value to fit that.
        if (!$method) $method = $this->getDataType($col);

        // Build the method name
        return 'get' . ucwords($method);
    }

    /**
     * Search for special field names
     *
     * @param  string $field
     * @return mixed
     */
    protected function checkForSpecialField($field)
    {
        $special = array(
            'name', 'email', 'phone',
            'age', 'address', 'city',
            'state', 'zip', 'street',
            'website', 'title'
        );

        return in_array($field, $special) ? $field : false;
    }

    /**
     * Is the field a foreign key?
     *
     * @param  string $field
     * @return mixed
     */
    protected function hasForeignKey($field)
    {
        // Do we need to create a relationship?
        // Look for a field, like author_id or author-id
        if (static::$isSaving and preg_match('/([A-z]+)[-_]id$/i', $field, $matches))
        {
            return $matches[1];
        }

        return false;
    }

    /**
     * Create a new factory and return its id
     *
     * @param  string $class
     * @return id
     */
    protected function createRelationship($class)
    {
        $parent = get_class($this->class);
        $namespace = $this->isNamespaced($parent)
                        ? str_replace(substr(strrchr($parent, '\\'), 1), '', $parent)
                        : null;

        return static::create($namespace.$class)->id;
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
