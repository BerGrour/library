<?php

use yii\db\Migration;

/**
 * Class m240502_105948_create_columns_withraw_for_tables
 */
class m240502_105948_create_columns_withraw_for_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('issue', 'withraw', $this->integer(10)->notNull()->defaultValue(0));
        $this->addColumn('statrelease', 'withraw', $this->integer(10)->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('issue', 'withraw');
        $this->dropColumn('statrelease', 'withraw');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240502_105948_create_columns_withraw_for_tables cannot be reverted.\n";

        return false;
    }
    */
}
