<?php

use common\models\Rubric;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\web\JsExpression;

/** @var yii\web\View $this */
/** @var common\models\Seria $model */
/** @var array $groupedData */

$this->title = StringHelper::truncate($model->name, 50);
$this->params['breadcrumbs'][] = ['label' => 'Инфо серии', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>

<div class="seria-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if(Yii::$app->user->can('seria/update')): ?>
        <p>
            <?= Html::a('Обновить', ['update', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
            <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Вы уверены что хотите удалить эту серию?',
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    <?php endif ?>

    <h2>Выпуски:</h2>
    <div class="issues">
    
        <label class="control-label">Рубрика</label>
        <?= Select2::widget([
            'name' => 'rubric',
            'data' => ArrayHelper::map(Rubric::find()->orderBy('title')->all(), 'id', 'infoTitle'),
            'language' => 'ru',
            'options' => ['placeholder' => 'Фильтр по рубрикам', 'id' => 'rubric-inforeleases-select'],
            'pluginOptions' => [
                'allowClear' => true,
                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                'formatSelection' => new JsExpression('function (data) {
                    return data.replace(/&lt;/g, "<").replace(/&gt;/g, ">");
                }'),
            ],
        ]); ?>
        <br>

        <?php if (Yii::$app->user->can('inforelease/create')): ?>
            <?= Html::a('Добавить новый выпуск', ['inforelease/create', 'seria_id' => $model->id], ['class' => 'btn btn-success', 'id' => 'create_object']) ?> 
        <?php endif ?>
        
        <div id="accordion-inforeleases-list">
            <?= $this->render('_inforeleases_list', [
                'groupedData' => $groupedData,
            ]); ?>
        </div>
    </div>
</div>
