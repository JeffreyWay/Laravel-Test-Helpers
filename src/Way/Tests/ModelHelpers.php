<?php namespace Way\Tests;

use Mockery;

trait ModelHelpers {

    public function tearDown()
    {
        Mockery::close();
    }

    public function assertValid($model)
    {
        $this->assertRespondsTo('validate', $model, "The 'validate' method does not exist on this model.");
        $this->assertTrue($model->validate(), 'Model did not pass validation.');
    }

    public function assertNotValid($model)
    {
        $this->assertRespondsTo('validate', $model, "The 'validate' method does not exist on this model.");
        $this->assertFalse($model->validate(), 'Did not expect model to pass validation.');
    }

    public function assertBelongsToMany($parent, $child)
    {
        $this->assertRelationship($parent, $child, 'belongsToMany');
    }

    public function assertBelongsTo($parent, $child)
    {
        $this->assertRelationship($parent, $child, 'belongsTo');
    }

    public function assertHasMany($relation, $class)
    {
        $this->assertRelationship($relation, $class, 'hasMany');
    }

    public function assertHasOne($relation, $class)
    {
        $this->assertRelationship($relation, $class, 'hasOne');
    }

    public function assertMorphMany($relation, $class, $morphable)
    {
        $this->assertRelationship($relation, $class, 'morphMany');
    }

    public function assertMorphTo($relation, $class)
    {
        $this->assertRelationship($relation, $class, 'morphTo');
    }

    public function assertRespondsTo($method, $class, $message = null)
    {
        $message = $message ?: "Expected the '$class' class to have method, '$method'.";

        $this->assertTrue(
            method_exists($class, $method),
            $message
        );
    }

    public function assertRelationship($relationship, $class, $type)
    {
        $this->assertRespondsTo($relationship, $class);

        $args = $this->getArgumentsRelationship($relationship, $class, $type);

        $class = Mockery::mock($class."[$type]");

        switch(count($args))
        {
            case 1 :
                $class->shouldReceive($type)
                      ->once()
                      ->with('/' . str_singular($relationship) . '/i');
                break;
            case 2 :
                $class->shouldReceive($type)
                      ->once()
                      ->with('/' . str_singular($relationship) . '/i', $args[1]);
                break;
            case 3 :
                $class->shouldReceive($type)
                      ->once()
                      ->with('/' . str_singular($relationship) . '/i', $args[1], $args[2]);
                break;
            case 4 :
                $class->shouldReceive($type)
                      ->once()
                      ->with('/' . str_singular($relationship) . '/i', $args[1], $args[2], $args[3]);
                break;
            default :
                $class->shouldReceive($type)
                      ->once();
                break;
        }

        $class->$relationship();
    }

    public function getArgumentsRelationship($relationship, $class, $type) {
        $mocked = Mockery::mock($class."[$type]");

        $mocked->shouldReceive($type)
              ->once()
              ->andReturnUsing(function ()
              {
                return func_get_args();
              });

        return $mocked->$relationship();
    }

}
