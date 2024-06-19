<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * @property string $edition // принадлежность к изданию
 */
class AdvancedBookSearch extends Book
{
    public $rubric_index;
    public $author_surname;
    public $author_name;
    public $author_middlename;
    public $year_start;
    public $year_end;
    public $edition = "среди книг";

    /**
     * {@inheritDoc}
     */
    public function rules() {
        return [
            [['year_start', 'year_end', 'rubric_index'], 'integer'],
            [[
                'author_surname',
                'author_name',
                'author_middlename',
                'name',
                'tom',
                'annotation',
                'key_words',
                'publishplace',
                'publishhouse'
            ], 'safe'],
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
     * Учитывая искомые атрибуты возвращает ActiveDataProvider модели
     *
     * @param array $params
     *
     * @return ActiveDataProvider|null
     */
    public function search($params) {
        $this->load($params);

        if ($this->name || $this->tom || $this->annotation || $this->key_words ||
            $this->publishhouse || $this->publishplace || $this->rubric_index ||
            $this->year_start || $this->year_end || $this->author_surname ||
            $this->author_name || $this->author_middlename)
        {
            $query = Book::find()
                ->andFilterWhere(['LIKE', 'book.name', $this->name])
                ->andFilterWhere(['LIKE', 'book.tom', $this->tom])
                ->andFilterWhere(['LIKE', 'book.annotation', $this->annotation])
                ->andFilterWhere(['LIKE', 'book.publishhouse', $this->publishhouse])
                ->andFilterWhere(['LIKE', 'book.publishplace', $this->publishplace])
                ->andFilterWhere(['LIKE', 'book.key_words', $this->key_words])
                ->addOrderBy(['book.name' => SORT_ASC]);

            if ($this->year_start) {
                $query->andFilterWhere(['>=', 'book.publishyear', $this->year_start]);
            }
            if ($this->year_end) {
                $query->andFilterWhere(['<=', 'book.publishyear', $this->year_end]);
            }
            if ($this->rubric_index) {
                $query->joinWith('rubric')
                    ->andFilterWhere(['=', 'rubric.id', $this->rubric_index]);
            }
            if ($this->author_surname || $this->author_name || $this->author_middlename) {
                $query->joinWith('bookAllAuthors.author', false)
                    ->andFilterWhere(['LIKE', 'author.surname', $this->author_surname])
                    ->andFilterWhere(['LIKE', 'author.name', $this->author_name])
                    ->andFilterWhere(['LIKE', 'author.middlename', $this->author_middlename]);
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
        } else {
            return null;
        }
        return $dataProvider;
    }
}