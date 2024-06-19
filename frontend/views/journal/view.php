<?php

use yii\bootstrap5\Accordion;
use yii\bootstrap5\Nav;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\Journal $model */
/** @var array $groupedData */

$this->title = StringHelper::truncate($model->name, 50);
$this->params['breadcrumbs'][] = ['label' => 'Журналы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="journal-view">

    <h1><?= Html::encode('Журнал "' . $this->title . '"') ?></h1>

    <?php if(Yii::$app->user->can('journal/update')): ?>
        <p>
            <?= Html::a('Обновить', ['update', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
            <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Вы уверены что хотите удалить этот журнал?',
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
            'name',
            'ISSN',
            [
                'label' => 'Рубрика',
                'format' => 'raw',
                'value' => function($model){
                    return $model->showRubric(true);
                }
            ],
            [
                'attribute' => 'disposition',
                'value' => function($model){
                    return $model->nameDisposition();
                } 
            ],
        ],
    ]) ?>

    <h2>Выпуски журнала:</h2>
    <div class="issues">
        <?php if (Yii::$app->user->can('issue/create')): ?>
            <?= Html::a('Добавить новый выпуск', ['issue/create', 'journal_id' => $model->id], ['class' => 'btn btn-success', 'id' => 'create_object']) ?> 
        <?php endif ?>
        
        <?= Accordion::widget([
            'items' => array_map(function($year, $issues) {
                return [
                    'label' => strval($year),
                    'content' => Nav::widget([
                        'items' => array_map(function($issue) {
                            return [
                                'label' => $issue->issuenumber,
                                'url' => $issue->getUrl(),
                            ];
                        }, $issues),
                        'options' => [
                            'class' => 'nav-pills'
                        ],
                    ]),
                    'contentOptions' => [
                        'class' => 'in'
                    ]
                ];
            }, array_keys($groupedData), $groupedData),
        ]);
        ?>
    </div>
</div>