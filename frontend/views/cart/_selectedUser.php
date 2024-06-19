<?php

use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\User $user */
/** @var common\models\Cart $carts */
/** @var common\models\Logbook $logbooks */

?>

<div class="cart-selected-user">
    <?php if ($carts) { ?>
        <?php if ($carts[0]->user_id != Yii::$app->user->identity->id) { ?>
            <?= $this->render('_cartList', [
                'user' => $user,
                'carts' => $carts
            ]); ?>
        <?php } ?>
    <?php } else { ?>
        У пользователя пустая корзина!
    <?php } ?>
</div>

<div class="logbook">
    <?php if ($logbooks->exists()) { ?>
        <div style="margin-top:16px;">Формуляр пользователя:</div>
        <div class="logbook-block">
            <?php if (Yii::$app->user->can('logbook/return')): ?>
                <?= Html::a(
                    'Версия для печати',
                    ['logbook/print-editions', 'user_id' => $user->id],
                    [
                        'class' => 'btn btn-sm btn-default btn-bordered',
                        'target' => '_blank',
                        'data-pjax' => 0
                    ]
                ); ?>
                <input type="button" class="btn btn-sm btn-success returnEditions" value="Принять">
                <input type="button" class="btn btn-sm btn-primary changeOwner" value="Переписать">
            <?php endif ?>

            <?= GridView::widget([
                'id' => 'gridview-logbook-user_' . $user->id,
                'dataProvider' => new ActiveDataProvider([
                    'query' => $logbooks,
                    'pagination' => false
                ]),
                'tableOptions' => ['class' => 'table', 'margin-top' => '8px', 'id' => 'logbook-table'],
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
                            return $model->getEditionInfo();
                        }
                    ]
                ],
                'summary' => false,
            ]); ?>
        </div>
    <?php } ?>
</div>
