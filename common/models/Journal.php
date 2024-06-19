<?php

namespace common\models;

use common\extensions\traits\DispositionTrait;
use Yii;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * This is the model class for table "journal".
 *
 * @property int $id
 * @property string $name
 * @property string $ISSN
 * @property int|null $rubric_id
 * @property int $disposition
 *
 * @property Issue[] $issues
 * @property Rubric $rubric
 */
class Journal extends \yii\db\ActiveRecord
{
    use DispositionTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'journal';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'ISSN', 'rubric_id'], 'required', 'message' => 'Поле не может быть пустым'],
            [['rubric_id', 'disposition'], 'integer', 'message' => 'Должно быть числом'],
            [['name'], 'string', 'max' => 200],
            [['ISSN'], 'string', 'max' => 9],
            [['rubric_id'], 'exist', 'skipOnError' => true, 'targetClass' => Rubric::class, 'targetAttribute' => ['rubric_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Наименование журнала',
            'ISSN' => 'ISSN',
            'rubric_id' => 'Рубрика',
            'disposition' => 'Расположение',
        ];
    }

    /**
     * Gets query for [[Issues]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getIssues()
    {
        return $this->hasMany(Issue::class, ['journal_id' => 'id']);
    }

    /**
     * Gets query for [[Rubric]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRubric()
    {
        return $this->hasOne(Rubric::class, ['id' => 'rubric_id']);
    }

    /**
     * Ссылка на детальную страницу
     * @return string
     */
    public function getUrl() {
        return Url::to(['journal/view', 'id' => $this->id]);
    }

    /**
     * Возвращает название рубрики
     * 
     * @param bool $linked кликабельное название на детальную страницу
     * @return string
     */
    public function showRubric($linked = false)
    {
        $rubric_name = null;
        $rubric = $this->rubric;
        if ($rubric) {
            $rubric_name = $rubric->getInfoTitle();
            if ($linked) {
                return Html::a(
                    $rubric_name,
                    $rubric->getUrl(),
                    [
                        'class' => 'text-link',
                        'data-pjax' => 0,
                        'title' => $rubric->title
                    ]
                );
            }
        }
        return $rubric_name;
    }

    /**
     * Возвращает заголовок журнала
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

    /**
     * Заголовок журнала для вывода на странице рубрики
     * 
     * @param bool $strong жирное начертание для заголовка
     * @param bool $linked кликабельное название на детальную страницу
     * @return string
     */
    public function getInfoTitle($strong = false, $linked = false)
    {
        return $this->showTitle($strong, $linked);
    }
}
