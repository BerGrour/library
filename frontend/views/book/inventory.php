<?php
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var common\models\InventoryBookSearch $searchModel */

$this->title = 'Инвентарная книга';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="inventory-book">

    <?= $this->render('_searchInventory', ['searchModel' => $searchModel]) ?>

    <h2>Результаты:</h2>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'recieptdate',
                'encodeLabel' => false,
                'label' => 'Дата<br>поступления',
                'format' =>  ['date', 'dd.MM.Y']
            ],
            'numbersk',
            [
                'attribute' => 'code',
                'encodeLabel' => false,
                'label' => 'Инвентарный<br>номер'
            ],
            [
                'header' => 'Заглавие, авторы, место издания, издательство, год издания, том (часть, выпуск)',
                'format' => 'raw',
                'value' => function($model) {
                    return $model->inventoryTitle();
                }
            ],
            'cost', 
        ],
        'summary' => 'Показано <strong>{begin}-{end}</strong> из <strong>{totalCount}</strong> книг',
        'emptyText' => 'Книг не найдено'
    ]);
    ?>

</div>

