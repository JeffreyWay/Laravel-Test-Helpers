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

        $class = Mockery::mock($class."[$type]");

        $class->shouldReceive($type)
              ->with('/' . str_singular($relationship) . '/i')
              ->once();

        $class->$relationship();
    }

}
