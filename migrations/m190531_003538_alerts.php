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
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%alerts}}',[
            'idAlert'   => $this->primaryKey(),
            'userId'    => $this->integer()->notNull(),
            'name'      => $this->string(),
            'status'    => $this->smallInteger(1)->defaultValue(1),
            'is_boolean'=> $this->smallInteger(1)->defaultValue(0),
            'start_date' => $this->integer(),
            'end_date' => $this->integer(),
            'createdAt' => $this->integer(),
            'updatedAt' => $this->integer(),
            'createdBy' => $this->integer(),
            'updatedBy' => $this->integer(),
            'typeResourceId' => $this->integer()->notNull(),

        ],$tableOptions);

    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable('{{%alerts}}');
    }
}
