<?php

use common\models\Author;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var common\models\AuthorSearch $searchModel */

$this->title = 'Авторы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="author-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->user->can('author/create')): ?>
        <p>
            <?= Html::a(
                'Добавить автора',
                ['create'],
                ['class' => 'btn btn-success']
            ); ?>
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
                'attribute' => 'surname',
                'label' => 'ФИО',
                'format' => 'raw',
                'value' => function($model){
                    return Html::a(
                        $model->showFIO(),
                        $model->getUrl(),
                        [
                            'class' => 'text-link',
                            'data-pjax' => 0,
                            'title' => $model->showFIO()
                        ]
                    );
                },
                'filterInputOptions' => [
                    'class'       => 'form-control',
                    'placeholder' => 'Поиск автора...'
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
                'attribute' => 'article_count',
                'label' => "Журнальных статей",
                'encodeLabel' => false,
                'format' => 'raw',
                'value' => function($model) {
                    return $model->getArticles()->count();
                },
                'headerOptions' => ['style' => 'width:14%']
            ],
            [
                'attribute' => 'infoarticle_count',
                'label' => "Инфо статей",
                'encodeLabel' => false,
                'format' => 'raw',
                'value' => function($model) {
                    return $model->getInfoarticles()->count();
                },
                'headerOptions' => ['style' => 'width:14%']
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{update}{delete}',
                'urlCreator' => function ($action, Author $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                },
                'visible' => Yii::$app->user->can('author/delete'),
            ],
        ],
        'summary' => 'Показано <strong>{begin}-{end}</strong> из <strong>{totalCount}</strong> авторов',
        'emptyText' => 'Авторов не найдено'
    ]); ?>

</div>
