<?php
namespace nufficunit\extensions\docblock;

use PHPUnit\Framework\TestCase;
use nuffic\docblock\ReflectionBuilder;

/**
* 
*/
class TestBuilder extends TestCase
{
    public function testClassWithInputs()
    {
        $tester = new ReflectionBuilder(stubs\FooStub::className());
        var_dump($tester->getModel());
    }
}
