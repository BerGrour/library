<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Book $books */
/** @var common\models\Issue $issues */
/** @var common\models\Statrelease $statreleases */
/** @var common\models\CatalogSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var array $countTypes */

$this->title = "Библиотека ВолНЦ РАН";
?>

<div class="search-title">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'id' => 'global-search'
        // 'options' => ['data-pjax' => true]
    ]); ?>
        <h1 style="text-align: center;">Поиск изданий</h1>

        <div class="container-search">
            <div class="search-input d-flex">
                <?= $form->field($searchModel, 'search')->textInput()
                    ->label(false); ?>

                <?= Html::submitButton('Поиск', ['class' => 'btn btn-primary']); ?>
            </div>

            <div class="checkbox-filters">
                <?= $form->field($searchModel, 'bool_book')->checkbox([
                    'label' => 'Книги',
                ]); ?>
                <?= $form->field($searchModel, 'bool_journal')->checkbox([
                    'label' => 'Журналы',
                    'labelOptions' => [
                        'style' => 'padding-left:20px;'
                    ],
                ]); ?>
                <?= $form->field($searchModel, 'bool_statrelease')->checkbox([
                    'label' => 'Стат. сборники',
                    'labelOptions' => [
                        'style' => 'padding-left:20px;'
                    ],
                ]); ?>
                <?= $form->field($searchModel, 'bool_inforelease')->checkbox([
                    'label' => 'Инфо. выпуски',
                    'labelOptions' => [
                        'style' => 'padding-left:20px;'
                    ],
                ]); ?>
            </div>
        </div>
        <div align="center" style="margin-bottom:20px;">
            <?= Html::a(
                'Расширенный поиск',
                ['advanced-search'],
                ['class' => 'text-link', 'data-pjax' => 0]
            ); ?>
        </div>
    <?php ActiveForm::end(); ?>
</div>

<?php if($dataProvider) { ?>
    <div class="with-search">
        <?= $this->render('_search', [
            'dataProvider' => $dataProvider,
            'countTypes' => $countTypes
        ]); ?>
    </div>

<?php } else { ?>
    <?= $this->render('_last_arrivals', [
        'books' => $books,
        'statreleases' => $statreleases,
        'issues' => $issues,
    ]); ?>
<?php } ?>