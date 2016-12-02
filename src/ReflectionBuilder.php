<?php
namespace nuffic\docblock;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Context;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use phpDocumentor\Reflection\DocBlock\Tag;
use yii\base\DynamicModel;

/**
* 
*/
class ReflectionBuilder extends \ReflectionClass
{
    private $_context;

    private $_instance;

    public function __construct($argument)
    {
        Tag::registerTagHandler('input', '\nuffic\docblock\tag\InputTag');
        $this->_instance = $argument;
        if (is_string($this->_instance)) {
            $this->_instance = new $argument;
        }
        parent::__construct($argument);
    }

    /**
     * Get all reflections that have input tags
     *
     * @return  ReflectionProperty[]|ReflectionMethod[]
     */
    private function getInputReflections()
    {
        $reflections = array_filter(ArrayHelper::merge($this->getProperties(\ReflectionProperty::IS_PUBLIC), $this->getMethods(\ReflectionMethod::IS_PUBLIC)), function ($reflection) {
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
     * @return  The input tags.
     */
    public function getInputTags()
    {
        return array_map(function ($reflection) {
            $phpdoc = new DocBlock($reflection, $this->getContext());
            return $phpdoc->getTagsByName('input')[0];
        }, $this->getInputReflections());
    }

    public function getModel()
    {
        $properties = array_keys($this->getInputTags());
        $model = new DynamicModel(array_keys($this->getInputTags()));
        $model->addRule($properties, 'safe');
        $model->load(array_map(function ($value) {
            return ArrayHelper::getValue($this->_instance, $value);
        }, array_combine(array_values($properties), $properties)), '');

        return $model;
    }

    private function getContext()
    {
        if (!$this->_context) {
            $this->_context = new Context($this->getNamespaceName());
        }
        return $this->_context;
    }
}
