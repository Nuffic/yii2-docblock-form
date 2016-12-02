<?php
/**
 * @var \yii\web\ActiveForm $form
 * @var \nuffic\docblock\tag\InputTag[]  $tags
 */

/**
 * @var \nuffic\docblock\tag\InputTag $tag
 */
foreach ($tags as $field => $tag) {
    $field = $form->field($model, $field);
    echo call_user_func_array([$field, $tag->method], $tag->parameters);
}
