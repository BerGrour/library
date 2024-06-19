<?php

use yii\helpers\Html;
use yii\helpers\StringHelper;

/** @var yii\web\View $this */
/** @var common\models\Issue $model */

$this->title = 'Добавить выпуск журнала';
$this->params['breadcrumbs'][] = ['label' => 'Журналы', 'url' => ['/journal', 'index']];
$this->params['breadcrumbs'][] = ['label' => StringHelper::truncate($model->journal->name, 50), 'url' => ['/journal' . '/view', 'id' => $model->journal->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
