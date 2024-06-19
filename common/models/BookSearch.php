<?php

namespace common\models;

use common\models\Book;
use Yii;
use yii\data\ActiveDataProvider;

class BookSearch extends Book
{
    public $authorname;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['authorname', 'rubric_id', 'name'], 'safe'],
        ];
    }

    /**
     * @param int $author_id индекс автора
     * @param int $rubric_id индекс рубрики
     */
    public function search($params, $author_id = 0, $rubric_id = 0) {
        $query = Book::find()
            ->joinWith('rubric')
            ->joinWith('bookAllAuthors.author', false, 'LEFT JOIN')
            ->groupBy('book.id');

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
                        'asc' => ['rubric.title' => SORT_ASC],
                        'desc' => ['rubric.title' => SORT_DESC],
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
        
        if ($author_id != 0) {
            $query->andFilterWhere(['book_author.author_id' => $author_id]);
        }

        if ($rubric_id != 0) {
            $query->andFilterWhere(['book.rubric_id' => $rubric_id]);
        }

        $query->andFilterWhere(['like', 'book.name', $this->name])
            ->andFilterWhere(['=', 'rubric.title', $this->rubric_id])
            ->andFilterWhere(['like', 'author.surname', $this->authorname]);

        return $dataProvider;
    }
}