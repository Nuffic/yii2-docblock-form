<?php

namespace tests\unit;

use nuffic\docblock\ReflectionBuilder;
use nuffic\docblock\tag\InputTag;
use tests\data\Bar;
use tests\data\Foo;
use tests\data\Person;

class BuilderTest extends \Codeception\Test\Unit
{
    public function testClassWithInputs()
    {
        $builder = new ReflectionBuilder(Foo::class);

        $model = $builder->getModel();

        $this->assertArrayHasKey('baz', $model->attributes);
    }

    public function testClassWithDefaultValues()
    {
        $builder = new ReflectionBuilder(Foo::class);

        $model = $builder->getModel();

        $this->assertEquals('bar', $model->baz);
    }

    public function testInitializedClass()
    {
        $builder = new ReflectionBuilder(new Foo([
            'baz' => 'hue'
        ]));

        $model = $builder->getModel();

        $this->assertEquals('hue', $model->baz);
    }

    public function testClassWithoutInputs()
    {
        $builder = new ReflectionBuilder(Bar::class);

        $model = $builder->getModel();

        $this->assertEmpty($model->attributes);
    }

    /**
     * @param array $attributes
     * @param bool $expectedResult
     * @dataProvider validationDataProvider
     */
    public function testValidation($attributes, $expectedResult)
    {
        $builder = new ReflectionBuilder(Person::class);
        $builder->getModel()->load($attributes, '');
        $this->assertEquals($expectedResult, $builder->getModel()->validate(array_keys($attributes)));
    }

    /**
     * @return array
     */
    public function validationDataProvider()
    {
        return [
            [['name' => ''], false],
            [['name' => 'ab'], false],
            [['name' => 'abc'], true],
            [['name' => 'abcd'], true],
            [['name' => 'abcde'], true],
            [['name' => 'abcdef'], true],
            [['name' => 'abcdefg'], true],
            [['name' => 'abcdefgh'], true],
            [['name' => 'abcdefghi'], true],
            [['name' => 'abcdefghij'], true],
            [['name' => 'abcdefghijk'], false],
        ];
    }

    public function testGetInputTags()
    {
        $builder = new ReflectionBuilder(Foo::class);
        $inputTags = $builder->getInputTags();
        $this->assertArrayHasKey('bar', $inputTags);
        $this->assertArrayHasKey('baz', $inputTags);
        /** @var InputTag $bar */
        $bar = $inputTags['bar'];
        $this->assertEquals('This is bar', $bar->getSummary());
        $this->assertEquals('test', $bar->getDefaultValue());
        $this->assertEquals('textInput', $bar->getMethod());
        $this->assertEquals([['type' => 'number']], $bar->getParameters());
    }
}
