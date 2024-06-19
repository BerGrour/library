<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var common\models\Cart $model */

$this->title = 'Библиографический список'
?>

<?php if($print) { ?>
    <body onload="print()">
<?php } else { ?>
    <body>
<?php } ?>

    <h1 style="text-align: center;"><?= Html::encode($this->title) ?></h1>

    <div class="editions-categories container">
        <?php if(isset($model->cartEditions)) { ?>
            <?php foreach($model->cartEditions as $index => $item) { ?>
                <div>
                    <?= $index + 1 . ". " ?>
                    <?= $item->getBibliographyInfo($print ? true : false) ?>
                </div>
            <?php } ?>

            <?php if(!$print) { ?>
                <div style="margin-top:10px">
                    <?= Html::a('Распечатать', Url::to(['print-editions', 'id' => $model->id, 'print' => true]), ['class' => 'btn btn-primary']) ?>
                </div>
            <?php } ?>
        <?php } ?>
    </div>
</body>
