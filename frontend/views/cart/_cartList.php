<?php

use common\models\CartEditions;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var common\models\User $user */
/** @var common\models\Cart $carts */

?>
<div class="accordion" id="accordionCart<?= $user->id ?>">

    <?php Pjax::begin(['id' => 'pjax-cart-gridview']); ?>

        <?php if(isset($carts[0])) { ?>
            <?php if ($carts[0]->user_id === Yii::$app->user->identity->id) { ?>
                Ваша корзина:
            <?php } else { ?>
                Корзина пользователя:
            <?php } ?>

            <?php foreach ($carts as $index_cart => $cart) { ?>
                <div class="item-container">
                    <div class="accordion-item" id="accordion-item<?=$cart->id?>">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" id="accordion-button<?=$cart->id?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?=$cart->id?>" aria-expanded="true" aria-controls="collapse<?=$cart->id?>">
                                <?= $index_cart + 1 ?>. <?= $cart->name ?>
                            </button>
                        </h2>
                        <div id="collapse<?=$cart->id?>" class="accordion-collapse collapse" data-bs-parent="#accordionCart<?= $user->id ?>" data-cart-id="<?= $cart->id ?>">
                            <div class="accordion-body">

                                <?php if ($cart->cartEditions) { ?>
                                    <?php $empty_table = true; ?>
                                    <?php if ($cart->user_id === Yii::$app->user->identity->id) { ?>
                                        <?= Html::a(
                                            'Версия для печати',
                                            ['cart/print-editions', 'id' => $cart->id],
                                            [
                                                'class' => 'btn btn-sm btn-default btn-bordered',
                                                'onclick' => 'OpenWindow(this.href, "Каталожная карточка", 680, 400); return false;',
                                                'data-pjax' => 0
                                            ]
                                        ); ?>
                                        <input type="button" class="btn btn-sm btn-danger removeFromCartButton" value="Убрать" data-cart-id="<?= $cart->id ?>">
                                    <?php } ?>

                                    <?php if (Yii::$app->user->can('logbook/give')): ?>
                                        <input type="button" class="btn btn-sm btn-primary giveToLogbook" value="Выдать" data-cart-id="<?= $cart->id ?>">
                                    <?php endif ?>
                                <?php } else { 
                                    $empty_table = false;
                                } ?>

                                <?= GridView::widget([
                                    'showHeader'=> $empty_table,
                                    'options' => [
                                        'id' => 'gridview-cart_' . $cart->id
                                    ],
                                    'dataProvider' => new ActiveDataProvider([
                                        'query' => CartEditions::find()->where(['cart_id' => $cart->id]),
                                        'pagination' => false
                                    ]),
                                    'tableOptions' => ['class' => 'table', 'margin-top' => '8px', 'id' => 'cart-table-' . $cart->id ],
                                    'columns' => [
                                        [
                                            'class' => 'yii\grid\CheckboxColumn',
                                            'visible' => !Yii::$app->user->isGuest
                                        ],
                                        [
                                            'class' => 'yii\grid\SerialColumn',
                                            'headerOptions' => ['class' => 'grid_column-serial']
                                        ],
                                        [
                                            'label' => 'Издание',
                                            'format' => 'raw',
                                            'value' => function($model) {
                                                return $model->getEditionInfo(on_hands: true);
                                            }
                                        ]
                                    ],
                                    'summary' => false,
                                    'emptyText' => 'Подборка пуста.'
                                ]); ?>
                            </div>
                        </div>
                    </div>
                <?php if ($cart->user_id === Yii::$app->user->identity->id) { ?>
                    <button id="deleteCartButton" class="btn btn-delete-trash" data-cart-name="<?= $cart->name ?>" data-cart-id="<?= $cart->id ?>" method="post">
                        <img src="/Files/Images/trash.svg" width="20" height="20">
                    </button>
                <?php } ?>
                </div>
            <?php } ?>
    <?php } else { ?>
        <p style="padding:8px;color:gray;">
            Пусто! Нажмите кнопку ниже, чтобы создать вашу первую подборку.
        </p>
    <?php } ?>

    <?php Pjax::end(); ?>

</div>

<?php if ($user->canCreateCart() && $user->id == Yii::$app->user->identity->id): ?>
    <button class="btn btn-success" id="createCartButton" style="margin:8px 0px;">
        Новая подборка
    </button>
<?php endif ?>