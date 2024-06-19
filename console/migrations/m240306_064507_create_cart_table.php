<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%cart}}`.
 */
class m240306_064507_create_cart_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // cart
        $this->createTable('{{%cart}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->notNull(),
            'user_id' => $this->integer(11)->notNull(),
        ]);
        $this->createIndex('idx-cart-user_id','cart','user_id');
        $this->addForeignKey('fk-cart-user_id','cart','user_id','user','id','CASCADE');

        // many2many cart_editions
        $this->createTable('{{%cart_editions}}', [
            'id' => $this->primaryKey(),
            'cart_id' => $this->integer(11)->notNull(),
            'book_id' => $this->bigInteger(20)->unsigned(),
            'issue_id' => $this->bigInteger(20)->unsigned(),
            'article_id' => $this->bigInteger(20)->unsigned(),
            'statrelease_id' => $this->bigInteger(20)->unsigned(),
        ]);

        $this->createIndex('idx-cart_editions-cart_id','cart_editions','cart_id');
        $this->addForeignKey('fk-cart_editions-cart_id','cart_editions','cart_id','cart','id','CASCADE');

        $this->createIndex('idx-cart_editions-book_id','cart_editions','book_id');
        $this->addForeignKey('fk-cart_editions-book_id','cart_editions','book_id','book','id','CASCADE');

        $this->createIndex('idx-cart_editions-issue_id','cart_editions','issue_id');
        $this->addForeignKey('fk-cart_editions-issue_id','cart_editions','issue_id','issue','id','CASCADE');

        $this->createIndex('idx-cart_editions-article_id','cart_editions','article_id');
        $this->addForeignKey('fk-cart_editions-article_id','cart_editions','article_id','article','id','CASCADE');

        $this->createIndex('idx-cart_editions-statrelease_id','cart_editions','statrelease_id');
        $this->addForeignKey('fk-cart_editions-statrelease_id','cart_editions','statrelease_id','statrelease','id','CASCADE');
}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // cart_editions
        $this->dropForeignKey('fk-cart_editions-cart_id','cart_editions');
        $this->dropIndex('idx-cart_editions-cart_id','cart_editions');

        $this->dropForeignKey('fk-cart_editions-book_id','cart_editions');
        $this->dropIndex('idx-cart_editions-book_id','cart_editions');

        $this->dropForeignKey('fk-cart_editions-issue_id','cart_editions');
        $this->dropIndex('idx-cart_editions-issue_id','cart_editions');

        $this->dropForeignKey('fk-cart_editions-article_id','cart_editions');
        $this->dropIndex('idx-cart_editions-article_id','cart_editions');

        $this->dropForeignKey('fk-cart_editions-statrelease_id','cart_editions');
        $this->dropIndex('idx-cart_editions-statrelease_id','cart_editions');

        $this->dropTable('{{%cart_editions}}');

        // cart
        $this->dropForeignKey('fk-cart-user_id','cart');
        $this->dropIndex('idx-cart-user_id','cart');
        $this->dropTable('{{%cart}}');
    }
}
