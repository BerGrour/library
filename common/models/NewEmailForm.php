<?php

namespace common\models;

use common\models\User;
use Yii;
use yii\base\Model;

/**
 * Login form
 */
class NewEmailForm extends Model
{
    public $email;
    public $login;

    private $_user;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['email', 'required', 'message' => 'Поле не может быть пустым'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'Почтовый адрес уже используется.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'email' => 'E-mail',         
        ];
    }

    /**
     * Присваивает пользователю указанную почту
     * @return bool|null
     */
    public function setNewEmail()
    {
        if (!$this->validate()) {
            return null;
        }
        $user = $this->getUser();
        $user->email = $this->email;
        $user->generateEmailVerificationToken();

        return $user->save() && $this->sendEmail($user);
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsername($this->login);
        }

        return $this->_user;
    }

    /**
     * Отправка сообщения
     * 
     * @param \common\models\User|null $user пользователь
     * @return bool whether this message is sent successfully.
     */
    protected function sendEmail($user)
    {
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo($this->email)
            ->setSubject('Аккаунт зарегистрирован на ' . Yii::$app->name)
            ->send();
    }
}
