<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;

/**
 * @property string $edition // принадлежность к изданию
 */
class AdvancedJournalSearch extends ActiveRecord
{
    public $journal_title;
    public $article_title;
    public $author_surname;
    public $author_name;
    public $author_middlename;
    public $year_start;
    public $year_end;
    public $rubric_index;
    public $edition = "среди журналов";
    
    /**
     * {@inheritDoc}
     */
    public function rules() {
        return [
            [['year_start', 'year_end', 'rubric_index'], 'integer'],
            [['journal_title', 'article_title', 'author_surname', 'author_name', 'author_middlename'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Учитывая искомые атрибуты возвращает ActiveDataProvider определенной модели (Article || Issue)
     *
     * @param array $params
     *
     * @return ActiveDataProvider|null
     */
    public function search($params) {
        $this->load($params);

        if (
            $this->article_title ||
            $this->author_name ||
            $this->author_surname ||
            $this->author_middlename
        ) {
            $query = Article::find()
                ->andFilterWhere(['LIKE', 'article.name', $this->article_title])
                ->addOrderBy(['article.name' => SORT_ASC]);

            if ($this->author_surname || $this->author_name || $this->author_middlename) {
                $query->joinWith('articleAllAuthors.author', false)
                    ->andFilterWhere(['LIKE', 'author.surname', $this->author_surname])
                    ->andFilterWhere(['LIKE', 'author.name', $this->author_name])
                    ->andFilterWhere(['LIKE', 'author.middlename', $this->author_middlename]);
            }

            if ($this->year_start || $this->year_end) {
                $query->joinWith('issue');
                if ($this->year_start) {
                    $query->andFilterWhere(['>=', 'issue.issueyear', $this->year_start]);
                }
                if ($this->year_end) {
                    $query->andFilterWhere(['<=', 'issue.issueyear', $this->year_end]);
                }
                if ($this->journal_title || $this->rubric_index) {
                    $query->joinWith('issue.journal')
                        ->andFilterWhere(['LIKE', 'journal.name', $this->journal_title]);
                    if ($this->rubric_index) {
                        $query->joinWith('issue.journal.rubric')
                            ->andFilterWhere(['=', 'rubric.id', $this->rubric_index]);
                    }
                }
            } elseif ($this->journal_title || $this->rubric_index) {
                $query->joinWith('issue')->joinWith('issue.journal')
                    ->andFilterWhere(['LIKE', 'journal.name', $this->journal_title]);
                if ($this->rubric_index) {
                    $query->joinWith('issue.journal.rubric')
                        ->andFilterWhere(['=', 'rubric.id', $this->rubric_index]);
                }
            }
        } elseif (
            $this->year_start ||
            $this->year_end ||
            $this->journal_title ||
            $this->rubric_index
        ) {
            $query = Issue::find()->joinWith('journal')->addOrderBy([
                'journal.name' => SORT_ASC,
                'issueyear' => SORT_DESC,
                new \yii\db\Expression("CONVERT(SUBSTRING_INDEX(issuenumber, '-', 1), SIGNED)"),
                new \yii\db\Expression("CASE WHEN LOCATE('-', issuenumber) > 0 THEN CONVERT(SUBSTRING_INDEX(issuenumber, '-', -1), SIGNED) END"),
                'issuenumber' => SORT_ASC,
            ]);
            if ($this->year_start) {
                $query->andFilterWhere(['>=', 'issue.issueyear', $this->year_start]);
            }
            if ($this->year_end) {
                $query->andFilterWhere(['<=', 'issue.issueyear', $this->year_end]);
            }
            if ($this->journal_title) {
                $query->andFilterWhere(['LIKE', 'journal.name', $this->journal_title]);
            }
            if ($this->rubric_index) {
                $query->joinWith('journal.rubric')
                    ->andFilterWhere(['=', 'rubric.id', $this->rubric_index]);
            }
        } else {
            return null;
        }
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query->groupBy('id'),
            'key' => function ($model) {
                return $model->tableName() . '_id;' . $model->id;
            },
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize']
            ],
        ]);

        return $dataProvider;
    }
}