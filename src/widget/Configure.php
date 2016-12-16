<?php

namespace nuffic\docblock\widget;

use yii\base\Widget;

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
	 * @var \yii\widgets\ActiveForm
	 */
	public $form;

    /**
     * @return string View for the widget
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
