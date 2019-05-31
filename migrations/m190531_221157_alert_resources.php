<?php

use yii\db\Migration;

/**
 * Class m190531_221157_alert_resources
 */
class m190531_221157_alert_resources extends Migration
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

        $this->createTable('{{%alert_resources}}', [
            'id' => $this->primaryKey(),
            'idAlert'          => $this->integer()->notNull(),
            'idResources'      => $this->integer()->notNull(),
            'data_json'        =>  $this->getDb()->getSchema()->createColumnSchemaBuilder("")->append("json"),
            'createdAt'        => $this->integer(),
            'updatedAt'        => $this->integer(),
            'createdBy'        => $this->integer(),
            'updatedBy'        => $this->integer(),
        ]);

        $this->insert('{{%alert_resources}}', [
            'idAlert'        => 1,
            'idResources'    => 1,
            'data_json'      => '{}',
            'createdAt'      => 1559312912,
            'updatedAt'      => 1559312912,
            'createdBy'      => 1,
            'updatedBy'      => 1,
        ]);

        // creates index for column `idAlert`
        $this->createIndex(
            'idx-AlertResources-idAlert',
            'alert_resources',
            'idAlert'
        );

        // add foreign key for table `{{%Alerts}}`
        $this->addForeignKey(
            'fk-AlertResources-idAlert',
            'alert_resources',
            'idAlert',
            'Alerts',
            'id',
            'CASCADE'
        );

        // creates index for column `idResources`
        $this->createIndex(
            'idx-AlertResources-idResources',
            'alert_resources',
            'idResources'
        );

        // add foreign key for table `{{%Resource}}`
        $this->addForeignKey(
            'fk-AlertResources-idResources',
            'alert_resources',
            'idResources',
            'Resource',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        // drops foreign key for table `{{%Alerts}}`
        $this->dropForeignKey(
            '{{%fk-AlertResources-idAlert}}',
            '{{%AlertResources}}'
        );

        // drops index for column `idAlert`
        $this->dropIndex(
            '{{%idx-AlertResources-idAlert}}',
            '{{%AlertResources}}'
        );

        // drops foreign key for table `{{%Resource}}`
        $this->dropForeignKey(
            '{{%fk-AlertResources-idResources}}',
            '{{%AlertResources}}'
        );

        // drops index for column `idResources`
        $this->dropIndex(
            '{{%idx-AlertResources-idResources}}',
            '{{%AlertResources}}'
        );

        $this->dropTable('{{%AlertResources}}');
    }
}
