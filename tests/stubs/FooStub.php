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
    public $bar = 'hue';

    public function getBaz()
    {
        return 'kala';
    }

    /**
     * @input textarea
     */
    public function setBaz()
    {
        
    }
}
