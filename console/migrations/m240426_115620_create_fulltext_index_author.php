<?php

use yii\db\Migration;

/**
 * Class m240426_115620_create_fulltext_index_author
 */
class m240426_115620_create_fulltext_index_author extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE author ADD FULLTEXT INDEX
            advanced_search_author_surname (surname)");

        $this->execute("ALTER TABLE author ADD FULLTEXT INDEX
            advanced_search_author_name (name)");

        $this->execute("ALTER TABLE author ADD FULLTEXT INDEX
            advanced_search_author_middlename (middlename)");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('advanced_search_author_surname', 'author');
        $this->dropIndex('advanced_search_author_name', 'author');
        $this->dropIndex('advanced_search_author_middlename', 'author');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240426_115620_create_fulltext_index_author cannot be reverted.\n";

        return false;
    }
    */
}
