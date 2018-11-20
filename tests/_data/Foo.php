<?php

namespace tests\data;

/**
 * @property string $baz
 */
class Foo extends \yii\base\Component
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
     * @validator {"class": "\\yii\\validators\\RequiredValidator"}
     * @input textarea
     */
    public function setBaz($value)
    {
        $this->_baz = $value;
    }
}
