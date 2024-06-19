<?php

namespace common\models;

use common\models\Inforelease;
use Yii;
use yii\data\ActiveDataProvider;

class InforeleaseSearch extends Inforelease {
    public $name;

    /**
     * @inheritDoc
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
        $query = Inforelease::find()
            ->joinWith('rubric')
            ->joinWith('seria');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize']
            ],
            'sort' => [
                'attributes' => [
                    'id',
                    'name' => [
                        'asc' => [
                            'publishyear' => SORT_ASC,
                            new \yii\db\Expression("CONVERT(SUBSTRING_INDEX(number, '-', 1), SIGNED)"),
                            new \yii\db\Expression("CASE WHEN LOCATE('-', number) > 0 THEN CONVERT(SUBSTRING_INDEX(number, '-', -1), SIGNED) END"),
                            'number' => SORT_DESC,
                        ],
                        'desc' => [
                            'publishyear' => SORT_DESC,
                            new \yii\db\Expression("CONVERT(SUBSTRING_INDEX(number, '-', 1), SIGNED)"),
                            new \yii\db\Expression("CASE WHEN LOCATE('-', number) > 0 THEN CONVERT(SUBSTRING_INDEX(number, '-', -1), SIGNED) END"),
                            'number' => SORT_ASC,
                        ]
                    ]
                ],
                'defaultOrder' => [
                    'name' => SORT_DESC,
                ]
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
            $query->andFilterWhere(['inforelease.rubric_id' => $rubric_id])
            ->andFilterWhere(['like', 'publishyear', $this->name])
            ->orFilterWhere(['like', 'number', $this->name])
            ->orFilterWhere(['like', 'seria.name', $this->name]);
        }

        return $dataProvider;
    }
}