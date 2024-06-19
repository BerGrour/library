<?php

use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var array $countTypes */
?>

<h2 align="center">Результаты поиска</h2>

<?php if(!Yii::$app->user->isGuest) { ?>
    <p>
        <button id="addToCartModal" class="btn btn-primary" data-bulk=1>Добавить в корзину</button>

        <?php if (Yii::$app->user->can('book/create')): ?>
            <button id="giveToLogbookModal" class="btn btn-primary" data-bulk=1>Выдать на руки</button>
        <?php endif ?>
    </p>
<?php } ?>

<div style="display:flex;align-items: center;">
    <div>Найдено: 
        <?php foreach ($countTypes as $key => $countType): ?>
            <?php if((string)$countType != '') { ?>
                <?= " <strong>{$countType}</strong> - {$key};" ?>
            <?php } ?>
        <?php endforeach; ?>
    </div>
</div>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'options' => ['id' => 'grid-view--edition', 'class' => "grid-view"],
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
            'label' => 'Автор(ы)',
            'format' => 'raw',
            'value' => function($model) {
                $class = "common\\models\\" . $model['classModel'];
                $query_model = $class::findOne(['id' => $model['id']]);
                if (method_exists($query_model, "showAuthor")) {
                    $content = $query_model->showAuthor(link: true, target: "_blank");
                    $redactor_rel = $query_model->tableName() . "RedactorIds";
                    if (!empty($query_model->$redactor_rel)) {
                        if ($content) {
                            $content .= "; ";
                        }
                        $content .= $query_model->showAuthor(link: true, target: "_blank", redactors: true);
                    }
                    return $content;
                }
                return null;
            }
        ],
        [
            'attribute' => 'name',
            'label' => 'Заглавие',
            'format' => 'raw',
            'value' => function($model) {
                $class = "common\\models\\" . $model['classModel'];
                $query_model = $class::findOne(['id' => $model['id']]);
                return $query_model->showTitle(false, true, true, "_blank");
            }
        ],
        [
            'label' => 'Рубрика',
            'format' => 'raw',
            'value' => function($model) {
                $class = "common\\models\\" . $model['classModel'];
                $query_model = $class::findOne(['id' => $model['id']]);
                return $query_model->showRubric();
            }
        ],
        [
            'label' => 'Данные',
            'format' => 'raw',
            'value' => function($model) {
                $class = "common\\models\\" . $model['classModel'];
                $query_model = $class::findOne(['id' => $model['id']]);
                return $query_model->showInfo(true, true, "_blank");
            }
        ],
    ],
    'summary' => 'Всего показано: <strong>{begin}-{end}</strong> из <strong>{totalCount}</strong> изданий',
    'emptyText' => 'Изданий не найдено'
]); ?>

<?php if(!Yii::$app->user->isGuest) { ?>
    <?= $this->render('../cart/_modalCart', ['bulk' => true]); ?>
    <?= $this->render('../logbook/_modalLogbook', ['bulk' => true]); ?>
<?php } ?>