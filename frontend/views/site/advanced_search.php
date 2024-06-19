<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\widgets\ActiveForm $form */

/** @var common\models\Book $books */
/** @var common\models\Issue $issues */
/** @var common\models\Statrelease $statreleases */

/** @var common\models\SeriaSearch $seriaSearch */
/** @var common\models\JournalSearch $journalSearch */
/** @var yii\data\ActiveDataProvider $seria */
/** @var yii\data\ActiveDataProvider $journal */
/** @var array $countTypes */

$this->title = "Расширенный поиск";
?>

<div class="advanced-search">
    <h1 style="text-align: center;">Расширенный поиск изданий</h1>

    <?php 
        $tabs = [
            'book' => $bookSearch,
            'journal' => $journalSearch,
            'statrelease' => $statreleaseSearch,
            'seria' => $seriaSearch,
        ];
        $tabTitles = [
            'book' => 'Книги',
            'journal' => 'Журналы',
            'statrelease' => 'Стат сборники',
            'seria' => 'Инфо выпуски',
        ];
        $activeTab = true;
    ?>
    <ul class="nav nav-tabs nav-advanced-search" id="myTab" role="tablist">
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
                    aria-selected="true"><b><?= $tabTitles[$tabId] ?></b></button>
            </li>
            <?php $activeTab = false ?>
        <?php endforeach ?>
    </ul>
    <div class="tab-content">
        <?php $activeTab = true ?>
        <?php foreach($tabs as $tabId => $tabData) : ?>
            <div class="tab-pane <?= $activeTab ? 'active' : '' ?>" id="<?= $tabId ?>" role="tabpanel" aria-labelledby="<?= $tabId ?>-tab">

                <?php Pjax::begin(); ?>
                    <?= $this->render("../{$tabId}/_search", [
                        'searchModel' => $tabData
                    ]); ?>

                    <div align="center">
                        <?= Html::a(
                            'Основной поиск',
                            ['index'],
                            ['class' => 'text-link', 'data-pjax' => 0]
                        ); ?>
                    </div>

                    <div class="container-content">
                        <?php if($$tabId) { ?>
                            <h2 align="center">Результаты поиска</h2>
                            <h3 align="center"><?= $tabData->edition ?></h3>

                            <?php if(!Yii::$app->user->isGuest) { ?>
                                <p>
                                    <button id="addToCartModal" class="btn btn-primary" data-bulk=1>Добавить в корзину</button>
                                    
                                    <?php if (Yii::$app->user->can('book/create') and $tabId != "seria"): ?>
                                        <button id="giveToLogbookModal" class="btn btn-primary" data-bulk=1>Выдать на руки</button>
                                    <?php endif ?>
                                </p>
                            <?php } ?>

                            <?= GridView::widget([
                                'dataProvider' => $$tabId,
                                'options' => ['id' => 'grid-view--edition', 'class' => "grid-view"],
                                'columns' => [
                                    [
                                        'class' => 'yii\grid\CheckboxColumn',
                                        'visible' => !Yii::$app->user->isGuest
                                    ],
                                    [
                                        'class' => 'yii\grid\SerialColumn',
                                        'headerOptions' => ['class' => 'grid_column-serial']
                                    ],
                                    [
                                        'label' => 'Автор(ы)',
                                        'format' => 'raw',
                                        'value' => function($model) {
                                            if (method_exists($model, "showAuthor")) {
                                                $content = $model->showAuthor(link: true, target: "_blank");
                                                $redactor_rel = $model->tableName() . "RedactorIds";
                                                if (!empty($model->$redactor_rel)) {
                                                    if ($content) {
                                                        $content .= "; ";
                                                    }
                                                    $content .= $model->showAuthor(link: true, target: "_blank", redactors: true);
                                                }
                                                return $content;
                                            }
                                            return null;
                                        }
                                    ],
                                    [
                                        'attribute' => 'name',
                                        'label' => 'Заглавие',
                                        'format' => 'raw',
                                        'value' => function($model) {
                                            return $model->showTitle(false, true, true, "_blank");
                                        }
                                    ],
                                    [
                                        'label' => 'Рубрика',
                                        'format' => 'raw',
                                        'value' => function($model) {
                                            return $model->showRubric();
                                        }
                                    ],
                                    [
                                        'label' => 'Данные',
                                        'format' => 'raw',
                                        'value' => function($model) {
                                            return $model->showInfo(true, true, "_blank");
                                        }
                                    ],
                                ],
                                'summary' => 'Всего показано: <strong>{begin}-{end}</strong> из <strong>{totalCount}</strong> изданий',
                                'emptyText' => 'Изданий не найдено'
                            ]); ?>
                        <?php } else {
                            echo $this->render('_last_arrivals', [
                                'books' => $books,
                                'statreleases' => $statreleases,
                                'issues' => $issues,
                            ]);
                        } ?>
                    </div>
                <?php Pjax::end(); ?>

            </div>
            <?php $activeTab = false ?>
        <?php endforeach ?>
    </div>
</div>

<?php if(!Yii::$app->user->isGuest) { ?>
    <?= $this->render('../cart/_modalCart', ['bulk' => true]); ?>
    <?= $this->render('../logbook/_modalLogbook', ['bulk' => true]); ?>
<?php } ?>