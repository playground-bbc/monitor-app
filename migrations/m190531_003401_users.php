<?php

use yii\db\Migration;

/**
 * Class m190531_003401_users
 */
class m190531_003401_users extends Migration
{
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%users}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->unique(),
            'email' => $this->string()->notNull()->unique(),
            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->insert('{{%users}}', [
            'username'      => 'admin',
            'auth_key'      => 'tPwo4kDpN7JAz8Rrm9EwNAQ7q8F1p7FN',
            // deathnote
            'password_hash' => '$2y$13$Xv3tYWezdvWV9GRUUv1/8.NEC8CX4fp2MRntK5L0EBJXgwy49IF.K',
            'email'         => 'spiderbbc@gmail.com',
            'status'        => 10,
            'created_at'    => 0,
            'updated_at'    => 0,
        ]);

        $this->insert('{{%users}}', [
            'username'      => 'dafne',
            'auth_key'      => 'tPwo4kDpN7JAz8Rrm9EwNAQ7q8F1p7FN',
            // lgdafne
            'password_hash' => '$2y$13$szhao4QHkBT0IFhzqeb0seRdGOPr3UDWmdvTe1XRENMyxYaM6FoX6',
            'email'         => 'Dafne@example.com',
            'status'        => 10,
            'created_at'    => 0,
            'updated_at'    => 0,
        ]);

        $this->insert('{{%users}}', [
            'username'      => 'cristobal',
            'auth_key'      => 'tPwo4kDpN7JAz8Rrm9EwNAQ7q8F1p7FN',
            // lgcristobal
            'password_hash' => '$2y$13$q0QI6ocpOpe/t9FxmyuKQufKQO5ncqwkdZgkQRqWpIhySjKzQ4kZS',
            'email'         => 'cristobal@example.com',
            'status'        => 10,
            'created_at'    => 0,
            'updated_at'    => 0,
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%users}}');
    }
    
}
