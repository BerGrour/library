<?php

namespace common\models;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * This is the model class for table "statreleaserubric".
 *
 * @property int $id
 * @property string|null $title
 *
 * @property Statrelease[] $statreleases
 */
class StatreleaseRubric extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'statreleaserubric';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'string', 'max' => 250],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Наименование рубрики',
        ];
    }

    /**
     * Gets query for [[Statreleases]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStatreleases()
    {
        return $this->hasMany(Statrelease::class, ['rubric_id' => 'id']);
    }

    /**
     * Ссылка на детальную страницу
     * @return string
     */
    public function getUrl() {
        return Url::to(['statrelease-rubric/view', 'id' => $this->id]);
    }

    /**
     * Выводит название рубрики (если библиотекарь еще + краткое название)
     * 
     * @param bool $linked кликабельное название на детальную страницу
     * @return string
     */
    public function getInfoTitle($linked = false)
    {
        $content = $this->title;
        if ($linked) {
            return Html::a(
                $content,
                $this->getUrl(),
                [
                    'class' => 'text-link',
                    'data-pjax' => 0,
                    'title' => $content
                ]
            );
        }
        return $content;
    }
}
