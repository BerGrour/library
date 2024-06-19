<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Rubric $model */

$this->title = 'Добавить рубрику';
$this->params['breadcrumbs'][] = ['label' => 'Рубрики', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rubric-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
