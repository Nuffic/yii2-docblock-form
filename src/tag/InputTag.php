<?php

namespace nuffic\docblock\tag;

use phpDocumentor\Reflection\DocBlock\Tags\BaseTag;
use phpDocumentor\Reflection\DocBlock\Tags\Factory\StaticMethod;
use Webmozart\Assert\Assert;
use yii\helpers\Json;

/**
 * Class InputTag
 * @package nuffic\docblock\tag
 */
class InputTag extends BaseTag implements StaticMethod
{
    /**
     * @var string register that this is the input tag.
     */
    protected $name = 'input';

    /**
     * @var string
     */
    private $_method;

    /**
     * @var array
     */
    private $_parameters = [];

    /**
     * Initializes this tag with the method name and params.
     *
     * @param string $method
     * @param array $parameters
     */
    public function __construct($method, $parameters = [])
    {
        Assert::string($method);
        Assert::isArray($parameters);

        $this->_method = $method;
        $this->_parameters = $parameters;
    }

    /**
     * @return string Method name in \yii\widgets\ActiveField
     */
    public function getMethod()
    {
        return $this->_method;
    }

    /**
     * @return array Method parameters in \yii\widgets\ActiveField
     */
    public function getParameters()
    {
        return $this->_parameters;
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        return $this->_method . ($this->_parameters ? Json::encode($this->_parameters) : '');
    }

    /**
     * @inheritDoc
     * @return InputTag|null
     */
    public static function create($body)
    {
        Assert::string($body);

        if (!empty($body) && !preg_match('/(?P<method>[a-zA-Z_]+)(?P<data>.*)/', $body, $matches)) {
            return null;
        }
        $method = isset($matches['method']) ? $matches['method'] : 'textInput';
        $parameters = (array) Json::decode(isset($matches['data']) ? $matches['data'] : '[]');
        return new static($method, $parameters);
    }
}
