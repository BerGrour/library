<?php

namespace common\models;

use common\extensions\traits\EditionInfoTrait;
use Yii;

/**
 * This is the model class for table "cart_editions".
 *
 * @property int $id
 * @property int $cart_id
 * @property int|null $book_id
 * @property int|null $issue_id
 * @property int|null $article_id
 * @property int|null $statrelease_id
 * @property int|null $infoarticle_id
 *
 * @property Article $article
 * @property Book $book
 * @property Cart $cart
 * @property Issue $issue
 * @property Statrelease $statrelease
 */
class CartEditions extends \yii\db\ActiveRecord
{
    use EditionInfoTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cart_editions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cart_id'], 'required'],
            [['cart_id', 'book_id', 'issue_id', 'article_id', 'statrelease_id', 'infoarticle_id'], 'integer'],
            [['article_id'], 'exist', 'skipOnError' => true, 'targetClass' => Article::class, 'targetAttribute' => ['article_id' => 'id']],
            [['book_id'], 'exist', 'skipOnError' => true, 'targetClass' => Book::class, 'targetAttribute' => ['book_id' => 'id']],
            [['cart_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cart::class, 'targetAttribute' => ['cart_id' => 'id']],
            [['issue_id'], 'exist', 'skipOnError' => true, 'targetClass' => Issue::class, 'targetAttribute' => ['issue_id' => 'id']],
            [['statrelease_id'], 'exist', 'skipOnError' => true, 'targetClass' => Statrelease::class, 'targetAttribute' => ['statrelease_id' => 'id']],
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
            'cart_id' => 'Cart ID',
            'book_id' => 'Book ID',
            'issue_id' => 'Issue ID',
            'article_id' => 'Article ID',
            'statrelease_id' => 'Statrelease ID',
            'infoarticle_id' => 'Infoarticle ID'
        ];
    }

    /**
     * Gets query for [[Article]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getArticle()
    {
        return $this->hasOne(Article::class, ['id' => 'article_id']);
    }

    /**
     * Gets query for [[Book]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBook()
    {
        return $this->hasOne(Book::class, ['id' => 'book_id']);
    }

    /**
     * Gets query for [[Cart]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCart()
    {
        return $this->hasOne(Cart::class, ['id' => 'cart_id']);
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
     * Gets query for [[Statrelease]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStatrelease()
    {
        return $this->hasOne(Statrelease::class, ['id' => 'statrelease_id']);
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
