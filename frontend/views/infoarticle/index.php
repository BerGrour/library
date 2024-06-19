<?php

use common\models\Infoarticle;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Инфо статьи';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="infoarticle-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
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
                }
            ],
            [
                'attribute' => 'Серия',
                'value' => function($model) {
                    return $model->inforelease->seria->name;
                }
            ],
            [
                'attribute' => 'Выпуск',
                'value' => function($model) {
                    return $model->inforelease->number;
                }
            ],
            'recieptdate',
            [
                'class' => ActionColumn::class,
                'template' => '{update}{delete}',
                'urlCreator' => function ($action, Infoarticle $model, $key, $index, $column) {
                    return Url::toRoute(['infoarticle/' . $action, 'id' => $model->id]);
                },
                'visible' => Yii::$app->user->can('infoarticle/delete'),
            ],
        ],
        'summary' => 'Показано <strong>{begin}-{end}</strong> из <strong>{totalCount}</strong> инфо статей',
        'emptyText' => 'Инфо статей не найдено'
    ]); ?>


</div>
