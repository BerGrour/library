<?php

use yii\db\Migration;

/**
 * Class m240423_135032_create_fulltext_index_for_tables
 */
class m240423_135032_create_fulltext_index_for_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE book ADD FULLTEXT INDEX idx_global_search_book
            (additionalname, response, bookinfo, publishplace, publishhouse,
            annotation, key_words, code, name, ISBN, authorsign)");

        $this->execute("ALTER TABLE article ADD FULLTEXT INDEX
            idx_global_search_article (name, annotation, key_words)");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx_global_search_book','book');
        $this->dropIndex('idx_global_search_article','article');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240423_135032_create_fulltext_index_for_tables cannot be reverted.\n";

        return false;
    }
    */
}
