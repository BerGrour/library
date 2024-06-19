<?php
use common\models\Cart;
use common\models\User;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\web\JsExpression;

/** @var yii\web\View $this */

$user = Yii::$app->user->identity;
?>

<div class="user-account">
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel" style="width:600px;">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasRightLabel">Личный кабинет</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Закрыть"></button>
        </div>
        <div class="offcanvas-body" id="offcanvas-body-person">
            <h5>Здравствуйте, <?= StringHelper::truncate( $user->fio , 100) ?></h5>
                <div class="accordion-cart-render">
                    <?= $this->render('../cart/_cartList', [
                        'user' => $user,
                        'carts' => Cart::findAll(['user_id' => $user->id])
                    ]) ?>
                </div>
            <?php if (Yii::$app->user->can('logbook/give')): ?>
                <hr style="border: 3px solid #868686;border-radius:5px;">
                <?= $this->render('../cart/_findUser', ['selectedUser' => null]) ?>
            <?php endif ?>
        </div>
    </div>
</div>

<div class="modal" id="createCartModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Новая подборка</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="text" minlength="1" maxlength="50" id="cartNameInput" class="form-control" placeholder="Название подборки">
            </div>
            <div class="modal-footer">
                <button type="button" id="createNewCartButton" class="btn btn-success">Сохранить</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
            </div>
        </div>
    </div>
</div>


<div class="modal" id="changeOwnerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Выберите на кого переписать издания</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <?= Select2::widget([
                    'name' => '',
                    'data' => ArrayHelper::map(User::find()->asArray()->all(), 'id','fio'),
                    'options' => [
                        'placeholder' => 'Поиск пользователя...',
                        'id' => 'user-change-owner-modal'
                    ],
                    'language' => 'ru',
                    'pluginOptions' => [
                        'allowClear' => false,
                        'dropdownParent' => '#changeOwnerModal',
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
                    ]
                ]); ?>
            </div>
            <div class="modal-footer">
                <button type="button" id="actionChangeOwner" class="btn btn-success">Выдать</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
            </div>
        </div>
    </div>
</div>

<div id="alertdiv-error" class="alert alert-danger alert-custom" style="display: none;">
    <a class="close" data-dismiss="alert"></a>
    <span></span>
</div>

<div id="alertdiv-alert" class="alert alert-warning alert-custom" style="display: none;">
    <a class="close" data-dismiss="alert"></a>
    <span></span>
</div>

<div id="alertdiv-success" class="alert alert-success alert-custom" style="display: none;">
    <a class="close" data-dismiss="alert"></a>
    <span></span>
</div>
