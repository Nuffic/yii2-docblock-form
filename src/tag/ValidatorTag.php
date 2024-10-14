<?php

namespace nuffic\docblock\tag;

use phpDocumentor\Reflection\DocBlock\Tags\BaseTag;
use phpDocumentor\Reflection\DocBlock\Tags\Factory\StaticMethod;
use Webmozart\Assert\Assert;
use yii\helpers\Json;

/**
 * Class ValidatorTag
 * @package nuffic\docblock\tag
 */
class ValidatorTag extends BaseTag implements StaticMethod
{
    /**
     * @var string register that this is the validator tag.
     */
    protected string $name = 'validator';

    /**
     * @var array Validator configuration for Yii::createObject method
     */
    private $_validatorConfig = [];

    /**
     * Initializes this tag with the method name and params.
     *
     * @param array $validatorConfig
     */
    public function __construct($validatorConfig = [])
    {
        $this->_validatorConfig = $validatorConfig;
    }

    public function getValidatorConfig()
    {
        return $this->_validatorConfig;
    }

    public function __toString(): string
    {
        return Json::encode($this->_validatorConfig);
    }

    public static function create(string $body)
    {
        Assert::string($body);
        return new static(Json::decode($body));
    }
}
