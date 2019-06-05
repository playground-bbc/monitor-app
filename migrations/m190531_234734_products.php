<?php

use yii\db\Migration;

/**
 * Class m190531_234734_products
 */
class m190531_234734_products extends Migration
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

        $this->createTable('{{%products}}', [
            'id'                => $this->primaryKey(),
            'categoryId'        => $this->integer(),
            'name'              => $this->string(),
            'abbreviation_name' => $this->string(),
            'status'            => $this->smallInteger(1)->defaultValue(1),
            'createdAt'         => $this->integer(),
            'updatedAt'         => $this->integer(),
            'createdBy'         => $this->integer(),
            'updatedBy'         => $this->integer(),

        ], $tableOptions);
        // Televisores
        $this->insert('{{%products}}', [
            'categoryId'         => 1,
            'name'              => 'Ultra HD 4K',
            'abbreviation_name' => '',
            'status'            =>  1,
            'createdAt'         => '1488153462',
            'updatedAt'         => '1488153462',
            'createdBy'         => '1',
            'updatedBy'         => '1',
        ]);

        $this->insert('{{%products}}', [
            'categoryId'         => 1,
            'name'              => 'HD',
            'abbreviation_name' => '',
            'status'            =>  1,
            'createdAt'         => '1488153462',
            'updatedAt'         => '1488153462',
            'createdBy'         => '1',
            'updatedBy'         => '1',
        ]);

        $this->insert('{{%products}}', [
            'categoryId'         => 1,
            'name'              => 'Full HD',
            'abbreviation_name' => '',
            'status'            =>  1,
            'createdAt'         => '1488153462',
            'updatedAt'         => '1488153462',
            'createdBy'         => '1',
            'updatedBy'         => '1',
        ]);

        $this->insert('{{%products}}', [
            'categoryId'         => 1,
            'name'              => 'NanoCell 4K',
            'abbreviation_name' => '',
            'status'            =>  1,
            'createdAt'         => '1488153462',
            'updatedAt'         => '1488153462',
            'createdBy'         => '1',
            'updatedBy'         => '1',
        ]);

        $this->insert('{{%products}}', [
            'categoryId'         => 1,
            'name'              => 'OLED 4K',
            'abbreviation_name' => '',
            'status'            =>  1,
            'createdAt'         => '1488153462',
            'updatedAt'         => '1488153462',
            'createdBy'         => '1',
            'updatedBy'         => '1',
        ]);

        $this->insert('{{%products}}', [
            'categoryId'         => 1,
            'name'              => 'OLED 4K',
            'abbreviation_name' => '',
            'status'            =>  1,
            'createdAt'         => '1488153462',
            'updatedAt'         => '1488153462',
            'createdBy'         => '1',
            'updatedBy'         => '1',
        ]);
        // end Televisores

        // Audio
        $this->insert('{{%products}}', [
            'categoryId'         => 2,
            'name'              => 'Minicomponentes',
            'abbreviation_name' => '',
            'status'            =>  1,
            'createdAt'         => '1488153462',
            'updatedAt'         => '1488153462',
            'createdBy'         => '1',
            'updatedBy'         => '1',
        ]);

        $this->insert('{{%products}}', [
            'categoryId'         => 2,
            'name'              => 'Soundbars',
            'abbreviation_name' => '',
            'status'            =>  1,
            'createdAt'         => '1488153462',
            'updatedAt'         => '1488153462',
            'createdBy'         => '1',
            'updatedBy'         => '1',
        ]);

        $this->insert('{{%products}}', [
            'categoryId'         => 2,
            'name'              => 'Parlantes Portátiles Bluetooth',
            'abbreviation_name' => '',
            'status'            =>  1,
            'createdAt'         => '1488153462',
            'updatedAt'         => '1488153462',
            'createdBy'         => '1',
            'updatedBy'         => '1',
        ]);
        // end Audio

        // refreigeradores

        $this->insert('{{%products}}', [
            'categoryId'         => 3,
            'name'              => 'Refrigeradores',
            'abbreviation_name' => '',
            'status'            =>  1,
            'createdAt'         => '1488153462',
            'updatedAt'         => '1488153462',
            'createdBy'         => '1',
            'updatedBy'         => '1',
        ]);

        $this->insert('{{%products}}', [
            'categoryId'         => 3,
            'name'              => 'Lavadoras',
            'abbreviation_name' => '',
            'status'            =>  1,
            'createdAt'         => '1488153462',
            'updatedAt'         => '1488153462',
            'createdBy'         => '1',
            'updatedBy'         => '1',
        ]);

        $this->insert('{{%products}}', [
            'categoryId'         => 3,
            'name'              => 'Microondas',
            'abbreviation_name' => '',
            'status'            =>  1,
            'createdAt'         => '1488153462',
            'updatedAt'         => '1488153462',
            'createdBy'         => '1',
            'updatedBy'         => '1',
        ]);


        // creates index for column `categoryId`
        $this->createIndex(
            'idx-products-categoryId',
            'products',
            'categoryId'
        );

        // add foreign key for table `category`
        $this->addForeignKey(
            'fk-products-categoryId',
            'products',
            'categoryId',
            'products_categories',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropTable('{{%products}}');
    }
}
