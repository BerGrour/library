<?php

use yii\db\Migration;

/**
 * Class m240422_080608_change_column_annotation_in_article_table
 */
class m240422_080608_change_column_annotation_in_article_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('article', 'annotation', $this->text()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('article', 'annotation', $this->text()->notNull());
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240422_080608_change_column_annotation_in_article_table cannot be reverted.\n";

        return false;
    }
    */
}
