<?php

use common\models\Logbook;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\Article $model */

$this->title = StringHelper::truncate($model->name, 50);
$this->params['breadcrumbs'][] = ['label' => 'Журналы', 'url' => ['/journal', 'index']];
$this->params['breadcrumbs'][] = ['label' => StringHelper::truncate($model->issue->journal->name, 50), 'url' => ['/journal' . '/view', 'id' => $model->issue->journal->id]];
$this->params['breadcrumbs'][] = ['label' => $model->issue->issuenumber, 'url' => ['/issue' . '/view', 'id' => $model->issue->id]];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="article-view">

    <h1><?= Html::encode('Статья "' . $this->title . '"') ?></h1>

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

        <?php if(Yii::$app->user->can('article/update')): ?>
            <?= Html::a('Обновить', ['update', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
            <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Вы уверены что хотите удалить эту статью?',
                    'method' => 'post',
                ],
            ]) ?>
        <?php endif ?>
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
            'name:ntext',
            [
                'label' => 'Статус',
                'format' => 'raw',
                'value' => function($model){
                    if ($model->issue->withraw) {
                        return "<span class='status-edition'>Списано</span>";
                    }
                    return Logbook::checkIssueHands($model->id);
                }
            ],
            [
                'label' => 'Страницы',
                'format' => 'raw',
                'value' => function($model){
                    $content = $model->pages;
                    if ($model->last_pages) {
                        $content .= "-{$model->last_pages}";
                    }
                    return $content;
                }
            ],
            'annotation:ntext',
            'key_words',
            [
                'label' => 'Журнал',
                'value' => $model->issue->journal->name
            ],
            [
                'label' => 'Номер выпуска',
                'value' => $model->issue->issuenumber
            ],
            [
                'label' => 'Автор(ы)',
                'format' => 'raw',
                'value' => function($model) {
                    return $model->showAuthor(link: true);
                },
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
    <?php if(!Yii::$app->user->isGuest) { ?>
        <?= $this->render('../cart/_modalCart', ['bulk' => false, 'model' => $model]); ?>
        <?= $this->render('../logbook/_modalLogbook', ['bulk' => false, 'model' => $model->issue]); ?>
    <?php } ?>
</div>
