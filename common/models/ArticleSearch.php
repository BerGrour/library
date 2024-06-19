<?php

namespace common\models;

use common\models\Article;
use Yii;
use yii\data\ActiveDataProvider;

class ArticleSearch extends Article
{
    public $authorname;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['authorname', 'name', 'pages'], 'safe'],
        ];
    }

    /**
     * @param int $issue_id индекс выпуска
     * @param int $author_id индекс автора
     */
    public function search($params, $issue_id = 0, $author_id = 0) {
        $query = Article::find()
            ->joinWith('articleAllAuthors.author', false, 'LEFT JOIN')
            ->groupBy('article.id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize']
            ],
            'sort' => [
                'attributes' => [
                    'id',
                    'name',
                    'pages'
                ],
                'defaultOrder' => [
                    'pages' => SORT_ASC,
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

        if ($issue_id != 0) {
            $query->andFilterWhere(['issue_id' => $issue_id]);
        }
        if ($author_id != 0) {
            $query->andFilterWhere(['article_author.author_id' => $author_id]);
        }

        $query->andFilterWhere(['like', 'article.name', $this->name])
            ->andFilterWhere(['=', 'article.pages', $this->pages ? (int) $this->pages : null])
            ->andFilterWhere(['like', 'author.surname', $this->authorname]);

        return $dataProvider;
    }
}