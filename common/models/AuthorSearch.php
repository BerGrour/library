<?php

namespace common\models;

use common\models\Author;
use Yii;
use yii\data\ActiveDataProvider;

class AuthorSearch extends Author
{
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['surname', 'name', 'middlename'], 'safe'],
        ];
    }

    public function search($params) {
        $query = Author::find()
            ->groupBy('id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize']
            ],
            'sort' => [
                'attributes' => [
                    'id',
                    'surname',
                    'name',
                    'middlename'
                ],
                'defaultOrder' => [
                    'surname' => SORT_ASC,
                    'name' => SORT_ASC,
                    'middlename' => SORT_ASC
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

        $query->andFilterWhere(['like', 'CONCAT([[surname]], " ", [[name]], " ", [[middlename]], "")', $this->surname])
            ->orFilterWhere(['like', 'CONCAT([[surname]], " ", [[name]], ". ", [[middlename]], ".")', $this->surname]);

        return $dataProvider;
    }
}