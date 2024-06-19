<?php

use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider Logbook */
/** @var common\models\User $user */

$this->title = 'Формуляр пользователя';
?>

<h1 style="text-align:center;"><?= Html::encode($this->title) ?></h1>
<h2 style="text-align:center;font-weight:normal;"><i><?= Html::encode($user->fio) ?></i></h2>

<div class="actual-logbook-user">
    <?php if($dataProvider->totalCount > 0) { ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'tableOptions' => ['class' => 'table', 'cellpadding' => '6'],
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                    'contentOptions' => ['style' => 'width:5%;border:1px solid black;height:20px;text-align:center;'],
                    'headerOptions' => ['style' => 'width:5%;border:1px solid black;height:20px;text-align:center;background-color:rgb(200);'],
                ],
                [
                    'label' => 'Издание',
                    'format' => 'raw',
                    'value' => function($model) {
                        return $model->getEditionInfo(true, true);
                    },
                    'contentOptions' => ['class' => 'custom-cell-style', 'style' => 'width:80%;border:1px solid black;height:20px;'],
                    'headerOptions' => ['style' => 'width:80%;border:1px solid black;height:20px;text-align:center;background-color:rgb(200);'],
                ],
                [
                    'attribute' => 'given_date',
                    'format' => ['datetime', 'php:d.m.Y H:i'],
                    'contentOptions' => ['class' => 'custom-cell-style', 'style' => 'width:15%;border:1px solid black;height:20px;'],
                    'headerOptions' => ['style' => 'width:15%;border:1px solid black;height:20px;text-align:center;background-color:rgb(200);'],
                    'enableSorting' => false
                ],
            ],
            'summary' => false,
        ]); ?>
    <?php } else { ?>
        У пользователя пустой формуляр.
    <?php } ?>
</div>