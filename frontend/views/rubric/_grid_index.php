<?php
use common\models\Rubric;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\data\ActiveDataProvider $mainProvider Rubric */
/** @var common\models\RubricSearch $filterModel */
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
            'attribute' => 'book_count',
            'label' => "Книг",
            'encodeLabel' => false,
            'format' => 'raw',
            'value' => function($model) {
                return $model->getBooks()->count();
            },
            'headerOptions' => ['style' => 'width:14%']
        ],
        [
            'attribute' => 'journal_count',
            'label' => "Журналов",
            'encodeLabel' => false,
            'format' => 'raw',
            'value' => function($model) {
                return $model->getJournals()->count();
            },
            'headerOptions' => ['style' => 'width:14%']
        ],
        [
            'attribute' => 'inforelease_count',
            'label' => "Инфо выпусков",
            'encodeLabel' => false,
            'format' => 'raw',
            'value' => function($model) {
                return $model->getInforeleases()->count();
            },
            'headerOptions' => ['style' => 'width:14%']
        ],
        [
            'class' => ActionColumn::class,
            'template' => '{update}{delete}',
            'urlCreator' => function ($action, Rubric $model, $key, $index, $column) {
                return Url::toRoute([$action, 'id' => $model->id]);
             },
            'visible' => Yii::$app->user->can('rubric/delete'),
        ],
    ],
    'summary' => 'Показано <strong>{begin}-{end}</strong> из <strong>{totalCount}</strong> рубрик',
    'emptyText' => 'Рубрик не найдено'
]); ?>
