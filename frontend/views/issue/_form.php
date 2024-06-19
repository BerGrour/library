<?php

use kartik\date\DatePicker;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Issue $model */
/** @var yii\widgets\ActiveForm $form */

$source = 'Журнал "' . StringHelper::truncate($model->journal->name, 50) . '"'; 
?>

<h3><?= Html::encode($source) ?></h3>

<div class="issue-form">

    <?php $form = ActiveForm::begin(['options' => ['class' => 'view-form']]); ?>

    <?= $form->field($model, 'issuenumber')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'issueyear')->textInput() ?>

    <?= $form->field($model, 'issuedate')->widget(DatePicker::class, [
            'name' => 'datepicker_issue',
            'language' => 'ru',
            'type' => DatePicker::TYPE_INPUT,
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd'
            ]
    ]) ?>

    <?= $form->field($model, 'withraw')->radioList([0 => 'Нет', 1 => 'Да']) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success',]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
