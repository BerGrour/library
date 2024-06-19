<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Journal $model */

$this->title = 'Добавить журнал';
$this->params['breadcrumbs'][] = ['label' => 'Журналы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="journal-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
