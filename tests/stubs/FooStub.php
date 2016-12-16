<?php

namespace nufficunit\extensions\docblock\stubs;

/**
 * 
 * property string $baz
 */
class FooStub extends \yii\base\Component
{
    /**
     * @input textInput[{"type":"number"}]
     */
    public $bar = 'test';

    private $_baz = 'bar';

    public function getBaz()
    {
        return $this->_baz;
    }

    /**
     * @input textarea
     */
    public function setBaz($value)
    {
        $this->_baz = $value;
    }
}
