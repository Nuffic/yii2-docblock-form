<?php

namespace nuffic\docblock\widget;

use yii\base\Widget;
use yii\widgets\ActiveForm;

/**
* 
*/
class Configure extends Widget
{
	/**
	 * @var \nuffic\docblock\ReflectionBuilder
	 */
	public $reflection;

	/**
	 * @var \yii\widget\ActiveForm
	 */
	public $form;

    /**
     * @return     <type>  ( description_of_the_return_value )
     */
    public $template;

    public function init()
    {
        if (!$this->template) {
            $this->template = 'form';
        }

        parent::init();
    }

    public function run()
    {
        return $this->render($this->template, [
            'form' => $this->form,
            'tags' => $this->reflection->getInputTags(),
            'model' => $this->reflection->getModel(),
        ]);
    }
}
