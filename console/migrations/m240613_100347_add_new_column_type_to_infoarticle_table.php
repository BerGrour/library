<?php

use yii\db\Migration;

/**
 * Class m240613_100347_add_new_column_type_to_infoarticle_table
 */
class m240613_100347_add_new_column_type_to_infoarticle_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%infoarticle}}', 'type', $this->smallInteger(6)->defaultValue(1)->notNull());

        $this->update('infoarticle', ['type' => 2], ['LIKE', 'recieptdate', '-00']);
        $this->update('infoarticle', ['type' => 3], ['LIKE', 'source', 'http']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%infoarticle}}', 'type');

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240613_100347_add_new_column_type_to_infoarticle_table cannot be reverted.\n";

        return false;
    }
    */
}
