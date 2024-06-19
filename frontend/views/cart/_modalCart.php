<?php
use common\models\Cart;
use yii\helpers\Html;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var bool $bulk Множественный выбор */
/** @var common\models\Book|Article|Issue|Statrelease $model */

$user_id = Yii::$app->user->identity->id;
$carts = Cart::findAll(['user_id' => $user_id])
?>

<?php Pjax::begin(["id" => "pjax-modal-cart"]); ?>
    <div class="modal" id="selectCartModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Выберите в какую подборку добавить</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if($carts) { ?>
                        <div class="list-group" style="margin-bottom:16px;">
                            <?php foreach($carts as $id_cart => $cart) { ?>
                                <?php if($bulk) { ?>
                                    <?= Html::a(
                                        $id_cart + 1 . '. ' . $cart->name,
                                        false,
                                        [
                                            'class' => 'list-group-item list-group-item-action',
                                            'id' => 'bulkAddCart',
                                            'data-cart' => $cart->id,
                                            'data-user' => $user_id
                                        ]
                                    ); ?>
                                <?php } else { ?>
                                    <?= Html::a(
                                        $id_cart + 1 . '. '. $cart->name,
                                        [
                                            'cart/add',
                                            'cart_id' => $cart->id,
                                            'model_type' => $model->tableName() . '_id',
                                            'model_id' => $model->id,
                                        ],
                                        ['class' => 'list-group-item list-group-item-action']
                                    ) ?>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    <?php } else { ?>
                        <p>Подборок не найдено.</p>
                        <p>Пожалуйста, создайте подборку в личном кабинете.</p>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
<?php Pjax::end(); ?>