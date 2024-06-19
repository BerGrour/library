<?php

/** @var yii\web\View $this */
/** @var common\models\Book $edition */

$card = $this->render('_card.php', [
    'edition' => $edition,
]);
?>

<body onload="print()">	
    <div style="float:left;" class="cat_card">
        <?= $card ?>
    </div>

    <div style="float:left; padding:0 0 0 2px;" class="cat_card">
        <?= $card ?>
    </div>
</body>