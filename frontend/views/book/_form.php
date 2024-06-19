<?php

use common\models\Rubric;
use kartik\date\DatePicker;
use kartik\file\FileInput;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Book $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="book-form">

    <?php $form = ActiveForm::begin(
        ['options' => ['enctype' => 'multipart/form-data', 'class' => 'view-form']]
    ); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'additionalname')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'response')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'additionalresponse')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bookinfo')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'publishplace')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'publishhouse')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'publishyear')->textInput() ?>

    <?= $form->field($model, 'tom')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pages')->textInput([
        'type' => 'number',
        'min' => 0,
        'maxlength' => true
    ]) ?>

    <?= $form->field($model, 'authorsign')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'numbersk')->textInput() ?>

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

    <?= $form->field($model, 'ISBN')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'annotation')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'key_words')->textarea(['rows' => 3, 'maxlength' => true])->label('Ключевые слова <i class="another-info">(через запятые)</i>') ?>

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

    <?=  $form->field($model, 'bookAuthorIds')->widget(Select2::class, [
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
                'data' => new JsExpression("function(params) {
                    return {term: params.term, page: params.page, limit: 20};
                }"),
                'processResults' => new JsExpression("function(data) {
                    return {results: data.results, pagination: { more: data.more }}
                }"),
            ],
        ],
    ]); ?>

    <?=  $form->field($model, 'bookRedactorIds')->widget(Select2::class, [
        'initValueText' => $model->getArrayRedactors(),
        'options' => [
            'multiple' => true,
            'placeholder' => ' Выберите редакторов...'
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

    <?= $form->field($model, 'withraw')->radioList([0 => 'Нет', 1 => 'Да']) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
