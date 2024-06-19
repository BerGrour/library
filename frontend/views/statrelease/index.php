<?php

use common\models\Statrelease;
use common\models\StatreleaseRubric;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\StatreleaseSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Статистические сборники';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="statrelease-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->user->can('statrelease/create')): ?>
        <p>
            <?= Html::a('Добавить сборник', ['create'], ['class' => 'btn btn-success']) ?>
        </p>
    <?php endif ?>

    <?php if(!Yii::$app->user->isGuest) { ?>
        <p>
            <button id="addToCartModal" class="btn btn-primary" data-bulk=1>Добавить в корзину</button>
            
            <?php if (Yii::$app->user->can('book/create')): ?>
                <button id="giveToLogbookModal" class="btn btn-primary" data-bulk=1>Выдать на руки</button>
            <?php endif ?>
        </p>
    <?php } ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'options' => ['id' => 'grid-view--edition', 'class' => "grid-view"],
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
                'visible' => !Yii::$app->user->isGuest
            ],
            [
                'class' => 'yii\grid\SerialColumn',
                'headerOptions' => ['class' => 'grid_column-serial']
            ],
            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function($model){
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
                    return $model-> showRubric();
                },
                'filter' => Select2::widget([
                    'model' => $searchModel,
                    'attribute' => 'rubric_id',
                    'language' => 'ru',
                    'data' => ArrayHelper::map(StatreleaseRubric::find()->asArray()->all(), 'title','title'),
                    'value' => 'rubric_id.title',
                    'options' => [
                        'class' => 'form-control',
                        'placeholder' => 'Выберите рубрику'
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ]
                ])
            ],
            [
                'attribute' => 'Данные',
                'format' => 'raw',
                'value' => function($model) {
                    return $model->showInfo();
                }
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{update}{delete}',
                'urlCreator' => function ($action, Statrelease $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 },
                'visible' => Yii::$app->user->can('statrelease/delete'),
            ],
        ],
        'summary' => 'Показано <strong>{begin}-{end}</strong> из <strong>{totalCount}</strong> стат. сборников',
        'emptyText' => 'Статистических сборников не найдено'
    ]); ?>
</div>

<?php if(!Yii::$app->user->isGuest) { ?>
    <?= $this->render('../cart/_modalCart', ['bulk' => true]); ?>
    <?= $this->render('../logbook/_modalLogbook', ['bulk' => true]); ?>
<?php } ?>