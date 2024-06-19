<?php

use yii\db\Migration;

/**
 * Class m240423_080505_change_column_middlename_in_author_table
 */
class m240423_080505_change_column_middlename_in_author_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('author', 'middlename', $this->string(255)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('author', 'middlename', $this->string(255)->notNull());
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240423_080505_change_column_middlename_in_author_table cannot be reverted.\n";

        return false;
    }
    */
}
