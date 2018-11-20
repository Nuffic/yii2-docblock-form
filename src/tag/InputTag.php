<?php

namespace nuffic\docblock\tag;

use phpDocumentor\Reflection\DocBlock\Tag;
use yii\helpers\Json;

/**
 * Class InputTag
 * @package nuffic\docblock\tag
 */
class InputTag extends Tag
{
    /**
     * @var string Method name in \yii\widgets\ActiveField
     */
    public $method = 'textInput';

    /**
     * @var array
     */
    public $parameters = [];

    /**
     * {@inheritdoc}
     */
    public function setContent($content)
    {
        parent::setContent($content);

        if (preg_match('/(?P<method>[a-zA-Z_]+)(?P<data>.*)/', $content, $matches)) {
            $this->method = $matches['method'];
            if ($matches['data']) {
                $this->parameters = Json::decode($matches['data']);
            }
        }
        return $this;
    }
}
