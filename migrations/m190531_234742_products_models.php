<?php

use yii\db\Migration;

/**
 * Class m190531_234742_products_models
 */
class m190531_234742_products_models extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%products_models}}', [
            'id'           => $this->primaryKey(),
            'productId'    => $this->integer(),
            'serial_model' => $this->string(),
            'name'         => $this->string(),
            'status'       => $this->smallInteger(1)->defaultValue(1),
            'createdAt'    => $this->integer(),
            'updatedAt'    => $this->integer(),
            'createdBy'    => $this->integer(),
            'updatedBy'    => $this->integer(),

        ], $tableOptions);

        $this->insert('{{%products_models}}', [
            'productId'    => 1,
            'serial_model' => 'LG 49LK5400PSA',
            'name'         => 'SMART TV LED 49" FHD',
            'status'       => 1,
            'createdAt'    => '1488153462',
            'updatedAt'    => '1488153462',
            'createdBy'    => '1',
            'updatedBy'    => '1',
        ]);

        $this->insert('{{%products_models}}', [
            'productId'    => 1,
            'serial_model' => 'LG 32LK540BPSA',
            'name'         => 'SMART TV LED 32 HD 720p',
            'status'       => 1,
            'createdAt'    => '1488153462',
            'updatedAt'    => '1488153462',
            'createdBy'    => '1',
            'updatedBy'    => '1',
        ]);

        $this->insert('{{%products_models}}', [
            'productId'    => 2,
            'serial_model' => 'LG CJ88',
            'name'         => 'XBOOM 2900W',
            'status'       => 1,
            'createdAt'    => '1488153462',
            'updatedAt'    => '1488153462',
            'createdBy'    => '1',
            'updatedBy'    => '1',
        ]);

        // creates index for column `parentId`
        $this->createIndex(
            'idx-products_models-parentId',
            'products_models',
            'productId'
        );

        // add foreign key for table `parentId`
        $this->addForeignKey(
            'fk-products_models-parentId',
            'products_models',
            'productId',
            'products',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropTable('{{%products_models}}');
    }
}
