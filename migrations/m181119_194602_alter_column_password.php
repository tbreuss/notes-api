<?php

use yii\db\Migration;

/**
 * Class m181119_194602_alter_column_password
 */
class m181119_194602_alter_column_password extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('users', 'password', $this->string(60)->notNull());
        $this->dropColumn('users', 'salt');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "- m181119_194602_alter_column_password cannot be reverted.\n";
    }

}
