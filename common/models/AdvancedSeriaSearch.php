<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use Yii;
use yii\db\ActiveRecord;

/**
 * @property string $edition // принадлежность к изданию
 */
class AdvancedSeriaSearch extends ActiveRecord
{
    public $article_title;
    public $author_surname;
    public $author_name;
    public $author_middlename;
    public $year_start;
    public $year_end;
    public $rubric_index;
    public $edition = "среди инфо выпусков";

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['year_start', 'year_end', 'rubric_index'], 'integer'],
            [['article_title', 'author_surname', 'author_name', 'author_middlename'], 'safe'],
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
     * Учитывая искомые атрибуты возвращает ActiveDataProvider определенной модели (Infoarticle || Infrelease)
     *
     * @param array $params
     *
     * @return ActiveDataProvider|null
     */
    public function search($params)
    {
        $this->load($params);

        if (
            $this->article_title ||
            $this->author_name ||
            $this->author_surname ||
            $this->author_middlename
        ) {
            $query = Infoarticle::find()
                ->joinWith('infoarticleAllAuthors.author', false, 'LEFT JOIN')
                ->andFilterWhere(['LIKE', 'infoarticle.name', $this->article_title])
                ->andFilterWhere(['LIKE', 'author.surname', $this->author_surname])
                ->andFilterWhere(['LIKE', 'author.name', $this->author_name])
                ->andFilterWhere(['LIKE', 'author.middlename', $this->author_middlename])
                ->addOrderBy(['infoarticle.name' => SORT_ASC]);

            if ($this->year_start || $this->year_end || $this->rubric_index) {
                $query->joinWith('inforelease');
                if ($this->year_start) {
                    $query->andFilterWhere(['>=', 'inforelease.publishyear', $this->year_start]);
                }
                if ($this->year_end) {
                    $query->andFilterWhere(['<=', 'inforelease.publishyear', $this->year_end]);
                }
                if ($this->rubric_index) {
                    $query->joinWith('inforelease')->joinWith('inforelease.rubric')
                        ->andFilterWhere(['=', 'rubric.id', $this->rubric_index]);
                }
            }

        } elseif ($this->year_start || $this->year_end || $this->rubric_index) {
            $query = Inforelease::find()->addOrderBy([
                'publishyear' => SORT_DESC,
                new \yii\db\Expression("CONVERT(SUBSTRING_INDEX(number, '-', 1), SIGNED)"),
                new \yii\db\Expression("CASE WHEN LOCATE('-', number) > 0 THEN CONVERT(SUBSTRING_INDEX(number, '-', -1), SIGNED) END"),
                'number' => SORT_ASC,
            ]);

            if ($this->year_start) {
                $query->andFilterWhere(['>=', 'inforelease.publishyear', $this->year_start]);
            }
            if ($this->year_end) {
                $query->andFilterWhere(['<=', 'inforelease.publishyear', $this->year_end]);
            }
            if ($this->rubric_index) {
                $query->joinWith('rubric')
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
