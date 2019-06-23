<?php

use yii\db\Migration;

/**
 * Class m190531_234755_products_models_alerts
 */
class m190531_234755_products_models_alerts extends Migration
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

        $this->createTable('{{%products_models_alerts}}', [
            'id'              => $this->primaryKey(),
            'alertId'         => $this->integer()->notNull(),
            'product_modelId' => $this->integer()->notNull(),
            'createdAt'       => $this->integer(),
            'updatedAt'       => $this->integer(),
            'createdBy'       => $this->integer(),
            'updatedBy'       => $this->integer(),
        ]);

        /*$this->insert('{{%products_models_alerts}}', [
            'alertId'         => 1,
            'product_modelId' => 1,
            'createdAt'       => 1559312912,
            'updatedAt'       => 1559312912,
            'createdBy'       => 1,
            'updatedBy'       => 1,
        ]);*/

        // creates index for column `idAlert`
        $this->createIndex(
            'idx-products_models_alerts-idAlert',
            'products_models_alerts',
            'alertId'
        );

        // add foreign key for table `{{%Alerts}}`
        $this->addForeignKey(
            'fk-products_models_alerts-idAlert',
            'products_models_alerts',
            'alertId',
            'alerts',
            'id',
            'CASCADE'
        );

        // creates index for column `idResources`
        $this->createIndex(
            'idx-products_models_model',
            'products_models_alerts',
            'product_modelId'
        );

        // add foreign key for table `{{%Resource}}`
        $this->addForeignKey(
            'fk-idx-products_models_model',
            'products_models_alerts',
            'product_modelId',
            'products_models',
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
            '{{%fk-products_models_alerts-idAlert}}',
            '{{%products_models_alerts}}'
        );

        // drops index for column `idAlert`
        $this->dropIndex(
            '{{%idx-products_models_alerts-idAlert}}',
            '{{%products_models_alerts}}'
        );

        // drops foreign key for table `{{%Resource}}`
        $this->dropForeignKey(
            '{{%fk-products_models_alerts-idResources}}',
            '{{%products_models_alerts}}'
        );

        // drops index for column `idResources`
        $this->dropIndex(
            '{{%idx-products_models_alerts-idResources}}',
            '{{%products_models_alerts}}'
        );

        $this->dropTable('{{%products_models_alerts}}');
    }
}
