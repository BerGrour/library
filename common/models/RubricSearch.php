<?php

namespace common\models;

use Yii;
use yii\data\ActiveDataProvider;

class RubricSearch extends Rubric
{
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['title'], 'safe'],
        ];
    }

    public function search($params) {
        $query = Rubric::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize']
            ],
            'sort' => [
                'attributes' => [
                    'title'
                ],
                'defaultOrder' => [
                    'title' => SORT_ASC,
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['id' => $this->id])
            ->andFilterWhere(['like', 'title', $this->title])
            ->OrFilterWhere(['like', 'shottitle', $this->title]);

        return $dataProvider;
    }
}