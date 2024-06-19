<?php

use common\models\Rubric;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Journal $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="journal-form">

    <?php $form = ActiveForm::begin(['options' => ['class' => 'view-form']]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ISSN')->textInput(['maxlength' => true]) ?>

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

    <?= $form->field($model, 'disposition')->radioList([1 => 'Волнц', 2 => 'СЗНИИ']) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
