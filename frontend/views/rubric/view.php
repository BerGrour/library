<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\Rubric $model */
/** @var yii\data\ActiveDataProvider $book */
/** @var common\models\BookSearch $bookSearchModel */
/** @var yii\data\ActiveDataProvider $journal */
/** @var common\models\JournalSearch $journalSearchModel */
/** @var yii\data\ActiveDataProvider $inforelease */
/** @var common\models\InforeleaseSearch $inforeleaseSearchModel */

$this->title = StringHelper::truncate($model->title, 50);
$this->params['breadcrumbs'][] = ['label' => 'Рубрики', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$tabs = [
    'book' => [$book, $bookSearchModel], 
    'journal' => [$journal, $journalSearchModel], 
    'inforelease' => [$inforelease, $inforeleaseSearchModel],
];
$tabTitles = [
    'book' => 'Книги',
    'journal' => 'Журналы',
    'inforelease' => 'Информационные выпуски',
];
$activeTab = true;
?>

<div class="rubric-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->user->can('rubric/update')) { ?>
        <p>
            <?= Html::a('Обновить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Вы уверены что хотите удалить рубрику?',
                    'method' => 'post',
                ],
            ]) ?>
        </p>

        <?= DetailView::widget([
            'model' => $model,
            'template' => function($attribute, $index, $widget){
                if($attribute['value'])
                {
                    return "<tr><th>{$attribute['label']}</th><td>{$attribute['value']}</td></tr>";
                }
            },
            'attributes' => [
                'title',
                'shottitle',
            ],
        ]) ?>
    <?php } ?>

    <div class="editions">
        <h2>Издания:</h2>

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
                        aria-selected="true"
                    ><b><?= $tabTitles[$tabId] ?></b></button>
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
                                        'value' => function($model){
                                            return $model->getInfoTitle(true, true);
                                        },
                                        'filterInputOptions' => [
                                            'class'       => 'form-control',
                                            'placeholder' => 'Поиск по названию...'
                                        ]
                                    ]
                                ],
                                'summary' => 'Показано <strong>{begin}-{end}</strong> из <strong>{totalCount}</strong> изданий',
                                'emptyText' => 'Изданий не найдено'
                            ]); ?>
                        
                        <?php \yii\widgets\Pjax::end(); ?>
                    </div>
                <?php $activeTab = false ?>
            <?php endforeach ?>
        </div>
    </div>
</div>
