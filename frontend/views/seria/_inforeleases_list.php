<?php 
use yii\bootstrap5\Accordion;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\helpers\Url;

/** @var array $groupedData массив с инфо выпусками сгруппированный по годам */
?>

<?= Accordion::widget([
    'items' => array_map(function($year, $releases) {
        return [
            'label' => strval($year),
            'content' => Nav::widget([
                'items' => array_map(function($release) {
                    if ($release->file) {
                        $label = $release->number;
                        $url = Url::to(['inforelease/view', 'id' => $release->id]);
                        $link = Html::a($label, $url, ['class' => 'nav-link', 'target' => '_self']);

                        $fileLink = Html::a(
                            '<img src="/Files/Images/doc.png" class="image-file-link">',
                            $release->file->getLinkOnFile(),
                            [
                                'class' => 'custom-link-file',
                                'title' => 'Скачать',
                                'target' => '_blank',
                                'data-pjax' => 0,
                            ]
                        );

                        return ['label' => $link . $fileLink];
                    } else {
                        return [
                            'label' => $release->number,
                            'url' => Url::to(['inforelease/view', 'id' => $release->id]),
                            'linkOptions' => ['target' => '_blank']
                        ];
                    }
                }, $releases),
                'options' => ['class' => 'nav-pills'],
                'encodeLabels' => false
            ]),
            'contentOptions' => ['class' => 'in'],
        ];
    }, array_keys($groupedData), $groupedData),
]); ?>