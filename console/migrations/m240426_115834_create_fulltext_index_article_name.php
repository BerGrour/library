<?php

use yii\db\Migration;

/**
 * Class m240426_115834_create_fulltext_index_article_name
 */
class m240426_115834_create_fulltext_index_article_name extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE article ADD FULLTEXT INDEX
            advanced_search_article_name (name)");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('advanced_search_article_name', 'article');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240426_115834_create_fulltext_index_article_name cannot be reverted.\n";

        return false;
    }
    */
}
