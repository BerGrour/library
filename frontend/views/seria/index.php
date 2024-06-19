<?php

use common\models\Seria;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\SeriaSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Информационные серии';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="seria-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->user->can('seria/create')): ?>
        <p>
            <?= Html::a('Добавить инфо серию', ['create'], ['class' => 'btn btn-success']) ?>
        </p>
    <?php endif ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'headerOptions' => ['class' => 'grid_column-serial']
            ],
            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->showTitle(false, true);
                },
                'filterInputOptions' => [
                    'class'       => 'form-control',
                    'placeholder' => 'Поиск по названию...'
                ]
            ],
            [
                'attribute' => "releases_count",
                'label' => "Кол-во выпусков",
                'format' => 'raw',
                'value' => function($model) {
                    return $model->getInforeleases()->count();
                }
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{update}{delete}',
                'urlCreator' => function ($action, Seria $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                },
                'visible' => Yii::$app->user->can('seria/delete'),
            ],
        ],
        'summary' => 'Показано <strong>{begin}-{end}</strong> из <strong>{totalCount}</strong> серий',
        'emptyText' => 'Серий не найдено'
    ]); ?>


</div>
