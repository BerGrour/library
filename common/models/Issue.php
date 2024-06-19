<?php

namespace common\models;

use Yii;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;

/**
 * This is the model class for table "issue".
 *
 * @property int $id
 * @property int|null $journal_id
 * @property int $issueyear
 * @property string $issuenumber
 * @property string|null $issuedate
 * @property int $withraw
 * @property Article[] $articles
 * @property Journal $journal
 */
class Issue extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'issue';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['journal_id', 'withraw', 'issueyear'], 'integer'],
            [['issueyear', 'withraw', 'issuenumber'], 'required'],
            [['issuedate'], 'date', 'format' => 'php:Y-m-d', 'message' => 'Неверный формат даты'],
            [['issuenumber'], 'string', 'max' => 9],
            [['journal_id'], 'exist', 'skipOnError' => true, 'targetClass' => Journal::class, 'targetAttribute' => ['journal_id' => 'id']],
            [['withraw'], 'frontend\validators\WithrawValidator'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'journal_id' => 'ID журнала',
            'issueyear' => 'Год выпуска',
            'issuenumber' => 'Номер выпуска',
            'issuedate' => 'Дата выпуска',
            'withraw' => 'Списано',
        ];
    }

    /**
     * Gets query for [[Articles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getArticles()
    {
        return $this->hasMany(Article::class, ['issue_id' => 'id']);
    }

    /**
     * Gets query for [[Journal]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getJournal()
    {
        return $this->hasOne(Journal::class, ['id' => 'journal_id']);
    }

    /**
     * Ссылка на детальную страницу
     * @return string
     */
    public function getUrl() {
        return Url::to(['issue/view', 'id' => $this->id]);
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
        $rubric = $this->journal->rubric;
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
        $content = $this->journal->name;
        if ($strong) {
            $content = "<strong>{$content}</strong>";
        }
        if ($linked) {
            return Html::a(
                $content,
                $this->journal->getUrl(),
                [
                    'class' => 'text-link',
                    'data-pjax' => 0,
                    'target' => $target,
                    'title' => $this->journal->name
                ]
            );
        }
        return $content;
    }

    /**
     * Возвращает кликабельные ссылки на выпуск
     * 
     * @param bool $linked кликабельное название на детальную страницу
     * @param bool $with_file нет функционала
     * @param string $target открытие ссылки в новой вкладке
     * @return string
     */
    public function showInfo($linked = false, $with_file = false, $target = "_self") {
        $content = "№ {$this->issuenumber} за {$this->issueyear}";
        if ($linked) {
            $content = Html::a(
                $content,
                $this->getUrl(),
                [
                    'class' => 'text-link',
                    'data-pjax' => 0,
                    'target' => $target,
                    'title' => $content
                ]
            ) . $this->getStatusOnHands();
        }
        if ($this->withraw) {
            $content .= '<span class="status-edition"> [Списано]</span> ';
        }
        return $content;
    }

    /**
     * Краткая инфо журнального выпуска для вывода на главной странице
     * @return string
     */
    public function getLibraryLink()
    {
        $issueNumber = $this->issuenumber;
        $issueJournal = $this->journal->name;
        $issueYear = $this->issueyear;
        if (isset($this->journal->rubric)) {
            $rubric = $this->journal->rubric->title;
        }
        $link = $issueJournal.' № '. $issueNumber.' за '.$issueYear." год ";;
        if (isset($rubric)) $link .= "({$rubric})";
        return $link;
    }

    /**
     * Получение краткой информации о издании для отображения в корзине
     * @param int $abbreviation ограничение основного заголовка
     * @param int $abbreviation_second ограничение доп заголовка
     * @return string
     */
    public function getBriefInfo($abbreviation, $abbreviation_second)
    {
        return StringHelper::truncate($this->journal->name, $abbreviation) 
        . "<strong> {$this->issueyear}, №{$this->issuenumber}</strong> "
        . "(<span style='color:green;'>Выпуск</span>)";
    }

    /**
     * Возвращает query выпусков журнала в порядке:
     * Сначала год выпуска по умешьшению, затем по номеру выпуска по возрастанию
     * 
     * @param int $journal_id индекс журнала
     * @return \yii\db\ActiveQuery
     */
    static function getIssuesOrdered($journal_id)
    {
        return Issue::find()->where(['journal_id' => $journal_id])->orderBy([
            'issueyear' => SORT_DESC,
            new \yii\db\Expression("CONVERT(SUBSTRING_INDEX(issuenumber, '-', 1), SIGNED)"),
            new \yii\db\Expression("CASE WHEN LOCATE('-', issuenumber) > 0 THEN CONVERT(SUBSTRING_INDEX(issuenumber, '-', -1), SIGNED) END"),
            'issuenumber' => SORT_ASC
        ]);
    }

    /**
     * Возвращает статус выдано или нет
     * @return string|null
     */
    public function getStatusOnHands()
    {
        $on_hands = Logbook::checkIssueHands($this->id);
        if ($on_hands) return $on_hands;
        return null;
    }

    /**
     * Метод для проверки статуса выдачи издания
     * 
     * @return bool на руках или нет
     */
    public function isBorrowed()
    {
        $logbook = Logbook::find()->where([
            'issue_id' => $this->id,
            'return_date' => null
        ]);
        if ($logbook->exists()) return true;
        return false;
    }
}


