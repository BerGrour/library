<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\Author $model */
/** @var common\models\BookSearch $bookSearchModel */
/** @var yii\data\ActiveDataProvider $books  */
/** @var common\models\ArticleSearch $articleSearchModel */
/** @var yii\data\ActiveDataProvider $articles  */
/** @var common\models\InfoarticleSearch $infoarticleSearchModel */
/** @var yii\data\ActiveDataProvider $infoarticles  */

$this->title = StringHelper::truncate($model->showFIO(), 50);
$this->params['breadcrumbs'][] = ['label' => 'Авторы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$tabs = [
    'books' => [$books, $bookSearchModel], 
    'articles' => [$articles, $articleSearchModel], 
    'infoarticles' => [$infoarticles, $infoarticleSearchModel],
];
$tabTitles = [
    'books' => 'Книги',
    'articles' => 'Журнальные статьи',
    'infoarticles' => 'Информационные статьи',
];
$activeTab = true;
?>

<div class="author-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if(Yii::$app->user->can('book/update')): ?>
        <p>
            <?= Html::a('Обновить', ['update', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
            <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Вы уверены что хотите удалить этого автора?',
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    <?php endif ?>

    <?= DetailView::widget([
        'model' => $model,
        'template' => function($attribute, $index, $widget){
            if($attribute['value'])
            {
                return "<tr><th>{$attribute['label']}</th><td>{$attribute['value']}</td></tr>";
            }
        },
        'attributes' => [
            'surname',
            'name',
            'middlename',
        ],
    ]) ?>

    <div class="works">
        <h2>Работы:</h2>

        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <?php foreach($tabs as $tabId => $tab) : ?>
                <li class="nav-item" role="presentation">
                    <button 
                        class="nav-link <?= $activeTab ? 'active' : '' ?>"
                        id="<?= $tabId ?>-tab" 
                        data-bs-toggle="tab" 
                        data-bs-target="#<?= $tabId ?>" 
                        type="button"
                        role="tab" 
                        aria-controls="<?= $tabId ?>" 
                        aria-selected="true"><b><?= $tabTitles[$tabId] ?></b></button>
                </li>
                <?php $activeTab = false ?>
            <?php endforeach ?>
        </ul>
        <div class="tab-content">
            <?php $activeTab = true ?>
                <?php foreach($tabs as $tabId => $data) : ?>
                    <div class="tab-pane <?= $activeTab ? 'active' : '' ?>" id="<?= $tabId ?>" role="tabpanel" aria-labelledby="<?= $tabId ?>-tab">
                        <?php \yii\widgets\Pjax::begin(); ?>
                            <?= GridView::widget([
                                'dataProvider' => $data[0],
                                'filterModel' => $data[1],
                                'columns' => [
                                    [
                                        'class' => 'yii\grid\SerialColumn',
                                        'headerOptions' => ['class' => 'grid_column-serial']
                                    ],
                                    [
                                        'attribute' => 'name',
                                        'format' => 'raw',
                                        'value' => function ($model) {
                                            return $model->getInfoTitle(true, true);
                                        },
                                        'filterInputOptions' => [
                                            'class'       => 'form-control',
                                            'placeholder' => 'Поиск по названию...'
                                        ]
                                    ],
                                ],
                                'summary' => 'Показано <strong>{begin}-{end}</strong> из <strong>{totalCount}</strong> работ',
                                'emptyText' => 'Работ не найдено'
                            ]);
                            ?>
                            <?php \yii\widgets\Pjax::end(); ?>
                    </div>
                <?php $activeTab = false ?>
            <?php endforeach ?>
        </div>

    </div>

</div>
