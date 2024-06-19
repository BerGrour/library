<?php

namespace common\models;

use common\models\Book;
use Yii;
use yii\data\ActiveDataProvider;

/**
 * @property int $codeDisplay
 * @property mixed $dateDisplay
 */
class InventoryBookSearch extends Book
{
    public $typeBook;
    public $filterType;
    public $selectDate;
    public $codestart;
    public $codeend;
    public $selectDisposition;
    public $dateDisplay;
    public $codeDisplay;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [[
                'typeBook',
                'filterType',
                'selectDate',
                'codestart',
                'codeend',
                'recieptdate',
                'code',
                'selectDisposition',
                'disposition'
            ], 'safe'],
        ];
    }

    public function search($params) {
        $query = Book::find()->groupBy('book.id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize']
            ],
            'sort' => [
                'defaultOrder' => [
                    'code' => SORT_ASC,
                ],
                'attributes' => [
                    'recieptdate',
                    'numbersk',
                    'code' => [
                        'asc' => ['CAST(code AS UNSIGNED)' => SORT_ASC],
                        'desc' => ['CAST(code AS UNSIGNED)' => SORT_DESC],
                    ],
                    'cost',
                ],
            ]
        ]);

        $this->load($params);

        if (!($this->load($params) && $this->validate())) {
            $query->andFilterWhere(['like','book.recieptdate',Yii::$app->formatter->asDate('now', 'yyyy-MM')])
                ->andFilterWhere(['not like', 'book.code', '/в'])
                ->andFilterWhere(['=', 'disposition', 1]);
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        if ($this->typeBook == 2) {
            $query->andFilterWhere(['like', 'book.code', '/в']);
        } else {
            $query->andFilterWhere(['not like', 'book.code', '/в']);
        }

        if ($this->filterType == 'filter1') {
            $this->codeDisplay = 'style="display: none;"';
            $this->dateDisplay = '';
            $query->andFilterWhere(['like', 'book.recieptdate', $this->selectDate]);
        } elseif ($this->filterType == 'filter2') {
            $this->codeDisplay = '';
            $this->dateDisplay = 'style="display: none;"';
            $query->andFilterWhere([
                'BETWEEN',
                'CAST(REPLACE(code, "/", "") AS UNSIGNED)',
                $this->codestart,
                $this->codeend]);
        }

        $query->andFilterWhere(['=', 'disposition', $this->selectDisposition]);

        return $dataProvider;
    }
}