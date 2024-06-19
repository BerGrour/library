<?php

use yii\db\Migration;

/**
 * Class m240610_073320_add_column_infoarticle_id_table_cart_editions
 */
class m240610_073320_add_new_column_infoarticle_id_table_cart_editions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%cart_editions}}', 'infoarticle_id', $this->bigInteger(20)->unsigned());

        $this->createIndex('idx-cart_editions-infoarticle_id', 'cart_editions', 'infoarticle_id');
        $this->addForeignKey('fk-cart_editions-infoarticle_id', 'cart_editions', 'infoarticle_id', 'infoarticle', 'id', 'RESTRICT');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-cart_editions-infoarticle_id','cart_editions');
        $this->dropIndex('idx-cart_editions-infoarticle_id','cart_editions');

        $this->dropColumn('{{%cart_editions}}', 'infoarticle_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240610_073320_add_column_infoarticle_id_table_cart_editions cannot be reverted.\n";

        return false;
    }
    */
}
