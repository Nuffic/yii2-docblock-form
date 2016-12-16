<?php
namespace nufficunit\extensions\docblock;

use PHPUnit\Framework\TestCase;
use nuffic\docblock\ReflectionBuilder;

/**
* 
*/
class BuilderTest extends TestCase
{
    public function testClassWithInputs()
    {
        $builder = new ReflectionBuilder(stubs\FooStub::className());

        $model = $builder->getModel();
        
        $this->assertArrayHasKey('baz', $model->attributes);
    }

    public function testClassWithDefaultValues()
    {
        $builder = new ReflectionBuilder(stubs\FooStub::className());

        $model = $builder->getModel();

        $this->assertEquals('bar', $model->baz);
    }

    public function testInitializedClass()
    {
        $builder = new ReflectionBuilder(new stubs\FooStub([
            'baz' => 'hue'
        ]));

        $model = $builder->getModel();

        $this->assertEquals('hue', $model->baz);
    }

    public function testClassWithoutInputs()
    {
        $builder = new ReflectionBuilder(stubs\BarStub::className());

        $model = $builder->getModel();

        $this->assertEmpty($model->attributes);
    }
}
