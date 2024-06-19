<?php

namespace common\models;

use Yii;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;

/**
 * This is the model class for table "article".
 *
 * @property int $id
 * @property string $name
 * @property int $pages
 * @property int $last_pages
 * @property string $annotation
 * @property string $key_words
 * @property int|null $issue_id
 * @property int|null $file_id
 *
 * @property ArticleAuthor[] $articleAuthors
 * @property Files $file
 * @property Issue $issue
 * @property Author[] $authors
 */
class Article extends \yii\db\ActiveRecord
{
    public $uploadedFile;
    public $articleAuthorIds = [];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'article';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'pages'], 'required', 'message' => 'Поле не может быть пустым'],
            [['name', 'annotation', 'key_words'], 'string'],
            [['pages', 'last_pages', 'issue_id', 'file_id'], 'integer', 'message' => 'Должно быть числом'],
            [['file_id'], 'exist', 'skipOnError' => true, 'targetClass' => Files::class, 'targetAttribute' => ['file_id' => 'id']],
            [['issue_id'], 'exist', 'skipOnError' => true, 'targetClass' => Issue::class, 'targetAttribute' => ['issue_id' => 'id']],
            [['uploadedFile'], 'file'],
            [['articleAuthorIds'], 'each', 'rule' => ['exist', 'targetClass' => Author::class, 'targetAttribute' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Наименование статьи',
            'pages' => 'Начальная страница',
            'last_pages' => 'Конечная страница',
            'annotation' => 'Аннотация',
            'key_words' => 'Ключевые слова',
            'issue_id' => 'ID выпуска',
            'file_id' => 'ID файла',
            'uploadedFile' => 'Файл',
            'articleAuthorIds' => 'Авторы',
        ];
    }

    public function afterFind()
    {
        parent::afterFind();

        foreach ($this->articleAuthors as $author_rel) {
            $this->articleAuthorIds[] = $author_rel->author->id;
        }
    }

    /**
     * Gets query for [[Author]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthors()
    {
        return $this
            ->hasMany(Author::class, ['id' => 'author_id'])
            ->via('articleAuthors');
    }

    /**
     * Gets query for [[ArticleAllAuthors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getArticleAllAuthors()
    {
        return $this->hasMany(ArticleAuthor::class, ['article_id' => 'id']);
    }

    /**
     * Gets query for [[ArticleAuthors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getArticleAuthors()
    {
        return $this->hasMany(ArticleAuthor::class, ['article_id' => 'id'])->where(['type' => 0]);
    }

    /**
     * Gets query for [[File]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFile()
    {
        return $this->hasOne(Files::class, ['id' => 'file_id']);
    }

    /**
     * Gets query for [[Issue]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getIssue()
    {
        return $this->hasOne(Issue::class, ['id' => 'issue_id']);
    }

    /**
     * Ссылка на детальную страницу
     * @return string
     */
    public function getUrl() {
        return Url::to(['article/view', 'id' => $this->id]);
    }

    /**
     * Возвращает кликабельного автора статьи
     * 
     * @param bool $linked кликабельное название на детальную страницу
     * @param string $target открытие ссылки в новой вкладке
     * @param bool $reverse порядок ФИО или ИОФ
     * @return string
     */
    public function showAuthor($link = false, $target = "_self", $reverse = false)
    {
        $separator = null;

        return $separator . implode(', ', array_map(function($author_rel) use ($link, $target, $reverse) {
            $nameParts = array_filter([$author_rel->author->name]);
            if (preg_match('/^\pL+$/u', $author_rel->author->middlename)) {
                $nameParts = array_filter([
                    mb_substr($author_rel->author->name, 0, 1),
                    mb_substr($author_rel->author->middlename, 0, 1)
                ]);
            }
            $withDots = implode('. ', $nameParts) . '.';
            if ($reverse) {
                $nameParts = array_filter([$withDots, $author_rel->author->surname]);
                $content = implode(' ', $nameParts);
            } else {
                $nameParts = array_filter([$author_rel->author->surname, $withDots]);
                $content = implode(' ', $nameParts);
            }

            if ($link) {
                return Html::a(
                    $content,
                    $author_rel->author->getUrl(),
                    [
                        'class' => 'text-link',
                        'data-pjax' => 0,
                        'target' => $target,
                        'title' => $content
                    ]
                );
            } else return $content;
        }, $this->articleAuthors));
    }

    /**
     * Возвращает массив с авторами
     * @return array
     */
    public function getArrayAuthors($delimeter = ' ')
    {
        $authors_rel = $this->articleAuthors;

        $authorSurnames = [];
        foreach ($authors_rel as $author_rel) {
            $authorSurnames[] = $author_rel->author->showFIO($delimeter);
        }
        return $authorSurnames;
    }

    /**
     * Возвращает название рубрики
     * 
     * @param bool $linked кликабельное название на детальную страницу
     * @return string
     */
    public function showRubric($linked = false) {
        $rubric_name = null;
        $rubric = $this->issue->journal->rubric;
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
     * Возвращает заголовок статьи
     * 
     * @param bool $strong жирное начертание для заголовка
     * @param bool $linked кликабельное название на детальную страницу
     * @param bool $with_file прикрепить к названию иконку с файлом
     * @param string $target открытие ссылки в новой вкладке
     * @return string
     */
    public function showTitle($strong = false, $linked = false, $with_file = false, $target = "_self") {
        $content = $this->name;
        $file = null;
        if ($strong) {
            $content = "<strong>{$content}</strong>";
        }
        if ($with_file && $this->file) {
            $file = Html::a(
                '<img src="/Files/Images/doc.png" class="image-file-link">',
                $this->file->getLinkOnFile(),
                [
                    'class' => 'custom-link-file',
                    'target' => "_blank",
                    'data-pjax' => 0,
                    'title' => 'Скачать',
                ]
            );
        }
        if ($linked) {
            return Html::a(
                $content,
                $this->getUrl(),
                [
                    'class' => 'text-link',
                    'data-pjax' => 0,
                    'target' => $target,
                    'title' => $this->annotation
                ]
            ) . $file;
        }
        return $content;
    }

    /**
     * Возвращает название на журнал и на выпуск
     * 
     * @param bool $linked кликабельное название на детальную страницу
     * @param bool $with_file нет функционала
     * @param string $target открытие ссылки в новой вкладке
     * @return string
     */
    public function showInfo($linked = false, $with_file = false, $target = "_self") {
        $journal = "<strong>{$this->issue->journal->name}</strong>";
        $issue = "№ {$this->issue->issuenumber} за {$this->issue->issueyear}";

        if ($linked) {
            $journal = Html::a(
                $journal,
                $this->issue->journal->getUrl(),
                [
                    'class' => 'text-link',
                    'data-pjax' => 0,
                    'target' => $target,
                    'title' => $this->issue->journal->name
                ]
            );
            $issue = Html::a(
                $issue,
                $this->issue->getUrl(),
                [
                    'class' => 'text-link',
                    'data-pjax' => 0,
                    'target' => $target,
                    'title' => $issue
                ]
            );
        }
        return $journal . " " . $issue . $this->getStatusOnHands();
    }

    /**
     * Возвращает краткую инфо статьи (<заголовок> <название_журнала> № <выпуска> за <год_выпуска>)
     * 
     * @param bool $linked кликабельное название на детальную страницу
     * @return string
     */
    public function getInfoTitle($linked = false)
    {
        $content = "{$this->showTitle(true, false, false)} {$this->issue->journal->name} 
            № {$this->issue->issuenumber} за {$this->issue->issueyear}";
        if($linked) {
            return Html::a(
                $content,
                $this->getUrl(),
                [
                    'class' => 'text-link',
                    'data-pjax' => 0,
                    'title' => $this->annotation
                ]
            );
        }
        return $content;
    }

    /**
     * Получение краткой информации о издании для отображения в корзине
     * @param int $abbreviation ограничение основного заголовка
     * @param int $abbreviation_second ограничение доп заголовка
     * @return string
     */
    public function getBriefInfo($abbreviation, $abbreviation_second)
    {
        $title = StringHelper::truncate($this->name, $abbreviation);
        $journal = StringHelper::truncate($this->issue->journal->name, $abbreviation_second);

        return  $title
        . "<strong> {$journal} - {$this->issue->issueyear}, №{$this->issue->issuenumber}</strong> "
        . "(<span style='color:brown;'>Статья</span>)";
    }

    /**
     * Возвращает статус выдано или нет
     * @return string|null
     */
    public function getStatusOnHands()
    {
        if ($this->issue->withraw) {
            return "<span class='status-edition'> [Списано]</span>";
        }
        return Logbook::checkIssueHands($this->issue->id);
    }
}
