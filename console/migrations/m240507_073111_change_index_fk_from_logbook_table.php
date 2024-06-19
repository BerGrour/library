<?php

use yii\db\Migration;

/**
 * Class m240507_073111_change_index_fk_from_logbook_table
 */
class m240507_073111_change_index_fk_from_logbook_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('logbook_book_id_foreign', 'logbook');
        $this->addForeignKey('logbook_book_id_foreign', 'logbook', 'book_id', 'book', 'id', 'CASCADE');
        
        $this->dropForeignKey('logbook_issue_id_foreign', 'logbook');
        $this->addForeignKey('logbook_issue_id_foreign', 'logbook', 'issue_id', 'issue', 'id', 'CASCADE');

        $this->dropForeignKey('logbook_statrelease_id_foreign', 'logbook');
        $this->addForeignKey('logbook_statrelease_id_foreign', 'logbook', 'statrelease_id', 'statrelease', 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('logbook_book_id_foreign', 'logbook');
        $this->addForeignKey('logbook_book_id_foreign', 'logbook', 'book_id', 'book', 'id', 'RESTRICT');
        
        $this->dropForeignKey('logbook_issue_id_foreign', 'logbook');
        $this->addForeignKey('logbook_issue_id_foreign', 'logbook', 'issue_id', 'issue', 'id', 'RESTRICT');

        $this->dropForeignKey('logbook_statrelease_id_foreign', 'logbook');
        $this->addForeignKey('logbook_statrelease_id_foreign', 'logbook', 'statrelease_id', 'statrelease', 'id', 'RESTRICT');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240507_073111_change_index_fk_from_logbook_table cannot be reverted.\n";

        return false;
    }
    */
}
