## Usage example

### Class definition
```php
class Person
{
    /**
     * @input
     * @validator {"class": "\\yii\\validators\\RequiredValidator"}
     * @validator {"class": "\\yii\\validators\\StringValidator", "min": 3, "max": 30}
     */
    public $name;
    
    /**
     * @input widget["kartik\\switchinput\\SwitchInput", {"template": "default"}]
     * @input {"class": \\yii\\validators\\BooleanValidator", "skipOnEmpty": false}
     */
    public $age;
}
```

### Form rendering

```php

$form = ActiveForm::begin();

echo \nuffic\docblock\widget\Configure::widget([
    'form' => $form,
    'reflection' => new \nuffic\docblockReflectionBuilder(Person::class),
]);

$form->end();
```
