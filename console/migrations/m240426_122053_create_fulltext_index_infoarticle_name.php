<?php

use yii\db\Migration;

/**
 * Class m240426_122053_create_fulltext_index_infoarticle_name
 */
class m240426_122053_create_fulltext_index_infoarticle_name extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE infoarticle ADD FULLTEXT INDEX
            advanced_search_infoarticle_name (name)");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('advanced_search_infoarticle_name', 'infoarticle');

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240426_122053_create_fulltext_index_infoarticle_name cannot be reverted.\n";

        return false;
    }
    */
}
