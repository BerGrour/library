<?php

use yii\db\Migration;

/**
 * Class m240424_073101_create_fulltext_index_for_tables
 */
class m240424_073101_create_fulltext_index_for_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE statrelease ADD FULLTEXT INDEX
            idx_global_search_statrelease (name, additionalname, response,
            publishplace, authorsign, key_words)");

        $this->execute("ALTER TABLE infoarticle ADD FULLTEXT INDEX
            idx_global_search_infoarticle (name, additionalinfo, source)");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx_global_search_statrelease', 'statrelease');
        $this->dropIndex('idx_global_search_infoarticle', 'infoarticle');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240424_073101_create_fulltext_index_for_tables cannot be reverted.\n";

        return false;
    }
    */
}
