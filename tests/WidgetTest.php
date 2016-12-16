<?php

namespace nufficunit\extensions\docblock;

use nuffic\docblock\ReflectionBuilder;
use nuffic\docblock\widget\Configure;
use yii\widgets\ActiveForm;
use yii\web\AssetManager;
use AspectMock\Test as test;
use Yii;

/**
* 
*/
class WidgetTest extends TestCase
{
    protected function tearDown()
    {
        parent::tearDown();
        test::clean(); // remove all registered test doubles
    }

    protected function setUp()
    {
        parent::setUp();
        $this->mockApplication();
    }

    public function testWidget()
    {
        $form = ActiveForm::begin(['action' => '/something', 'enableClientScript' => false]);

        $builder = new ReflectionBuilder(stubs\FooStub::className());

        $widget = new Configure([
            'form' => $form,
            'reflection' => $builder,
        ]);

        $this->assertEqualsWithoutLE(<<<HTML
<div class="form-group field-dynamicmodel-bar">
<label class="control-label" for="dynamicmodel-bar">Bar</label>
<input type="number" id="dynamicmodel-bar" class="form-control" name="DynamicModel[bar]" value="test">

<div class="help-block"></div>
</div><div class="form-group field-dynamicmodel-baz">
<label class="control-label" for="dynamicmodel-baz">Baz</label>
<textarea id="dynamicmodel-baz" class="form-control" name="DynamicModel[baz]">bar</textarea>

<div class="help-block"></div>
</div>
HTML
, $widget->run());
    }
}
