<?php

use \yii\db\Migration;

class m190124_110200_add_new_columns_to_user_table extends Migration
{
    public function up()
    {
        $this->addColumn('{{%user}}', 'verification_token', $this->string()->defaultValue(null));
        $this->addColumn('{{%user}}', 'fio', $this->string(100)->notNull());
        $this->addColumn('{{%user}}', 'second_email', $this->string()->unique());
    }

    public function down()
    {
        $this->dropColumn('{{%user}}', 'verification_token');
        $this->dropColumn('{{%user}}', 'fio');
        $this->dropColumn('{{%user}}', 'second_email');
    }
}
