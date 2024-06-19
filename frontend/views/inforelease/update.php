<?php

use yii\helpers\Html;
use yii\helpers\StringHelper;

/** @var yii\web\View $this */
/** @var common\models\Inforelease $model */

$this->title = 'Обновить инфо выпуск № ' . $model->number;
$this->params['breadcrumbs'][] = ['label' => 'Инфо серии', 'url' => ['/seria', 'index']];
$this->params['breadcrumbs'][] = ['label' => StringHelper::truncate($model->seria->name, 50), 'url' => ['/seria' . '/view', 'id' => $model->seria->id]];
$this->params['breadcrumbs'][] = ['label' => $model->number, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Обновить выпуск журнала';
?>
<div class="inforelease-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
