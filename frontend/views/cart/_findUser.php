<?php
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;
use common\models\User;
use common\models\UserSearch;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\widgets\ActiveForm $form */
/** @var common\models\UserSearch $searchModel */

$searchModel = new UserSearch();
?>

<div class="find-user">
    <strong>Поиск читателя</strong>
    <?php Pjax::begin(["id" => "pjax-user-info"]); ?>
    
        <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($searchModel, 'id')->widget(Select2::class, [
                'data' => ArrayHelper::map(User::find()->asArray()->all(), 'id','fio'),
                'options' => [
                    'placeholder' => 'Введите фио ...',
                    'id' => 'find-user-select'
                ],
                'language' => 'ru',
                'pluginOptions' => [
                    'allowClear' => false,
                    'dropdownParent' => '#offcanvas-body-person',
                    'ajax' => [
                        'url' => Url::to(['user/list']),
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
                'pluginEvents' => [
                    "select2:select" => "function() { 
                        var selectedUser = $(this).val();
                        getSelectedUserInfo(selectedUser);
                    }",
                ],
            ])->label(false); ?>

        <?php ActiveForm::end(); ?>

        <div id="selected-user-info"></div>
    <?php Pjax::end(); ?>

</div>

