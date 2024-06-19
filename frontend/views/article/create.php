<?php

use yii\helpers\Html;
use yii\helpers\StringHelper;

/** @var yii\web\View $this */
/** @var common\models\Article $model */

$this->title = 'Добавить статью журнала';
$this->params['breadcrumbs'][] = ['label' => 'Журналы', 'url' => ['/journal', 'index']];
$this->params['breadcrumbs'][] = ['label' => StringHelper::truncate($model->issue->journal->name, 50), 'url' => ['/journal' . '/view', 'id' => $model->issue->journal->id]];
$this->params['breadcrumbs'][] = ['label' => $model->issue->issuenumber, 'url' => ['/issue' . '/view', 'id' => $model->issue->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="article-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
