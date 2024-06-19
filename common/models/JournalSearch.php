<?php

namespace common\models;

use common\models\Journal;
use Yii;
use yii\data\ActiveDataProvider;

class JournalSearch extends Journal
{
    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['name', 'rubric_id'], 'safe'],
        ];
    }

    /**
     * @param int $rubric_id индекс рубрики
     */
    public function search($params, $rubric_id = 0) {
        $query = Journal::find()
            ->joinWith('rubric')
            ->groupBy('journal.id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize']
            ],
            'sort' => [
                'attributes' => [
                    'id',
                    'name',
                    'rubric_id' => [
                        'asc' => ['rubric.title' => SORT_ASC],
                        'desc' => ['rubric.title' => SORT_DESC],
                    ]
                ],
                'defaultOrder' => [
                    'name' => SORT_ASC,
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        if ($rubric_id != 0) {
            $query->andFilterWhere(['journal.rubric_id' => $rubric_id]);
        }

        $query->andFilterWhere(['like', 'journal.name', $this->name])
            ->andFilterWhere(['=', 'rubric.title', $this->rubric_id]);

        return $dataProvider;
    }
}