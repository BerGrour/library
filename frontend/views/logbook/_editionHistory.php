<?php
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var common\models\LogbookSearch $searchModel */
/** @var Book|Issue|Statrelease $model */
?>

<button class="btn btn-primary" id="btn-history-edition" data-model-id="<?= $model->id ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapseHistory" aria-expanded="false" aria-controls="collapseHistory">
    История выдачи
</button>
<div class="collapse" id="collapseHistory">
    <div class="card card-body" id="card-body-history">
        <?php if($dataProvider->totalCount > 0) { ?>

            <?php Pjax::begin(); ?>

                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    // 'filterModel' => $searchModel
                    'tableOptions' => ['class' => 'table'],
                    'columns' => [
                        [
                            'class' => 'yii\grid\SerialColumn',
                            'headerOptions' => ['class' => 'grid_column-serial']
                        ],
                        [
                            'label' => 'Пользователь',
                            'format' => 'raw',
                            'value' => function($model) {
                                return $model->user->fio;
                            }
                        ],
                        [
                            'attribute' => 'given_date',
                            'format' => ['datetime', 'php:d.m.Y H:i']
                        ],
                        [
                            'attribute' => 'return_date',
                            'format' => 'raw',
                            'value' => function($model) {
                                if ($model->return_date) {
                                    return Yii::$app->formatter->asDatetime(
                                        $model->return_date,
                                        'php:d.m.Y H:i'
                                    );
                                }
                                return '<div style="color:red">На руках<div>';
                            }
                        ]
                    ],
                    'summary' => 'Показано <strong>{begin}-{end}</strong> из <strong>{totalCount}</strong> пользователей',
                ]);?>

            <?php Pjax::end(); ?>

        <?php } else { ?>
            <p>Издание еще не было выдано!</p>
        <?php } ?>
    </div>
</div>

