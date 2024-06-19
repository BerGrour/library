<?php 
use common\models\User;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\JsExpression;

/** @var yii\web\View $this */
/** @var bool $bulk Множественный выбор */
/** @var common\models\Book|Article|Issue|Statrelease $model */
?>

<div class="modal" id="selectLogbookModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Выберите пользователя</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?= Select2::widget([
                    'name' => '',
                    'data' => ArrayHelper::map(User::find()->asArray()->all(), 'id','fio'),
                    'options' => [
                        'placeholder' => 'Поиск пользователя...',
                        'id' => 'user-select-logbook-modal'
                    ],
                    'language' => 'ru',
                    'pluginOptions' => [
                        'allowClear' => false,
                        'dropdownParent' => '#selectLogbookModal',
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
                <?php if($bulk) { ?>
                    <button type="button" id="bulkAddLogbook" class="btn btn-success">Выдать</button>
                <?php } else {?>
                    <button type="button" id="addLogbook" class="btn btn-success" data-type="<?= $model->tableName() . '_id' ?>" data-id="<?= $model->id ?>">Выдать</button>
                <?php } ?>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
            </div>
        </div>
    </div>
</div>