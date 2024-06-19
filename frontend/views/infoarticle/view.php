<?php

use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\Infoarticle $model */

$this->title = StringHelper::truncate($model->name, 50);
$this->params['breadcrumbs'][] = ['label' => 'Инфо серии', 'url' => ['/seria' . '/index']];
$this->params['breadcrumbs'][] = ['label' => StringHelper::truncate($model->inforelease->seria->name, 50), 'url' => ['/seria' . '/view', 'id' => $model->inforelease->seria->id]];
$this->params['breadcrumbs'][] = ['label' => $model->inforelease->number, 'url' => ['/inforelease' . '/view', 'id' => $model->inforelease->id]];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="infoarticle-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if(!Yii::$app->user->isGuest): ?>
            <p>
                <button id="addToCartModal" class="btn btn-primary" data-bulk=0>
                    Добавить в корзину
                </button>
            </p>
        <?php endif ?>

        <?php if(Yii::$app->user->can('article/update')): ?>
            <?= Html::a('Обновить', ['update', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
            <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Вы уверены что хотите удалить эту инфо статью?',
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
                'label' => 'Инфо серия',
                'value' => $model->inforelease->seria->name
            ],
            [
                'label' => 'Номер инфо выпуска',
                'value' => $model->inforelease->number
            ],
            [
                'label' => 'Автор(ы)',
                'format' => 'raw',
                'value' => function($model) {
                    return $model->showAuthor(link: true);
                },
            ],
            'source',
            'recieptdate',
            'additionalinfo',
            'publishyear',
        ],
    ]) ?>
    <?php if(!Yii::$app->user->isGuest) { ?>
        <?= $this->render('../cart/_modalCart', ['bulk' => false, 'model' => $model]); ?>
    <?php } ?>
</div>
