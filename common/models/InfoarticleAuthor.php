<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "infoarticle_author".
 *
 * @property int $id
 * @property int $infoarticle_id
 * @property int $author_id
 * @property int $type
 * 
 * @property Author $author
 * @property Infoarticle $infoarticle
 */
class InfoarticleAuthor extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infoarticle_author';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['infoarticle_id', 'author_id'], 'required'],
            [['infoarticle_id', 'author_id', 'type'], 'integer'],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => Author::class, 'targetAttribute' => ['author_id' => 'id']],
            [['infoarticle_id'], 'exist', 'skipOnError' => true, 'targetClass' => Infoarticle::class, 'targetAttribute' => ['infoarticle_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'infoarticle_id' => 'Infoarticle ID',
            'author_id' => 'Author ID',
            'type' => 'Тип связи'
        ];
    }

    /**
     * Gets query for [[Author]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(Author::class, ['id' => 'author_id']);
    }

    /**
     * Gets query for [[Infoarticle]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInfoarticle()
    {
        return $this->hasOne(Infoarticle::class, ['id' => 'infoarticle_id']);
    }
}
