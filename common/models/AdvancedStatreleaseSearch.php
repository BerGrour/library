<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * @property string $edition // принадлежность к изданию
 */
class AdvancedStatreleaseSearch extends Statrelease
{
    public $rubric_index;
    public $year_start;
    public $year_end;
    public $edition = "среди статистических сборников";

    /**
     * {@inheritDoc}
     */
    public function rules() {
        return [
            [['year_start', 'year_end', 'rubric_index'], 'integer'],
            [['name', 'key_words'], 'safe']
        ];
    }

    /**
     * {@inheritDoc}
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
     * @return ActiveDataprovider|null
     */
    public function search($params) {
        $this->load($params);

        if ($this->name || $this->key_words || $this->year_start ||
            $this->year_end || $this->rubric_index)
        {
            $query = Statrelease::find()
                ->andFilterWhere(['LIKE', 'statrelease.name', $this->name])
                ->andFilterWhere(['LIKE', 'statrelease.key_words', $this->key_words]);
            
            if ($this->year_start) {
                $query->andFilterWhere(['>=', 'statrelease.publishyear', $this->year_start]);
            }
            if ($this->year_end) {
                $query->andFilterWhere(['<=', 'statrelease.publishyear', $this->year_end]);
            }
            if ($this->rubric_index) {
                $query->joinWith('rubric')
                    ->andFilterWhere(['=', 'statreleaserubric.id', $this->rubric_index]);
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