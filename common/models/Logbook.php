<?php

namespace common\models;

use common\extensions\traits\EditionInfoTrait;
use Yii;

/**
 * This is the model class for table "logbook".
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $book_id
 * @property int|null $issue_id
 * @property int|null $statrelease_id
 * @property int $given_date
 * @property int|null $return_date
 *
 * @property Book $book
 * @property Issue $issue
 * @property Statrelease $statrelease
 * @property User $user
 */
class Logbook extends \yii\db\ActiveRecord
{
    use EditionInfoTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'logbook';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'given_date'], 'required'],
            [['user_id', 'book_id', 'issue_id', 'statrelease_id', 'given_date', 'return_date'], 'integer'],
            [['book_id'], 'exist', 'skipOnError' => true, 'targetClass' => Book::class, 'targetAttribute' => ['book_id' => 'id']],
            [['issue_id'], 'exist', 'skipOnError' => true, 'targetClass' => Issue::class, 'targetAttribute' => ['issue_id' => 'id']],
            [['statrelease_id'], 'exist', 'skipOnError' => true, 'targetClass' => Statrelease::class, 'targetAttribute' => ['statrelease_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Пользователь',
            'book_id' => 'Книга',
            'issue_id' => 'Журнальный выпуск',
            'statrelease_id' => 'Стат сборник',
            'given_date' => 'Дата выдачи',
            'return_date' => 'Дата возврата',
        ];
    }

    /**
     * Получает статус "на руках" для издания, учитывая права пользователя
     * 
     * @param \common\models\LogBook|null $model издание
     * @return string|null
     */
    static function getInfoAccess($model)
    {
        $result = null;
        if ($model) {
            $result = '<span class="status-edition"> ';
            if (Yii::$app->user->can('logbook/access')) {
                $result .= "Находится у - {$model->user->getLinkOnUser()}</span>";
            } else {
                $result .= "На руках</span>";
            }
        }
        return $result;
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
     * Проверяет на руках ли книга
     * 
     * @param int $book_id индекс книги
     * @return string|null
     */
    static function checkBookHands($book_id)
    {
        $book_on_hands = LogBook::findOne([
            'book_id' => $book_id,
            'return_date' => null
        ]);
        return LogBook::getInfoAccess($book_on_hands);
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
     * Проверяет на руках ли журнальный выпуск
     * 
     * @param int $issue_id индекс журнального выпуска
     * @return string|null
     */
    static function checkIssueHands($issue_id)
    {
        $issue_on_hands = LogBook::findOne([
            'issue_id' => $issue_id,
            'return_date' => null
        ]);
        return LogBook::getInfoAccess($issue_on_hands);
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
     * Проверяет на руках ли стат сборник
     * 
     * @param int $statrelease_id индекс стат сборника
     * @return string|null
     */
    static function checkStatreleaseHands($statrelease_id)
    {
        $statrelease_on_hands = LogBook::findOne([
            'statrelease_id' => $statrelease_id,
            'return_date' => null
        ]);
        return LogBook::getInfoAccess($statrelease_on_hands);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
