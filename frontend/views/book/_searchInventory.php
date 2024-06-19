<?php

use kartik\date\DatePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var common\models\InventoryBookSearch $searchModel */
/** @var yii\widgets\ActiveForm $form */
?>

<?php $form = ActiveForm::begin([
    'action' => ['inventory'],
    'method' => 'get',
]); ?>

<?= $form->field($searchModel, 'typeBook')->dropDownList(
    [1 => 'Книги', 2 => 'Брошюрные издания']
)->label('Выбрать:'); ?>

<?= $form->field($searchModel, 'filterType')->radioList([
    'filter1' => 'за определенный месяц',
    'filter2' => 'интервал инвентарных номеров'
])->label('Какой фильтр:'); ?>

<div id="filter1" class="filter filter-date" <?=$searchModel->dateDisplay?>>
    <?= $form->field($searchModel, 'selectDate')->widget(DatePicker::class, [
        'name' => 'datepicker_inventory',
        'language' => 'ru',
        'type' => DatePicker::TYPE_INPUT,
        'pluginOptions' => [
            'autoclose' => true,
            'orientation' => 'bottom',
            'format' => 'yyyy-mm',
            'startView' => 'year',
            'minViewMode' => 'months',
        ]
    ]); ?>
</div>

<div id="filter2" class="filter filter-code" <?=$searchModel->codeDisplay?>>
    <?= $form->field($searchModel, 'codestart')->textInput(['type' => 'number']) ?>
    <?= $form->field($searchModel, 'codeend')->textInput(['type' => 'number']) ?>
</div>

<?= $form->field($searchModel, 'selectDisposition')->radioList([
    '1' => 'Волнц',
    '2' => 'СЗНИИ'
]) ?>

<div class="form-group">
    <?= Html::submitButton('Искать', ['class' => 'btn btn-primary']) ?>
    <?= Html::a(
        'Сгенерировать PDF',
        ['generatepdf',Yii::$app->request->queryParams],
        ['class' => 'btn btn-success pdf', 'target' => "_blank", 'data-pjax' => 0]
    ); ?>

</div>


<?php ActiveForm::end(); ?>

