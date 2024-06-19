<?php

namespace common\models;

use common\models\Statrelease;
use Yii;
use yii\data\ActiveDataProvider;

class StatreleaseSearch extends Statrelease
{
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['rubric_id', 'name'], 'safe'],
        ];
    }

    /**
     * @param int $rubric_id индекс рубрики
     */
    public function search($params, $rubric_id = 0) {
        $query = Statrelease::find()
            ->joinWith('rubric')
            ->groupBy('statrelease.id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize']
            ],
            'key' => function ($model) {
                return $model->tableName() . '_id;' . $model->id;
            },
            'sort' => [
                'attributes' => [
                    'id',
                    'name',
                    'rubric_id' => [
                        'asc' => ['statreleaserubric.title' => SORT_ASC],
                        'desc' => ['statreleaserubric.title' => SORT_DESC],
                    ]
                ],
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        if ($rubric_id != 0) {
            $query->andFilterWhere(['statrelease.rubric_id' => $rubric_id]);
        }

        $query->andFilterWhere(['like', 'statrelease.name', $this->name])
            ->andFilterWhere(['=', 'statreleaserubric.title', $this->rubric_id]);

        return $dataProvider;
    }
}