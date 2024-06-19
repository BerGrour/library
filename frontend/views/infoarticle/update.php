<?php

use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Infoarticle $model */

$this->title = 'Обновление инфо статьи';
$this->params['breadcrumbs'][] = ['label' => 'Инфо серии', 'url' => ['/seria', 'index']];
$this->params['breadcrumbs'][] = ['label' => StringHelper::truncate($model->inforelease->seria->name, 50), 'url' => ['/seria' . '/view', 'id' => $model->inforelease->seria->id]];
$this->params['breadcrumbs'][] = ['label' => $model->inforelease->number, 'url' => ['/inforelease' . '/view', 'id' => $model->inforelease->id]];
$this->params['breadcrumbs'][] = ['label' => StringHelper::truncate($model->name, 50), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Обновить';
?>
<div class="infoarticle-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="infoarticle-form">

        <?php $form = ActiveForm::begin(['options' => ['class' => 'view-form', 'id' => 'infoarticle-form-update']]); ?>

        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

        <?php if ($model->type == 1 or $model->type == 2) { ?>
            <div class="form-group field-group-infoarticle-resource">
                <span class="label">Ресурс</span>
                <div class="field-group-infoarticle-double-resource d-flex">
                    <?= $form->field($model, 'source_name')->textInput(['maxlength' => true])->label(false); ?>
                    <span class="label"> № </span>
                    <?= $form->field($model, 'source_number')->textInput(['type' => 'number', 'min' => 1])->label(false); ?>
                </div>
            </div>
        <?php } else if ($model->type == 3) { ?>
            <div class="form-group field-group-infoarticle-source">
                <?= $form->field($model, 'source')->textInput(['maxlength' => true]) ?>
            </div>
        <?php } ?>

        <?php if ($model->type == 1 or $model->type == 3) { ?>
            <div class="form-group field-group-infoarticle-recieptdate">
                <?= $form->field($model, 'recieptdate')->widget(DatePicker::class, [
                    'name' => 'datepicker_infoarticle',
                    'language' => 'ru',
                    'type' => DatePicker::TYPE_INPUT,
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd'
                    ]
                ]); ?>
            </div>
        <?php } else if ($model->type == 2) { ?>
            <div class="form-group field-group-infoarticle-reciept_year">
                <?= $form->field($model, 'reciept_year')->textInput(['type' => 'number', 'max' => date("Y"), 'min' => 0]); ?>
            </div>
        <?php } ?>

        <?= $form->field($model, 'additionalinfo')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'infoarticleAuthorIds')->widget(Select2::class, [
            'initValueText' => $model->getArrayAuthors(),
            'options' => [
                'multiple' => true,
                'placeholder' => ' Выберите авторов...'
            ],
            'language' => 'ru',
            'maintainOrder' => true,
            'pluginOptions' => [
                'allowClear' => true,
                'templateSelection' => new JsExpression("function (data) { 
                    return data.text ? data.text : data.surname;
                }"),
                'ajax' => [
                    'url' => Url::to(['author/list']),
                    'dataType' => 'json',
                    'delay' => 200,
                    'data' => new JsExpression("function(params) { return {term: params.term, page: params.page, limit: 20}; }"),
                    'processResults' => new JsExpression("function(data) { return {results: data.results, pagination: { more: data.more }} }"),
                ],
            ],
        ]); ?>

        <?= Html::a('Новый автор', Url::to(['author/create']), ['class' => 'btn btn-success new-author', 'target' => "_blank"]); ?>

        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
