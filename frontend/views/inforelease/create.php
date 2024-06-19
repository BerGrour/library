<?php

use yii\helpers\Html;
use yii\helpers\StringHelper;

/** @var yii\web\View $this */
/** @var common\models\Inforelease $model */

$this->title = 'Добавить информационный выпуск';
$this->params['breadcrumbs'][] = ['label' => 'Инфо серии', 'url' => ['/seria', 'index']];
$this->params['breadcrumbs'][] = ['label' => StringHelper::truncate($model->seria->name, 50), 'url' => ['/seria' . '/view', 'id' => $model->seria->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inforelease-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
