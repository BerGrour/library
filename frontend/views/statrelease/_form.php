<?php

use common\models\StatreleaseRubric;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Statrelease $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="statrelease-form">

    <?php $form = ActiveForm::begin(['options' => ['class' => 'view-form']]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'additionalname')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'response')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'publishplace')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'publishyear')->textInput() ?>


    <?= $form->field($model, 'rubric_id')->widget(Select2::class, [
        'data' => ArrayHelper::map(StatreleaseRubric::find()->orderBy('title')->all(), 'id', 'infoTitle'),
        'language' => 'ru',
        'options' => ['placeholder' => 'Выберите рубрику'],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ]); ?>

    <?= $form->field($model, 'disposition')->radioList([1 => 'Волнц', 2 => 'СЗНИИ']) ?>

    <?= $form->field($model, 'pages')->textInput([
        'type' => 'number',
        'min' => 0,
        'maxlength' => true
    ]) ?>

    <?= $form->field($model, 'recieptdate')->widget(DatePicker::class, [
            'name' => 'datepicker_issue',
            'language' => 'ru',
            'type' => DatePicker::TYPE_INPUT,
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd'
            ]
    ]) ?>

    <?= $form->field($model, 'cost')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'authorsign')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'numbersk')->textInput() ?>

    <?= $form->field($model, 'key_words')->textarea(['rows' => 3, 'maxlength' => true])->label('Ключевые слова <i class="another-info">(через запятые)</i>') ?>

    <?= $form->field($model, 'withraw')->radioList([0 => 'Нет', 1 => 'Да']) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
