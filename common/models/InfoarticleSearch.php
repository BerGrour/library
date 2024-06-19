<?php

namespace common\models;

use common\models\Infoarticle;
use Yii;
use yii\data\ActiveDataProvider;

class InfoarticleSearch extends Infoarticle
{
    public $authorname;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['authorname', 'name'], 'safe'],
        ];
    }

    /**
     * @param int $inforelease_id индекс инфо выпуска
     * @param int $author_id индекс автора
     */
    public function search($params, $inforelease_id = 0, $author_id = 0) {
        $query = Infoarticle::find()
            ->joinWith('infoarticleAllAuthors.author', false, 'LEFT JOIN')
            ->groupBy('infoarticle.id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize']
            ],
            'sort' => [
                'attributes' => [
                    'id',
                    'name',
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

        if ($inforelease_id != 0) {
            $query->andFilterWhere(['inforelease_id' => $inforelease_id]);
        }
        if ($author_id != 0) {
            $query->andFilterWhere(['infoarticle_author.author_id' => $author_id]);
        }

        $query->andFilterWhere(['like', 'infoarticle.name', $this->name])
            ->andFilterWhere(['like', 'author.surname', $this->authorname]);

        return $dataProvider;
    }
}