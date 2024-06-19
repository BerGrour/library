<?php

use common\models\Rubric;
use kartik\file\FileInput;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Inforelease $model */
/** @var yii\widgets\ActiveForm $form */

$source = StringHelper::truncate('Серия "' . $model->seria->name . '"', 50); 
?>

<h3><?= Html::encode($source) ?></h3>

<div class="inforelease-form">

    <?php $form = ActiveForm::begin(['options' => ['class' => 'view-form']]); ?>

    <?= $form->field($model, 'number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'numbersk')->textInput() ?>

    <?= $form->field($model, 'publishyear')->textInput() ?>

    <?= $form->field($model, 'rubric_id')->widget(Select2::class, [
        'data' => ArrayHelper::map(Rubric::find()->orderBy('title')->all(), 'id', 'infoTitle'),
        'language' => 'ru',
        'options' => ['placeholder' => 'Выберите рубрику'],
        'pluginOptions' => [
            'allowClear' => true,
            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
            'formatSelection' => new JsExpression('function (data) {
                return data.replace(/&lt;/g, "<").replace(/&gt;/g, ">");
            }'),
        ],
    ]); ?>

    <?= $form->field($model, 'uploadedFile')->widget(FileInput::class, [
        'language' => 'ru',
        'pluginOptions' => [
            'showPreview' => false,
            'showCaption' => true,
            'showRemove' => true,
            'showUpload' => false
        ]
    ])->label("Файл: " . $model->uploadedFile); ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
