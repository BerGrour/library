<?php

namespace common\models;

use common\models\Logbook;
use Yii;
use yii\data\ActiveDataProvider;

class LogbookSearch extends Logbook
{
    /**
     * @param int $user_id индекс пользователя
     * @param int $book_id индекс книги
     * @param int $issue_id индекс журнального выпуска
     * @param int $statrelease_id индекс стат сборника
     */
    public function search($params, $user_id = 0, $book_id = 0, $issue_id = 0, $statrelease_id = 0) {
        $query = Logbook::find();

        if ($user_id) {
            $query->andFilterWhere(['user_id' => $user_id]);
        }
        if ($book_id) {
            $query->andFilterWhere(['book_id' => $book_id]);
        }
        if ($issue_id) {
            $query->andFilterWhere(['issue_id' => $issue_id]);
        }
        if ($statrelease_id) {
            $query->andFilterWhere(['statrelease_id' => $statrelease_id]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize']
            ],
            'sort' => [
                'attributes' => [
                    'given_date',
                    'return_date' => [
                        'asc' => ['return_date' => new \yii\db\Expression(
                            'CASE WHEN return_date IS NULL THEN 1 ELSE 0 END, return_date ASC'
                        )],
                        'desc' => ['return_date' => new \yii\db\Expression(
                            'CASE WHEN return_date IS NULL THEN 0 ELSE 1 END, return_date DESC'
                        )]
                    ],
                ],
                'defaultOrder' => [
                    'return_date' => SORT_DESC,
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

        return $dataProvider;
    }
}