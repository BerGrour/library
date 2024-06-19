<?php

use common\models\Book;
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
/** @var common\models\BookSearch $searchModel */

$this->title = 'Книги';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="book-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->user->can('book/create')): ?>
        <p>
            <?= Html::a('Добавить книгу', ['create'], ['class' => 'btn btn-success']) ?>
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
                'label' => 'Автор(ы)',
                'format' => 'raw',
                'attribute'=>'authorname',
                'value' => function($model) {
                    $content = $model->showAuthor(link: true);
                    if ($model->bookRedactorIds) {
                        if ($content) {
                            $content .= "; ";
                        }
                        $content .= $model->showAuthor(link: true, redactors: true);
                    }
                    return $content;
                },
                'filterInputOptions' => [
                    'class'       => 'form-control',
                    'placeholder' => 'Поиск по автору...'
                ]
            ],
            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function($model){
                    return $model->showTitle(false, true, true);
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
                'attribute' => 'Данные',
                'format' => 'raw',
                'value' => function($model) {
                    return $model->showInfo();
                }
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{update}{delete}',
                'urlCreator' => function ($action, Book $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                },
                'visible' => Yii::$app->user->can('book/delete'),
            ],
        ],
        'summary' => 'Показано <strong>{begin}-{end}</strong> из <strong>{totalCount}</strong> книг',
        'emptyText' => 'Книг не найдено'
    ]); ?>
</div>

<?php if(!Yii::$app->user->isGuest) { ?>
    <?= $this->render('../cart/_modalCart', ['bulk' => true]); ?>
    <?= $this->render('../logbook/_modalLogbook', ['bulk' => true]); ?>
<?php } ?>