<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \common\models\NewEmailForm $model */

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Ввод почты';
$this->params['breadcrumbs'][] = ['label' => 'Авторизация', 'url' => ['login']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-new-email">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Пожалуйста, введите свою почту:</p>

    <div class="row">
        <div class="col-lg-5">
        <?php $form = ActiveForm::begin(['id' => 'write-new-email']); ?>

            <?= $form->field($model, 'email')->textInput(['autofocus' => true, 'name' => 'NewEmailForm[email]']) ?>
                
            <div class="form-group">
                <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
