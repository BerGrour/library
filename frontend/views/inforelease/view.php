<?php

use common\models\Infoarticle;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\Inforelease $model */
/** @var yii\data\ActiveDataProvider $child_provider_model */

$this->title = "Инфо выпуск № " . $model->number;
$this->params['breadcrumbs'][] = ['label' => 'Инфо серии', 'url' => ['/seria', 'index']];
$this->params['breadcrumbs'][] = ['label' => StringHelper::truncate($model->seria->name, 50), 'url' => ['/seria' . '/view', 'id' => $model->seria->id]];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="inforelease-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if(Yii::$app->user->can('inforelease/update')): ?>
        <p>
            <?= Html::a('Обновить', ['update', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
            <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Вы уверены что хотите удалить этот инфо выпуск?',
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    <?php endif ?>

    <?= DetailView::widget([
        'template' => function($attribute, $index, $widget){
            if($attribute['value'])
            {
                return "<tr><th>{$attribute['label']}</th><td>{$attribute['value']}</td></tr>";
            }
        },
        'model' => $model,
        'attributes' => [
            [
                'label' => 'Серия',
                'value' => $model->seria->name
            ],
            'number',
            'numbersk',
            'publishyear',
            [
                'label' => 'Рубрика',
                'format' => 'raw',
                'value' => function($model){
                    return $model->showRubric(true);
                }
            ],
            [
                'label' => 'Файл',
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->file) {
                        return Html::a(
                            '<img src="/Files/Images/doc.png" class="image-file-link">',
                            $model->file->getLinkOnFile(),
                            [
                                'class' => 'custom-link-file',
                                'target' => "_blank",
                                'data-pjax' => 0,
                                'title' => 'Скачать'
                            ]
                        );
                    }
                }
            ]
        ],
    ]) ?>

<h2>Статьи инфо выпуска:</h2>
    <div class="articles">
    <?php if (Yii::$app->user->can('infoarticle/create')): ?>
        <?= Html::a(
            'Добавить новую инфо статью',
            ['infoarticle/create', 'inforelease_id' => $model->id],
            ['class' => 'btn btn-success', 'id' => 'create_object']
        ); ?> 
    <?php endif ?>
        <?= GridView::widget([
            'dataProvider' => $child_provider_model,
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                    'headerOptions' => ['class' => 'grid_column-serial']
                ],
                [
                    'label' => 'Автор',
                    'format' => 'raw',
                    'attribute'=>'authorname',
                    'value' => function($model) {
                        $content = $model->showAuthor(link: true);
                        return $content;
                    },
                ],
                [
                    'attribute' => 'name',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return $model->showTitle(false, true);
                    },
                ],
                [
                    'class' => ActionColumn::class,
                    'template' => '{update}{delete}',
                    'urlCreator' => function ($action, Infoarticle $model, $key, $index, $column) {
                        return Url::toRoute(['infoarticle/' . $action, 'id' => $model->id]);
                    },
                    'visible' => Yii::$app->user->can('infoarticle/delete'),
                ],
            ],
            'summary' => 'Показано <strong>{begin}-{end}</strong> из <strong>{totalCount}</strong> статей',
            'emptyText' => 'Статей не найдено'
        ]);
        ?>
    </div>
</div>
