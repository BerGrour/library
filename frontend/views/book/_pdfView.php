<?php

use yii\grid\GridView;

/** @var yii\data\ActiveDataProvider $dataProvider */
?>

<?= 
GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'attribute' => 'recieptdate',
            'label' => 'Дата записи',
            'contentOptions' => ['style' => 'width:8.5%;border:1px solid black;height:30px;text-align:center;'],
            'headerOptions' => ['style' => 'width:8.5%;border:1px solid black;height:30px;text-align:center;background-color:rgb(200);'],
            'enableSorting' => false,
            'format' =>  ['date', 'dd.MM.Y']
        ],
        [
            'attribute' => 'numbersk',
            'label' => '№СК',
            'contentOptions' => ['style' => 'width:4.5%;border:1px solid black;height:30px;text-align:center;'],
            'headerOptions' => ['style' => 'width:4.5%;border:1px solid black;height:30px;text-align:center;background-color:rgb(200);'],
            'enableSorting' => false
        ],
        [
            'attribute' => 'code',
            'contentOptions' => ['style' => 'width:11%;border:1px solid black;height:30px;text-align:center;'],
            'headerOptions' => ['style' => 'width:11%;border:1px solid black;height:30px;text-align:center;background-color:rgb(200);'],
            'enableSorting' => false
        ],
        [
            'label' => 'Заглавие, авторы, место издания, издательство,<br> год издания, том (часть, выпуск)',
            'format' => 'raw',
            'encodeLabel' => false,
            'value' => function($model){
                return '
                <table style="border-collapse: collapse;width: 100%;height: 100%;">
                    <tr>
                        <td>' 
                            . $model->inventoryTitle() . 
                        '</td>
                    </tr>
                </table>';
            },
            'contentOptions' => ['class' => 'custom-cell-style', 'style' => 'width:44%;border:1px solid black;height:30px;'],
            'headerOptions' => ['style' => 'width:44%;border:1px solid black;height:30px;text-align:center;background-color:rgb(200);'],
        ],
        [
            'attribute' => 'cost',
            'label' => 'Цена р.',
            'contentOptions' => ['style' => 'width:6.5%;border:1px solid black;height:30px;text-align:center;'],
            'headerOptions' => ['style' => 'width:6.5%;border:1px solid black;height:30px;text-align:center;background-color:rgb(200);'],
            'enableSorting' => false
        ],
        [
            'label' => 'Отметка о проверке',
            'contentOptions' => ['style' => 'width:9%;border:1px solid black;height:30px;text-align:center;'],
            'headerOptions' => ['style' => 'width:9%;border:1px solid black;height:30px;text-align:center;background-color:rgb(200);'],
            'value' => function(){
                return '
                <table>
                    <tr>
                        <td style="height:22rem:;width:50%;border-right:1px solid black;border-bottom:1px solid black"></td>
                        <td style="border-bottom:1px solid black;height:22rem;width:50%"></td>
                    </tr>
                    <tr>
                        <td style="height:22rem;border-right:1px solid black;"></td>
                        <td style="height:22rem;"></td>
                    </tr>
                </table>
                ';
            },
            'format' => 'raw',
        ],
        [
            'label' => '№акта выбытия',
            'contentOptions' => ['style' => 'width:8%;border:1px solid black;height:30px;text-align:center;'],
            'headerOptions' => ['style' => 'width:8%;border:1px solid black;height:30px;text-align:center;background-color:rgb(200);'],
        ],
        [
            'label' => 'Примечание',
            'contentOptions' => ['style' => 'width:10%;border:1px solid black;height:30px;text-align:center;'],
            'headerOptions' => ['style' => 'width:10%;border:1px solid black;height:30px;text-align:center;background-color:rgb(200);'],
        ],
    ],
    'summary' => 'Показано <strong>{begin}-{end}</strong> из <strong>{totalCount}</strong> книг',
    'emptyText' => 'Книг не найдено'
]);
?>