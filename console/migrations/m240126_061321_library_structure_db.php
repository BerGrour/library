<?php

use yii\db\Migration;

/**
 * Class m240126_061321_library_structure_db
 */
class m240126_061321_library_structure_db extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute(file_get_contents(__DIR__ .
            '/library.sql'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240126_061321_library_structure_db cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240126_061321_library_new cannot be reverted.\n";

        return false;
    }
    */
}
