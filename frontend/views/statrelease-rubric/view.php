<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\StringHelper;

/** @var yii\web\View $this */
/** @var common\models\Rubric $model */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var common\models\StatreleaseSearch $searchModel */

$this->title = StringHelper::truncate($model->title, 50);
$this->params['breadcrumbs'][] = ['label' => 'Рубрики', 'url' => ['rubric/index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
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
    <?php } ?>

    <div class="statreleases">
        <h2>Статистические сборники:</h2>

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
    </div>
</div>