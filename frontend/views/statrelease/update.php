<?php

use yii\helpers\Html;
use yii\helpers\StringHelper;

/** @var yii\web\View $this */
/** @var common\models\Statrelease $model */

$this->title = 'Обновить : ' . StringHelper::truncate($model->name, 50);
$this->params['breadcrumbs'][] = ['label' => 'Статстический сборник', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => StringHelper::truncate($model->name, 50), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Обновить';
?>
<div class="statrelease-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
