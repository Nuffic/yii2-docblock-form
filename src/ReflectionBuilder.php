<?php

namespace nuffic\docblock;

use nuffic\docblock\tag\InputTag;
use nuffic\docblock\tag\ValidatorTag;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Context;
use phpDocumentor\Reflection\DocBlock\Tag;
use Yii;
use yii\base\DynamicModel;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\validators\Validator;

/**
*
*/
class ReflectionBuilder extends \ReflectionClass
{
    /**
     * @var Context
     */
    private $_context;

    /**
     * @var object
     */
    private $_instance;

    /**
     * @var DynamicModel
     */
    private $_model;

    /**
     * @inheritdoc
     */
    public function __construct($argument)
    {
        Tag::registerTagHandler('input', InputTag::class);
        Tag::registerTagHandler('validator', ValidatorTag::class);
        $this->_instance = $argument;
        if (is_string($this->_instance)) {
            $this->_instance = new $argument;
        }
        parent::__construct($argument);
    }

    /**
     * Get all reflections that have input tags
     *
     * @return  \ReflectionProperty[]|\ReflectionMethod[]
     */
    private function getInputReflections()
    {
        $reflections = array_filter(ArrayHelper::merge($this->getProperties(\ReflectionProperty::IS_PUBLIC), $this->getMethods(\ReflectionMethod::IS_PUBLIC)), function ($reflection) {
            if ($reflection->getDeclaringClass()->getName() !== $this->getName()) {
                return false;
            }
            if ($reflection instanceof \ReflectionMethod) {
                /**
                 * var \ReflectionMethod $reflection
                 */
                if (substr($reflection->name, 0, 3) !== 'set') {
                    return false;
                }
                $getterName = Inflector::variablize('get ' . Inflector::camel2words(substr($reflection->name, 3)));
                if (!$this->getMethod($getterName)) {
                    return false;
                }
            }
            $phpdoc = new DocBlock($reflection, $this->getContext());
            return $phpdoc->hasTag('input');
        });

        return ArrayHelper::index($reflections, function ($reflection) {
            if ($reflection instanceof \ReflectionMethod) {
                return Inflector::variablize(substr($reflection->name, 3));
            }
            return $reflection->name;
        });
    }

    /**
     * Gets the input tags.
     *
     * @return InputTag[] The input tags.
     */
    public function getInputTags()
    {
        return array_map(function ($reflection) {
            $phpdoc = new DocBlock($reflection, $this->getContext());
            return $phpdoc->getTagsByName('input')[0];
        }, $this->getInputReflections());
    }

    /**
     * @return array ["attribute" => ValidatorTag[]]
     */
    public function getValidatorTags()
    {
        return array_map(function ($reflection) {
            $phpdoc = new DocBlock($reflection, $this->getContext());
            return $phpdoc->getTagsByName('validator');
        }, $this->getInputReflections());
    }

    public function getModel()
    {
        if ($this->_model) {
            return $this->_model;
        }

        $properties = array_keys($this->getInputTags());
        $this->_model = new DynamicModel(array_keys($this->getInputTags()));
        $this->_model->addRule($properties, 'default', [
            'value' => null
        ]);

        foreach ($this->getValidatorTags() as $attribute => $tags) {
            foreach ($tags as $tag) {
                /** @var Validator $validator */
                $validator = Yii::createObject($tag->validatorConfig);
                $validator->attributes = [$attribute];
                $this->_model->getValidators()->append($validator);
            }
        }

        $this->_model->load(array_map(function ($value) {
            return ArrayHelper::getValue($this->_instance, $value);
        }, array_combine(array_values($properties), $properties)), '');

        return $this->_model;
    }

    private function getContext()
    {
        if (!$this->_context) {
            $this->_context = new Context($this->getNamespaceName());
        }
        return $this->_context;
    }
}
