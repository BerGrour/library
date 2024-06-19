<?php

use common\models\Logbook;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\Book $model */

$this->title = StringHelper::truncate($model->name, 50);
$this->params['breadcrumbs'][] = ['label' => 'Книги', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="book-view">

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

        <?php if(Yii::$app->user->can('book/update')): ?>
                <?= Html::a(
                    'Обновить',
                    ['update', 'id' => $model->id],
                    ['class' => 'btn btn-success']
                ); ?>
                <?= Html::a(
                    'Удалить',
                    ['delete', 'id' => $model->id],
                    [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => 'Вы уверены что хотите удалить эту книгу?',
                            'method' => 'post',
                        ],
                    ]
                ); ?>
                <?= Html::a(
                    'Каталожная карточка',
                    ['edition-card', 'id' => $model->id],
                    [
                        'class' => 'btn btn-bordered',
                        'onclick' => 'OpenWindow(this.href, "Каталожная карточка", 1040, 285); return false;'
                    ]
                ); ?>
        <?php endif ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'template' => function($attribute, $index, $widget){
            if($attribute['value']) {
                return "<tr><th>{$attribute['label']}</th><td>{$attribute['value']}</td></tr>";
            }
        },
        'attributes' => [
            'name',
            [
                'label' => 'Статус',
                'format' => 'raw',
                'value' => function($model){
                    if ($model->withraw) {
                        return "<span class='status-edition'>Списано</span>";
                    }
                    return Logbook::checkBookHands($model->id);
                }
            ],
            'additionalname',
            [
                'label' => 'Рубрика',
                'format' => 'raw',
                'value' => function($model){
                    return $model->showRubric(true);
                }
            ],
            'response',
            [
                'label' => 'Автор(ы)',
                'format' => 'raw',
                'value' => function($model) {
                    return $model->showAuthor(link: true);
                },
            ],
            [
                'label' => 'Редактор(ы)',
                'format' => 'raw',
                'value' => function($model) {
                    return $model->showAuthor(link: true, redactors: true);
                },
            ],
            'additionalresponse',
            'bookinfo',
            'publishplace',
            'publishhouse',
            'publishyear',
            'tom',
            'pages',
            'annotation:ntext',
            'authorsign',
            'code',
            // 'numbersk',
            'recieptdate',
            [
                'label' => 'Стоимость',
                'value' => function($model){
                    if (isset($model->cost)) {
                        return $model->cost . ' руб.';
                    }
                } 
            ],
            'ISBN',
            [
                'attribute' => 'disposition',
                'value' => function($model){
                    return $model->nameDisposition();
                }
            ],
            'key_words',
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

        
    <?php if (Yii::$app->user->can('logbook/access')) { ?>
        <div class="edition-history-logbook">
            <?= $this->render('../logbook/_editionHistory', [
                'model' => $model,
                'dataProvider' => $logbookProvider,
                'searchModel' => $searchLogbook
            ]) ?>
        </div>
    <?php } ?>

    <?php if(!Yii::$app->user->isGuest) { ?>
        <?= $this->render('../cart/_modalCart', ['bulk' => false, 'model' => $model]); ?>
        <?= $this->render('../logbook/_modalLogbook', ['bulk' => false, 'model' => $model]); ?>
    <?php } ?>
</div>
