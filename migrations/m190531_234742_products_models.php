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
            'status'       => $this->smallInteger(1)->defaultValue(1),
            'createdAt'    => $this->integer(),
            'updatedAt'    => $this->integer(),
            'createdBy'    => $this->integer(),
            'updatedBy'    => $this->integer(),

        ], $tableOptions);
        // tv
        /*$this->insert('{{%products_models}}', [
            'productId'    => 1,
            'serial_model' => 'LG 49LK5400PSA',
            'status'       => 1,
            'createdAt'    => '1488153462',
            'updatedAt'    => '1488153462',
            'createdBy'    => '1',
            'updatedBy'    => '1',
        ]);

        $this->insert('{{%products_models}}', [
            'productId'    => 1,
            'serial_model' => 'LG 43LK5700PSC',
            'status'       => 1,
            'createdAt'    => '1488153462',
            'updatedAt'    => '1488153462',
            'createdBy'    => '1',
            'updatedBy'    => '1',
        ]);

        $this->insert('{{%products_models}}', [
            'productId'    => 1,
            'serial_model' => 'LG 32LK540BPSA',
            'status'       => 1,
            'createdAt'    => '1488153462',
            'updatedAt'    => '1488153462',
            'createdBy'    => '1',
            'updatedBy'    => '1',
        ]);

        $this->insert('{{%products_models}}', [
            'productId'    => 1,
            'serial_model' => 'LG 55UK6200PSA',
            'status'       => 1,
            'createdAt'    => '1488153462',
            'updatedAt'    => '1488153462',
            'createdBy'    => '1',
            'updatedBy'    => '1',
        ]);

        $this->insert('{{%products_models}}', [
            'productId'    => 1,
            'serial_model' => 'LG 60UK6200PSA',
            'status'       => 1,
            'createdAt'    => '1488153462',
            'updatedAt'    => '1488153462',
            'createdBy'    => '1',
            'updatedBy'    => '1',
        ]);

        $this->insert('{{%products_models}}', [
            'productId'    => 1,
            'serial_model' => 'LG 43UK6300PSB',
            'status'       => 1,
            'createdAt'    => '1488153462',
            'updatedAt'    => '1488153462',
            'createdBy'    => '1',
            'updatedBy'    => '1',
        ]);

        $this->insert('{{%products_models}}', [
            'productId'    => 1,
            'serial_model' => 'LG 50UK6300PSB',
            'status'       => 1,
            'createdAt'    => '1488153462',
            'updatedAt'    => '1488153462',
            'createdBy'    => '1',
            'updatedBy'    => '1',
        ]);*/
        //end tv

        // Audio

        // end Audio


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
