<?php

use yii\db\Migration;

/**
 * Class m240416_124611_add_new_columns_to_tables
 */
class m240416_124611_add_new_columns_to_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%article}}', 'key_words', $this->text()->after('annotation'));
        $this->addColumn('{{%book}}', 'key_words', $this->text()->after('withraw'));
        $this->addColumn('{{%statrelease}}', 'key_words', $this->text()->after('numbersk'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%article}}', 'key_words');
        $this->dropColumn('{{%book}}', 'key_words');
        $this->dropColumn('{{%statrelease}}', 'key_words');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240416_124611_add_new_columns_to_tables cannot be reverted.\n";

        return false;
    }
    */
}
