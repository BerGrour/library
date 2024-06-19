<?php

use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\RoleAssignmentForm $formModel */
/** @var yii\widgets\ActiveForm $form */

$this->title = 'Изменить пользователя';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="assign_role-form">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(['id' => 'update-user-search']) ?>

        <?= $form->field($formModel, 'user_id')->widget(Select2::class, [
            'options' => ['placeholder' => 'Введите фио...', 'id' => 'choose-user-role'],
            'language' => 'ru',
            'pluginOptions' => [
                'allowClear' => false,
                'ajax' => [
                    'url' => Url::to(['user/list']),
                    'dataType' => 'json',
                    'delay' => 200,
                    'data' => new JsExpression("function(params) { return {term: params.term, page: params.page, limit: 20}; }"),
                    'processResults' => new JsExpression("function(data) { return {results: data.results, pagination: { more: data.more }} }"),
                ],
            ],
            'pluginEvents' => [
                "select2:select" => "function() { 
                    var selectedUser = $(this).val();
                    chooseUserForUpdate(selectedUser);
                }",
            ],
        ]); ?>

        <div id="update-user-form"></div>

    <?php ActiveForm::end(); ?>
</div>