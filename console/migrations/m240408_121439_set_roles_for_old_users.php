<?php

use common\models\User;
use yii\db\Migration;

/**
 * Class m240408_121439_set_roles_for_old_users
 */
class m240408_121439_set_roles_for_old_users extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        $reader = $auth->getRole('reader');

        $users = User::find()->where(['<>', 'username', 'admin'])->all();
        foreach ($users as $user) {
            $auth->assign($reader, $user->id);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $this->delete($auth->auth_assignment);

        $admin = $auth->getRole('admin');
        $user = User::findByUsername('admin');
        $auth->assign($admin, $user->id);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240408_121439_set_roles_for_old_users cannot be reverted.\n";

        return false;
    }
    */
}
