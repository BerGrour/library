<?php

use common\models\Article;
use common\models\Logbook;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\widgets\DetailView;


/** @var yii\web\View $this */
/** @var common\models\Issue $model */
/** @var yii\data\ActiveDataProvider $childProvider */
/** @var common\models\ArticleSearch $searchChildModel */

$this->title = "Выпуск № " . $model->issuenumber;
$this->params['breadcrumbs'][] = ['label' => 'Журналы', 'url' => ['/journal' . '/index']];
$this->params['breadcrumbs'][] = ['label' => StringHelper::truncate($model->journal->name, 50), 'url' => ['/journal' . '/view', 'id' => $model->journal->id]];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="issue-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if(!Yii::$app->user->isGuest): ?>
            <p>
                <button id="addToCartModal" class="btn btn-primary" data-bulk=0>
                    Добавить в корзину
                </button>
                <?php if(Yii::$app->user->can('book/update')): ?>
                    <button id="giveToLogbookModal" class="btn btn-primary" data-bulk=0>
                        Выдать на руки
                    </button>
                <?php endif ?>
            </p>
        <?php endif ?>

        <?php if(Yii::$app->user->can('issue/update')): ?>
            <?= Html::a('Обновить', ['update', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
            <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Вы уверены что хотите удалить этот выпуск?',
                    'method' => 'post',
                ],
            ]) ?>
        <?php endif ?>
    </p>

    <?= DetailView::widget([
        'template' => function($attribute, $index, $widget){
            if($attribute['value'])
            {
                return "<tr><th>{$attribute['label']}</th><td>{$attribute['value']}</td></tr>";
            }
        },
        'model' => $model,
        'attributes' => [
            'issuenumber',
            [
                'label' => 'Журнал',
                'value' => $model->journal->name
            ],
            'issueyear',
            [
                'label' => 'Статус',
                'format' => 'raw',
                'value' => function($model){
                    if ($model->withraw) {
                        return "<span class='status-edition'>Списано</span>";
                    }
                    return Logbook::checkIssueHands($model->id);
                }
            ],
            'issuedate',
        ],
    ]) ?>

    <?php if (Yii::$app->user->can('logbook/access')) { ?>
        <div class="edition-history-logbook">
            <?= $this->render('../logbook/_editionHistory', [
                'model' => $model,
                'dataProvider' => $logbookProvider,
                'searchModel' => $searchLogbook
            ]) ?>
        </div>
    <?php } ?>

    <h2>Статьи выпуска:</h2>

    <div class="articles">
        <?php if (Yii::$app->user->can('article/create')): ?>
            <p>
                <?= Html::a('Добавить новую статью', ['article/create', 'issue_id' => $model->id], ['class' => 'btn btn-success', 'id' => 'create_object']) ?> 
            </p>
        <?php endif ?>

        <?= GridView::widget([
            'filterModel' => $search_child_model,
            'dataProvider' => $child_provider_model,
            'columns' => [
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
                    'value' => function ($model) {
                        return $model->showTitle(false, true);
                    },
                    'filterInputOptions' => [
                        'class'       => 'form-control',
                        'placeholder' => 'Поиск по названию...'
                    ]
                ],
                [
                    'attribute' => 'pages',
                    'format' => 'raw',
                    'value' => function ($model) {
                        $content = '';
                        if ($model->file) {
                            $content = Html::a(
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
                        return $model->pages . $content;
                    },
                    'filterInputOptions' => [
                        'class'       => 'form-control',
                        'placeholder' => 'Страница № ...'
                    ],
                    'headerOptions' => ['style' => 'width:10%']
                ],
                [
                    'class' => ActionColumn::class,
                    'template' => '{update}{delete}',
                    'urlCreator' => function ($action, Article $model, $key, $index, $column) {
                        return Url::toRoute(['article/' . $action, 'id' => $model->id]);
                    },
                    'visible' => Yii::$app->user->can('article/delete'),
                ],
            ],
            'summary' => 'Показано <strong>{begin}-{end}</strong> из <strong>{totalCount}</strong> статей',
            'emptyText' => 'Статей не найдено'
        ]);
        ?>
    </div>
    
    <?php if(!Yii::$app->user->isGuest) { ?>
        <?= $this->render('../cart/_modalCart', ['bulk' => false, 'model' => $model]); ?>
        <?= $this->render('../logbook/_modalLogbook', ['bulk' => false, 'model' => $model]); ?>
    <?php } ?>
</div>