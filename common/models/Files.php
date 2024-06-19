<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "files".
 *
 * @property int $id
 * @property string|null $filepath
 * @property string|null $filename
 *
 * @property Article[] $articles
 * @property Book[] $books
 */
class Files extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'files';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['filepath', 'filename'], 'string', 'max' => 250],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'filepath' => 'Путь к файлу',
            'filename' => 'Имя файла',
        ];
    }

    /**
     * Gets query for [[Articles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getArticles()
    {
        return $this->hasMany(Article::class, ['file_id' => 'id']);
    }

    /**
     * Gets query for [[Books]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBooks()
    {
        return $this->hasMany(Book::class, ['file_id' => 'id']);
    }
    
    /**
     * Gets query for [[Inforelease]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInforeleases()
    {
        return $this->hasMany(Inforelease::class, ['file_id' => 'id']);
    }

    /**
     * Возвращает ссылку на файл
     */
    public function getLinkOnFile()
    {
        $content = null;
        if ($this->filepath[0] != '/') {
            $content = '/';
        }
        $content .= $this->filepath;
        return $content;
    }
}
