<?php

namespace common\models;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * This is the model class for table "rubric".
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $shottitle
 *
 * @property Book[] $books
 * @property Inforelease[] $inforeleases
 * @property Journal[] $journals
 */
class Rubric extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rubric';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'shottitle'], 'string', 'max' => 250],
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
            'shottitle' => 'Краткое наименование',
        ];
    }

    /**
     * Gets query for [[Books]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBooks()
    {
        return $this->hasMany(Book::class, ['rubric_id' => 'id']);
    }

    /**
     * Gets query for [[Inforeleases]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInforeleases()
    {
        return $this->hasMany(Inforelease::class, ['rubric_id' => 'id']);
    }

    /**
     * Gets query for [[Journals]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getJournals()
    {
        return $this->hasMany(Journal::class, ['rubric_id' => 'id']);
    }

    /**
     * Ссылка на детальную страницу
     * @return string
     */
    public function getUrl() {
        return Url::to(['rubric/view', 'id' => $this->id]);
    }

    /**
     * Выводит название рубрики (если библиотекарь еще + краткое название)
     * 
     * @param bool $linked кликабельное название на детальную страницу
     * @return string
     */
    public function getInfoTitle($linked = false)
    {
        $rubric_name = $this->title;

        if (Yii::$app->user->can('rubric/update')) {
            $rubric_name .= '<i class="another-info"> (' . $this->shottitle . ')</i>';
        }
        if ($linked) {
            return Html::a(
                $rubric_name,
                $this->getUrl(),
                [
                    'class' => 'text-link',
                    'data-pjax' => 0,
                    'title' => $this->title
                ]
            );
        }
        return $rubric_name;
    }
}
