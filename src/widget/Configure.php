<?php

namespace nuffic\docblock\widget;

use nuffic\docblock\ReflectionBuilder;
use yii\base\Widget;
use yii\widgets\ActiveForm;

/**
 * Class Configure
 * @package nuffic\docblock\widget
 */
class Configure extends Widget
{
    /**
     * @var ReflectionBuilder
     */
    public $reflection;

    /**
     * @var ActiveForm
     */
    public $form;

    /**
     * @return string View for the widget
     */
    public $template;

    /**
     * @inheritDoc
     */
    public function init()
    {
        if (!$this->template) {
            $this->template = 'form';
        }

        parent::init();
    }

    /**
     * @inheritDoc
     */
    public function run()
    {
        return $this->render($this->template, [
            'form' => $this->form,
            'tags' => $this->reflection->getInputTags(),
            'model' => $this->reflection->getModel(),
        ]);
    }
}
