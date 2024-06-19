<?php

use common\models\Rubric;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var yii\widgets\ActiveForm $form */
/** @var common\models\AdvancedBookSearch $searchModel */
?>

<div class="search-title">
    <div class="container-search advanced-search advanced-search-book">
        <?php $form = ActiveForm::begin([
            'action' => ['advanced-search'],
            'method' => 'get',
            'id' => 'advanced-search',
            'options' => [
                'data-pjax' => true,
                'class' => 'advanced-search with-reset'
            ]
        ]); ?>

        <div class="advanced-search-raw d-flex">
            <span class="advanced-search-label">Заглавие</span>

            <?= $form->field($searchModel, 'name')
                ->textInput()->label(false); ?>
        </div>

        <div class="advanced-search-raw d-flex">
            <span class="advanced-search-label">Том</span>

            <?= $form->field($searchModel, 'tom')
                ->textInput()->label(false); ?>
        </div>

        <div class="advanced-search-raw d-flex">
            <span class="advanced-search-label">Рубрика</span>

            <?= $form->field($searchModel, 'rubric_index')->widget(Select2::class, [
                'data' => ArrayHelper::map(Rubric::find()
                    ->orderBy('title')->all(), 'id', 'infoTitle'),
                'language' => 'ru',
                'options' => ['placeholder' => 'Выберите рубрику'],
                'pluginOptions' => [
                    'allowClear' => true,
                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                    'formatSelection' => new JsExpression('function (data) {
                        return data.replace(/&lt;/g, "<").replace(/&gt;/g, ">");
                    }'),
                ],
            ])->label(false); ?>
        </div>
        
        <div class="advanced-search-raw d-flex search-author">
            <span class="advanced-search-label">Автор</span>

            <?= $form->field($searchModel, 'author_surname')
                ->textInput()->label(false); ?><span>&#160</span>

            <div class="short-search-author d-flex">
                <?= $form->field($searchModel, 'author_name')->textInput([
                    'maxlength' => 1
                ])->label(false); ?><span>&#160</span>

                <?= $form->field($searchModel, 'author_middlename')->textInput([
                    'maxlength' => 1
                ])->label(false); ?>
            </div>
        </div>

        <div class="advanced-search-raw d-flex search-years">
            <span class="advanced-search-label">Год издания с</span>

            <?= $form->field($searchModel, 'year_start')->textInput([
                'type' => 'number',
                'min' => 0,
                'max' => date("Y")
            ])->label(false) ?>

            <span class="advanced-search-label">по</span>

            <?= $form->field($searchModel, 'year_end')->textInput([
                'type' => 'number',
                'min' => 0,
                'max' => date("Y")
            ])->label(false) ?>
        </div>

        <div class="advanced-search-raw d-flex">
            <span class="advanced-search-label">Аннотация</span>

            <?= $form->field($searchModel, 'annotation')
                ->textInput()->label(false); ?>
        </div>

        <div class="advanced-search-raw d-flex">
            <span class="advanced-search-label">Издательство</span>

            <?= $form->field($searchModel, 'publishhouse')
                ->textInput()->label(false); ?>
        </div>

        <div class="advanced-search-raw d-flex">
            <span class="advanced-search-label">Место издания</span>

            <?= $form->field($searchModel, 'publishplace')
                ->textInput()->label(false); ?>
        </div>

        <div class="advanced-search-raw d-flex">
            <span class="advanced-search-label">Ключевые слова</span>

            <?= $form->field($searchModel, 'key_words')
                ->textInput()->label(false); ?>
        </div>

        <div class="form-group">
            <?= Html::submitButton('Поиск', ['class' => 'btn btn-primary']); ?>
            <?= Html::Button('Очистить', [
                'class' => 'btn btn-default btn-bordered',
                'id' => 'btn-reset'
            ]); ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>