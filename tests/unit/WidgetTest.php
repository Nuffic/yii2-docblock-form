<?php

namespace tests\unit;

use nuffic\docblock\ReflectionBuilder;
use nuffic\docblock\widget\Configure;
use tests\data\Foo;
use Yii;
use yii\i18n\I18N;
use yii\web\Application;
use yii\web\View;
use yii\widgets\ActiveForm;

class WidgetTest extends \Codeception\Test\Unit
{
    public function _before()
    {
        Yii::$app = $this->make(Application::class);
        Yii::$app->set('i18n', $this->make(I18N::class, ['translate' => '']));
    }

    public function _after()
    {
        Yii::$app = null;
    }

    public function testWidget()
    {
        $form = ActiveForm::begin(['action' => '/something', 'enableClientScript' => false]);

        $builder = new ReflectionBuilder(Foo::class);

        $widget = new Configure([
            'form' => $form,
            'reflection' => $builder,
            'view' => $this->make(View::class),
        ]);

        $this->assertEquals(<<<HTML
<div class="form-group field-dynamicmodel-bar">
<label class="control-label" for="dynamicmodel-bar">Bar</label>
<input type="number" id="dynamicmodel-bar" class="form-control" name="DynamicModel[bar]" value="test">

<div class="help-block"></div>
</div><div class="form-group field-dynamicmodel-baz required">
<label class="control-label" for="dynamicmodel-baz">Baz</label>
<textarea id="dynamicmodel-baz" class="form-control" name="DynamicModel[baz]" aria-required="true">bar</textarea>

<div class="help-block"></div>
</div>
HTML
            , $widget->run());
    }
}
