<?php

namespace nuffic\docblock;

use Exception;
use nuffic\docblock\tag\InputTag;
use nuffic\docblock\tag\ValidatorTag;
use phpDocumentor\Reflection\DocBlockFactory;
use Yii;
use yii\base\DynamicModel;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\validators\Validator;

/**
 * Class ReflectionBuilder
 * @package nuffic\docblock
 */
class ReflectionBuilder extends \ReflectionClass
{
    /**
     * @var object
     */
    private $_instance;

    /**
     * @var Model
     */
    private $_model;

    /**
     * @var DocBlockFactory
     */
    private $_factory;

    /**
     * @inheritdoc
     */
    public function __construct($argument)
    {
        $this->_factory = DocBlockFactory::createInstance([
            'input' => InputTag::class,
            'validator' => ValidatorTag::class,
        ]);

        $this->_instance = $argument;
        if (is_string($this->_instance)) {
            $this->_instance = new $argument;
        }

        if ($this->_instance instanceof Model) {
            $this->_model = $this->_instance;
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
        $reflections = array_filter($this->getPublicInputs(), function ($reflection) {
            if (!$this->_instance instanceof Model && $reflection->getDeclaringClass()->getName() !== $this->getName()) {
                return false;
            }

            try {
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
                $phpdoc = $this->_factory->create($reflection->getDocComment());
                return $phpdoc->hasTag('input');
            } catch (Exception $ex) {
                return false;
            }
        });

        return ArrayHelper::index($reflections, function ($reflection) {
            if ($reflection instanceof \ReflectionMethod) {
                return Inflector::variablize(substr($reflection->name, 3));
            }
            return $reflection->name;
        });
    }

    /**
     * @return array
     */
    private function getPublicInputs()
    {
        return ArrayHelper::merge(
            $this->getProperties(\ReflectionProperty::IS_PUBLIC),
            $this->getMethods(\ReflectionMethod::IS_PUBLIC)
        );
    }

    /**
     * Gets the input tags.
     *
     * @return InputTag[] The input tags.
     */
    public function getInputTags()
    {
        return array_map(function ($reflection) {
            $phpdoc = $this->_factory->create($reflection->getDocComment());
            $tag = $phpdoc->getTagsByName('input')[0];
            if ($tag instanceof InputTag) {
                $tag->setSummary($phpdoc->getSummary());

                if ($reflection instanceof \ReflectionProperty) {
                    $defaultProperties = $reflection->getDeclaringClass()->getDefaultProperties();
                    $tag->setDefaultValue($defaultProperties[$reflection->getName()]);
                }                
            }
            return $tag;
        }, $this->getInputReflections());
    }

    /**
     * @return array ["attribute" => ValidatorTag[]]
     */
    public function getValidatorTags()
    {
        return array_map(function ($reflection) {
            $phpdoc = $this->_factory->create($reflection->getDocComment());
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
                $validator = Yii::createObject($tag->getValidatorConfig());
                $validator->attributes = [$attribute];
                $this->_model->getValidators()->append($validator);
            }
        }

        $this->_model->load(array_map(function ($value) {
            return ArrayHelper::getValue($this->_instance, $value);
        }, array_combine(array_values($properties), $properties)), '');

        return $this->_model;
    }
}
