<?php

use yii\db\Migration;

/**
 * Handles dropping columns from table `{{%infoarticle}}`.
 */
class m240424_063510_drop_edition_column_from_infoarticle_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('infoarticle', 'edition');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('infoarticle', 'edition', $this->string(250)->defaultValue(null)->after('source'));
    }
}
