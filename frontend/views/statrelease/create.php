<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Statrelease $model */

$this->title = 'Добавить статистический сборник';
$this->params['breadcrumbs'][] = ['label' => 'Статистические сборники', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="statrelease-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
