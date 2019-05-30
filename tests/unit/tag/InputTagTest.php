<?php

namespace tests\unit\tag;

use Codeception\Test\Unit;
use nuffic\docblock\tag\InputTag;

class InputTagTest extends Unit
{
    public function testGetMethodAndParameters()
    {
        $tag = new InputTag('textarea', ['a' => 'b']);
        $this->assertEquals('textarea', $tag->getMethod());
        $this->assertEquals(['a' => 'b'], $tag->getParameters());
    }

    public function testSetAndGetSummary()
    {
        $tag = new InputTag;
        $result = $tag->setSummary('Hello!');
        $this->assertEquals('Hello!', $result->getSummary());
        $this->assertEquals($result, $tag);
    }

    public function testSetAndGetDefaultValue()
    {
        $tag = new InputTag;
        $result = $tag->setDefaultValue('123');
        $this->assertEquals('123', $result->getDefaultValue());
        $this->assertEquals($result, $tag);
    }

    public function testCreate()
    {
        $tag = InputTag::create('someInput{"type":"number"}');
        $this->assertEquals('someInput', $tag->getMethod());
        $this->assertEquals(['type' => 'number'], $tag->getParameters());
    }

    public function testCreateEmpty()
    {
        $tag = InputTag::create('');
        $this->assertEquals('textInput', $tag->getMethod());
        $this->assertEquals([], $tag->getParameters());
    }

    public function testCreateFromMalformedString()
    {
        $tag = InputTag::create('12345');
        $this->assertNull($tag);
    }

    public function testToString()
    {
        $tag = new InputTag('someInput', ['type' => 'number']);
        $this->assertEquals('someInput{"type":"number"}', $tag->__toString());
    }
}
