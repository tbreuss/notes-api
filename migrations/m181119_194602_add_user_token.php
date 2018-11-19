<?php

use yii\db\Migration;

/**
 * Class m181119_194602_add_user_token
 */
class m181119_194602_add_user_token extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addColumn('users', 'auth_token', $this->string(100)->null()->after('salt'));
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropColumn('users', 'auth_token');
    }
}
