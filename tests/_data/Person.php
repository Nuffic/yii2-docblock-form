<?php

namespace tests\data;

class Person
{
    /**
     * @input
     * @validator {"class": "\\yii\\validators\\RequiredValidator"}
     * @validator {"class": "\\yii\\validators\\StringValidator", "min": 3, "max": 10}
     */
    public $name;
}
