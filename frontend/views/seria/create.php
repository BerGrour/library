<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Seria $model */

$this->title = Yii::t('app', 'Добавить информационную серию');
$this->params['breadcrumbs'][] = ['label' => 'Информационные серии', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="seria-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
