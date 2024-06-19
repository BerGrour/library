<?php

use yii\helpers\Html;
use yii\helpers\StringHelper;

/** @var yii\web\View $this */
/** @var common\models\Article $model */

$this->title = 'Обновление статьи журнала';
$this->params['breadcrumbs'][] = ['label' => 'Журналы', 'url' => ['/journal', 'index']];
$this->params['breadcrumbs'][] = ['label' => StringHelper::truncate($model->issue->journal->name, 50), 'url' => ['/journal' . '/view', 'id' => $model->issue->journal->id]];
$this->params['breadcrumbs'][] = ['label' => $model->issue->issuenumber, 'url' => ['/issue' . '/view', 'id' => $model->issue->id]];
$this->params['breadcrumbs'][] = ['label' => StringHelper::truncate($model->name, 50), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Оновить';
?>
<div class="article-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
