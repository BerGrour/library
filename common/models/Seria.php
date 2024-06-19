<?php

namespace common\models;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * This is the model class for table "seria".
 *
 * @property int $id
 * @property string $name
 *
 * @property Inforelease[] $inforeleases
 */
class Seria extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'seria';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => 'Название',
        ];
    }

    /**
     * Gets query for [[Inforeleases]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInforeleases()
    {
        return $this->hasMany(Inforelease::class, ['seria_id' => 'id']);
    }

    /**
     * Ссылка на детальную страницу
     * @return string
     */
    public function getUrl() {
        return Url::to(['seria/view', 'id' => $this->id]);
    }

    /**
     * Возвращает заголовок серии
     * 
     * @param bool $strong жирное начертание для заголовка
     * @param bool $linked кликабельное название на детальную страницу
     * @param bool $with_file нет функционала
     * @param string $target открытие ссылки в новой вкладке
     * @return string
     */
    public function showTitle($strong = false, $linked = false, $with_file = false, $target = "_self") {
        $content = $this->name;
        if ($strong) {
            $content = "<strong>{$content}</strong>";
        }
        if ($linked) {
            return Html::a(
                $content,
                $this->getUrl(),
                [
                    'class' => 'text-link',
                    'data-pjax' => 0,
                    'target' => $target,
                    'title' => $this->name
                ]
            );
        }
        return $content;
    }
}
