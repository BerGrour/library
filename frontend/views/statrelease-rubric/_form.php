<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\StatreleaseRubric $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="statrelease-rubric-form">

    <?php $form = ActiveForm::begin(['options' => ['class' => 'view-form']]); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
