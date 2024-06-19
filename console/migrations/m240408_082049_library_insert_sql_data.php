<?php

use yii\db\Migration;

/**
 * Class m240408_082049_library_insert_sql_data
 */
class m240408_082049_library_insert_sql_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute(file_get_contents(__DIR__ .
            '/library-insert.sql'
        ));
    }


    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240408_082049_library_insert_sql_data cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240408_082049_library_insert_sql_data cannot be reverted.\n";

        return false;
    }
    */
}
