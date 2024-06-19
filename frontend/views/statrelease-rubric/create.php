<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\StatreleaseRubric $model */

$this->title = 'Рубрика статистического сборника';
$this->params['breadcrumbs'][] = ['label' => 'Рубрики', 'url' => ['rubric/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="statrelease-rubric-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
