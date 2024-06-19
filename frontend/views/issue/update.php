<?php

use yii\helpers\Html;
use yii\helpers\StringHelper;

/** @var yii\web\View $this */
/** @var common\models\Issue $model */

$this->title = 'Обновить выпуск журнала № ' . $model->issuenumber;
$this->params['breadcrumbs'][] = ['label' => 'Журналы', 'url' => ['/journal', 'index']];
$this->params['breadcrumbs'][] = ['label' => StringHelper::truncate($model->journal->name, 50), 'url' => ['/journal' . '/view', 'id' => $model->journal->id]];
$this->params['breadcrumbs'][] = ['label' => $model->issuenumber, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Обновить выпуск журнала';
?>
<div class="issue-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
