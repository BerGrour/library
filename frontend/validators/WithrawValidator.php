<?php
namespace frontend\validators;

use yii\validators\Validator;

class WithrawValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        if ($model->$attribute && $model->isBorrowed()) {
            $this->addError(
                $model,
                $attribute,
                'Издание не может быть списано, так как оно выдано на руки'
            );
        }
    }
}