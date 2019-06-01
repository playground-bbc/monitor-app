<?php

use yii\db\Migration;

/**
 * Class m190531_003538_alerts
 */
class m190531_003538_alerts extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%alerts}}',[
            'id'        => $this->primaryKey(),
            'userId'         => $this->integer()->notNull(),
            'name'           => $this->string(),
            'status'         => $this->smallInteger(1)->defaultValue(1),
            'is_boolean'     => $this->smallInteger(1)->defaultValue(0),
            'start_date'     => $this->integer(),
            'end_date'       => $this->integer(),
            'createdAt'      => $this->integer(),
            'updatedAt'      => $this->integer(),
            'createdBy'      => $this->integer(),
            'updatedBy'      => $this->integer(),

        ],$tableOptions);

        $this->insert('{{%alerts}}', [
            'userId'         => 1,
            'name'           => 'LiveChat',
            'status'         => 1,
            'is_boolean'     => 1,
            'start_date'     => 1559312912 ,
            'end_date'       => 1559312912 ,
            'createdAt'      => 1559312912,
            'updatedAt'      => 1559312912,
            'createdBy'      => 1,
            'updatedBy'      => 1,
        ]);

        $this->addForeignKey(
            'useralert_userId_alerts',
            'alerts',
            'userId',
            'users',
            'id',
            'CASCADE',
            'CASCADE'
        );

    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable('{{%alerts}}');
    }
}
