<?php
namespace nuffic\docblock;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Context;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use phpDocumentor\Reflection\DocBlock\Tag;

/**
* 
*/
class ReflectionBuilder extends \ReflectionClass
{
    private $_context;

    public function __construct($argument)
    {
        Tag::registerTagHandler('input', '\nuffic\docblock\tag\InputTag');
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

    private function getContext()
    {
        if (!$this->_context) {
            $this->_context = new Context($this->getNamespaceName());
        }
        return $this->_context;
    }
}
