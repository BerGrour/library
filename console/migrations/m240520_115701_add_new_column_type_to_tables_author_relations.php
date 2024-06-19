<?php

use yii\db\Migration;

/**
 * Class m240520_115701_add_new_column_type_to_tables_author_relations
 */
class m240520_115701_add_new_column_type_to_tables_author_relations extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%article_author}}', 'type', $this->smallInteger(6)->defaultValue(0));
        $this->addColumn('{{%book_author}}', 'type', $this->smallInteger(6)->defaultValue(0));
        $this->addColumn('{{%infoarticle_author}}', 'type', $this->smallInteger(6)->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%article_author}}', 'type');
        $this->dropColumn('{{%book_author}}', 'type');
        $this->dropColumn('{{%infoarticle_author}}', 'type');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240520_115701_add_new_column_type_to_tables_author_relations cannot be reverted.\n";

        return false;
    }
    */
}
