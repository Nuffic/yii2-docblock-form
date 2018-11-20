<?php

namespace nuffic\docblock\tag;

use phpDocumentor\Reflection\DocBlock\Tag;
use yii\helpers\Json;

/**
 * Class ValidatorTag
 * @package nuffic\docblock\tag
 */
class ValidatorTag extends Tag
{
    /**
     * @var array Validator configuration for Yii::createObject method
     */
    public $validatorConfig;

    /**
     * {@inheritdoc}
     */
    public function setContent($content)
    {
        parent::setContent($content);
        $this->validatorConfig = Json::decode($content);
        return $this;
    }
}
