<?php

use common\models\Journal;
use common\models\Rubric;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\web\JsExpression;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var common\models\JournalSearch $searchModel */

$this->title = 'Журналы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="journal-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->user->can('article/create')): ?>
        <p>
            <?= Html::a('Добавить журнал', ['create'], ['class' => 'btn btn-success']) ?>
        </p>
    <?php endif ?>

    <div class="journals">
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
                    'attribute' => 'rubric_id',
                    'format' => 'raw',
                    'value' => function($model){
                        return $model->showRubric();
                    },
                    'filter' => Select2::widget([
                        'model' => $searchModel,
                        'attribute' => 'rubric_id',
                        'language' => 'ru',
                        'data' => ArrayHelper::map(Rubric::find()->all(), 'title','infoTitle'),
                        'value' => 'rubric_id.title',
                        'options' => [
                            'class' => 'form-control',
                            'placeholder' => 'Выберите рубрику'
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                            'formatSelection' => new JsExpression('function (data) {
                                return data.replace(/&lt;/g, "<").replace(/&gt;/g, ">");
                            }'),
                        ]
                    ])
                ],
                [
                    'attribute' => "issues_count",
                    'label' => "Кол-во<br>выпусков",
                    'encodeLabel' => false,
                    'format' => 'raw',
                    'value' => function($model) {
                        return $model->getIssues()->count();
                    }
                ],
                [
                    'class' => ActionColumn::class,
                    'template' => '{update}{delete}',
                    'urlCreator' => function ($action, Journal $model, $key, $index, $column) {
                        return Url::toRoute([$action, 'id' => $model->id]);
                    },
                    'visible' => Yii::$app->user->can('journal/delete')
                ],
            ],
            'summary' => 'Показано <strong>{begin}-{end}</strong> из <strong>{totalCount}</strong> журналов',
            'emptyText' => 'Журналов не найдено'
        ]); ?>
    </div>


</div>
