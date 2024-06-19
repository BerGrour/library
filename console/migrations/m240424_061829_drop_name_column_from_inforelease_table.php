<?php

use yii\db\Migration;

/**
 * Handles dropping columns from table `{{%inforelease}}`.
 */
class m240424_061829_drop_name_column_from_inforelease_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('inforelease', 'name');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('inforelease', 'name', $this->string(250)->defaultValue(null)->after('seria_id'));
    }
}
