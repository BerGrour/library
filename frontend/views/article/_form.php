<?php

use kartik\file\FileInput;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Article $model */
/** @var yii\widgets\ActiveForm $form */

$source = 'Журнал "' . StringHelper::truncate($model->issue->journal->name, 50) . '", Выпуск № ' . $model->issue->issuenumber; 
?>

<h3><?= Html::encode($source) ?></h3>

<div class="article-form">

    <?php $form = ActiveForm::begin(['options' => ['class' => 'view-form']]); ?>

    <?= $form->field($model, 'name')->textarea(['rows' => 3, 'maxlength' => true]) ?>

    <div class='advanced-search-raw d-flex'>
        <?= $form->field($model, 'pages')->textInput([
            'type' => 'number',
            'min' => 0,
            'maxlength' => true
        ]) ?>

        <?= $form->field($model, 'last_pages')->textInput([
            'type' => 'number',
            'min' => 0,
            'maxlength' => true
        ]) ?>
    </div>

    <?= $form->field($model, 'annotation')->textarea(['rows' => 6, 'maxlength' => true]) ?>

    <?= $form->field($model, 'key_words')->textarea(['rows' => 3, 'maxlength' => true])->label('Ключевые слова <i class="another-info">(через запятые)</i>') ?>

    <?=  $form->field($model, 'articleAuthorIds')->widget(Select2::class, [
        'initValueText' => $model->getArrayAuthors(),
        'options' => [
            'multiple' => true,
            'placeholder' => ' Выберите авторов...'
        ],
        "language" => 'ru',
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
