<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $mainProvider Rubric */
/** @var common\models\RubricSearch $searchMain */
/** @var yii\data\ActiveDataProvider $secondProvider StatreleaseRubric */
/** @var common\models\StatreleaseRubricSearch $searchSecond */

$this->title = 'Рубрики';
$this->params['breadcrumbs'][] = $this->title;

$tabs = [
    'rubric' => [$mainProvider, $searchMain], 
    'statrelease-rubric' => [$secondProvider, $searchSecond], 
];
$tabTitles = [
    'rubric' => 'Основные рубрики',
    'statrelease-rubric' => 'Рубрики стат. сборников',
];
$activeTab = true;
?>

<div class="rubric-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <?php foreach($tabs as $tabId => $tab) : ?>
            <li class="nav-item" role="presentation">
                <button 
                    class="nav-link <?= $activeTab ? 'active' : '' ?>"
                    id="<?= $tabId ?>-tab" 
                    data-bs-toggle="tab" 
                    data-bs-target="#<?= $tabId ?>" 
                    type="button"
                    role="tab" 
                    aria-controls="<?= $tabId ?>" 
                    aria-selected="true"
                ><b><?= $tabTitles[$tabId] ?></b></button>
            </li>
            <?php $activeTab = false ?>
        <?php endforeach ?>
    </ul>
    
    <div class="tab-content">
        <?php $activeTab = true ?>
            <?php foreach($tabs as $tabId => $data) : ?>
                <div class="tab-pane <?= $activeTab ? 'active' : '' ?>" id="<?= $tabId ?>" role="tabpanel" aria-labelledby="<?= $tabId ?>-tab">
                    <?php \yii\widgets\Pjax::begin(); ?>
                        <?php if (Yii::$app->user->can('rubric/create')): ?>
                            <p>
                                <?= Html::a('Добавить рубрику', ["{$tabId}/create"], ['class' => 'btn btn-success', 'style' => 'margin-top:16px']) ?>
                            </p>
                        <?php endif ?>

                        <?= $this->render("../{$tabId}/_grid_index", [
                            'dataProvider' => $data[0],
                            'filterModel' => $data[1],
                        ]) ?>

                    <?php \yii\widgets\Pjax::end(); ?>
                </div>
            <?php $activeTab = false ?>
        <?php endforeach ?>
    </div>
</div>
