<?php 

namespace common\models;

use Yii;
use yii\base\Model;


class RoleAssignmentForm extends Model
{
    public $user_id;
    public $role;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['role'], 'safe'],
            [['user_id', 'role'], 'required', 'message' => 'Поле не может быть пустым'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'Пользователь',
            'role' => 'Роль',
        ];
    }

    /**
     * Смена роли пользователя
     * @return bool
     */
    public function assignRole()
    {
        $auth = Yii::$app->authManager;

        $role_auth = $auth->getRole($this->role);
        if ($role_auth) {
            $auth->revokeAll($this->user_id);
            $auth->assign($role_auth, $this->user_id);
        }

        return true;
    }
}