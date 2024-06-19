<?php

namespace common\models;

use Yii;
use yii\helpers\Url;

/**
 * This is the model class for table "author".
 *
 * @property int $id
 * @property string $surname
 * @property string $name
 * @property string $middlename
 *
 * @property ArticleAuthor[] $articleAuthors
 * @property BookAuthor[] $bookAuthors
 * @property InfoarticleAuthor[] $infoarticleAuthors
 */
class Author extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'author';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['surname', 'name'], 'required', 'message' => 'Поле не может быть пустым'],
            [['surname', 'name', 'middlename'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'surname' => 'Фамилия',
            'name' => 'Имя',
            'middlename' => 'Отчество',
        ];
    }

    /**
     * Gets query for [[ArticleAuthors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getArticleAuthors()
    {
        return $this->hasMany(ArticleAuthor::class, ['author_id' => 'id']);
    }

    /**
     * Gets query for [[BookAuthors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBookAuthors()
    {
        return $this->hasMany(BookAuthor::class, ['author_id' => 'id']);
    }

    /**
     * Gets query for [[InfoarticleAuthors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInfoarticleAuthors()
    {
        return $this->hasMany(InfoarticleAuthor::class, ['author_id' => 'id']);
    }

    public function getBooks()
    {
        return $this
            ->hasMany(Book::class, ['id' => 'book_id'])
            ->via('bookAuthors');
    }

    public function getArticles()
    {
        return $this
            ->hasMany(Article::class, ['id' => 'article_id'])
            ->via('articleAuthors');
    }

    public function getInfoarticles()
    {
        return $this
            ->hasMany(Infoarticle::class, ['id' => 'infoarticle_id'])
            ->via('infoarticleAuthors');
    }

    /**
     * Ссылка на детальную страницу
     * @return string
     */
    public function getUrl() {
        return Url::to(['author/view', 'id' => $this->id]);
    }

    /**
     * Возвращает полное ФИО автора
     * 
     * @return string
     */
    public function showFIO($delimeter = ' ')
    {
        $res = $this->surname;
        if (trim($this->name) != '') {
            $res .= $delimeter . mb_substr($this->name, 0, 1) . '.';
        }
        if (trim($this->middlename) != '') {
            $res .= ' ' . mb_substr($this->middlename, 0, 1) . '.';
        }
        return $res;
    }
}
