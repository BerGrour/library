<?php 
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Book $books */
/** @var common\models\Issue $issues */
/** @var common\models\Statrelease $statreleases */
?>

<div class="without-search">
    <h2 align="center">Последние поступления</h2>
    <?php
        $tabs = [
            'books' => $books, 
            'issues' => $issues, 
            'statreleases' => $statreleases,
        ];
        $tabTitles = [
            'books' => 'Книги',
            'issues' => 'Выпуски жуналов',
            'statreleases' => 'Статистические сборники',
        ];
        $activeTab = true;
    ?>
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
                    aria-selected="true"><b><?= $tabTitles[$tabId] ?></b></button>
            </li>
            <?php $activeTab = false ?>
        <?php endforeach ?>
    </ul>
    <div class="tab-content">
        <?php $activeTab = true ?>
        <?php foreach($tabs as $tabId => $tabData) : ?>
            <div class="tab-pane <?= $activeTab ? 'active' : '' ?>" id="<?= $tabId ?>" role="tabpanel" aria-labelledby="<?= $tabId ?>-tab">
                <div class="col">
                    <table class="table table-hover">            
                        <?php foreach ($tabData as $key => $data): ?>
                            <tr>
                                <td><?= $key+1 ?></td>
                                <td><?= Html::a(
                                    $data->libraryLink,
                                    $data->url,
                                    ['class' => 'text-link', 'data-pjax' => 0]
                                ); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
            <?php $activeTab = false ?>
        <?php endforeach ?>
    </div>
</div>