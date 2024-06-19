<?php
use common\models\StatreleaseRubric;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\data\ActiveDataProvider $dataProvider StatreleaseRubric */
/** @var common\models\StatreleaseRubricSearch $filterModel */
?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $filterModel,
    'columns' => [
        [
            'class' => 'yii\grid\SerialColumn',
            'headerOptions' => ['class' => 'grid_column-serial']
        ],
        [
            'attribute' => 'title',
            'format' => 'raw',
            'value' => function ($model) {
                return $model->getInfoTitle(true);
            },
            'filterInputOptions' => [
                'class'       => 'form-control',
                'placeholder' => 'Поиск'
            ]
        ],
        [
            'attribute' => 'statrelease_count',
            'label' => "Стат сборников",
            'encodeLabel' => false,
            'format' => 'raw',
            'value' => function($model) {
                return $model->getStatreleases()->count();
            },
            'headerOptions' => ['style' => 'width:14%']
        ],
        [
            'class' => ActionColumn::class,
            'template' => '{update}{delete}',
            'urlCreator' => function ($action, StatreleaseRubric $model, $key, $index, $column) {
                return Url::toRoute(["statrelease-rubric/" . $action, 'id' => $model->id]);
             },
            'visible' => Yii::$app->user->can('statreleaserubric/delete'),
        ],
    ],
    'summary' => 'Показано <strong>{begin}-{end}</strong> из <strong>{totalCount}</strong> рубрик',
    'emptyText' => 'Рубрик не найдено'
]); ?>
