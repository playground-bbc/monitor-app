<?php

use yii\db\Migration;

/**
 * Class m190809_205340_users_profile_social
 */
class m190809_205340_users_profile_social extends Migration
{

    
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%users_profile_social}}', array(
            'id' => $this->primaryKey(),
            'userId'         => $this->integer()->notNull(),
            'screen_name' => $this->string()->notNull(),
            'oauth_token' => $this->string()->notNull(),
            'oauth_token_secret' => $this->string()->notNull(),
            'last_checked' => $this->integer(),
            'created_at' => 'DATETIME NOT NULL DEFAULT 0',
            'modified_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
               ), 
        $this->MySqlOptions);
        $this->addForeignKey('fk_account_user', $this->tableName, 'user_id', $this->tablePrefix.'users', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        echo "m190809_205340_users_profile_social cannot be reverted.\n";

        return false;
    }
    
}
