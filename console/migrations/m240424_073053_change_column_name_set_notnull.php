<?php

use yii\db\Migration;

/**
 * Class m240424_073053_change_column_name_set_notnull
 */
class m240424_073053_change_column_name_set_notnull extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('infoarticle', 'name', $this->string(250)->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('infoarticle', 'name', $this->string(250)->null());
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240424_073053_change_column_name_set_notnull cannot be reverted.\n";

        return false;
    }
    */
}
