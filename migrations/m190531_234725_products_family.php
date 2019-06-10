<?php

use yii\db\Migration;

/**
 * Class m190531_234725_products_family
 */
class m190531_234725_products_family extends Migration
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

        $this->createTable('{{%products_family}}', [
            'id'                => $this->primaryKey(),
            'parentId'          => $this->integer(),
            'name'              => $this->string(),
            'abbreviation_name' => $this->string(),
            'status'            => $this->smallInteger(1)->defaultValue(1),
            'createdAt'         => $this->integer(),
            'updatedAt'         => $this->integer(),
            'createdBy'         => $this->integer(),
            'updatedBy'         => $this->integer(),

        ], $tableOptions);
        // Home Entertainment
        $this->insert('{{%products_family}}', [
            'parentId'          => null,
            'name'              => 'Home Entertainment',
            'abbreviation_name' => 'HE',
            'status'            => 1,
            'createdAt'         => '1488153462',
            'updatedAt'         => '1488153462',
            'createdBy'         => '1',
            'updatedBy'         => '1',
        ]);

        $this->insert('{{%products_family}}', [
            'parentId'          => 1,
            'name'              => 'Televisores',
            'abbreviation_name' => 'TV',
            'status'            => 1,
            'createdAt'         => '1488153462',
            'updatedAt'         => '1488153462',
            'createdBy'         => '1',
            'updatedBy'         => '1',
        ]);

        $this->insert('{{%products_family}}', [
            'parentId'          => 1,
            'name'              => 'Audio',
            'abbreviation_name' => '',
            'status'            => 1,
            'createdAt'         => '1488153462',
            'updatedAt'         => '1488153462',
            'createdBy'         => '1',
            'updatedBy'         => '1',
        ]);
        // end Home Entertainment

        // Home Appliances
        $this->insert('{{%products_family}}', [
            'parentId'          => null,
            'name'              => 'Home Appliances',
            'abbreviation_name' => 'HA',
            'status'            => 1,
            'createdAt'         => '1488153462',
            'updatedAt'         => '1488153462',
            'createdBy'         => '1',
            'updatedBy'         => '1',
        ]);

        $this->insert('{{%products_family}}', [
            'parentId'          => 2,
            'name'              => 'Refrigeradores',
            'abbreviation_name' => '',
            'status'            => 1,
            'createdAt'         => '1488153462',
            'updatedAt'         => '1488153462',
            'createdBy'         => '1',
            'updatedBy'         => '1',
        ]);

        $this->insert('{{%products_family}}', [
            'parentId'          => 2,
            'name'              => 'Lavadoras',
            'abbreviation_name' => '',
            'status'            => 1,
            'createdAt'         => '1488153462',
            'updatedAt'         => '1488153462',
            'createdBy'         => '1',
            'updatedBy'         => '1',
        ]);

        $this->insert('{{%products_family}}', [
            'parentId'          => 2,
            'name'              => 'Microondas',
            'abbreviation_name' => '',
            'status'            => 1,
            'createdAt'         => '1488153462',
            'updatedAt'         => '1488153462',
            'createdBy'         => '1',
            'updatedBy'         => '1',
        ]);

        //end Home Appliances

        //Mobile connect

        $this->insert('{{%products_family}}', [
            'parentId'          => null,
            'name'              => 'Mobile connect',
            'abbreviation_name' => 'MC',
            'status'            => 1,
            'createdAt'         => '1488153462',
            'updatedAt'         => '1488153462',
            'createdBy'         => '1',
            'updatedBy'         => '1',
        ]);

        $this->insert('{{%products_family}}', [
            'parentId'          => 3,
            'name'              => 'Smartphones',
            'abbreviation_name' => '',
            'status'            => 1,
            'createdAt'         => '1488153462',
            'updatedAt'         => '1488153462',
            'createdBy'         => '1',
            'updatedBy'         => '1',
        ]);
        // end Mobile connect

        // Monitors and Projectors
        $this->insert('{{%products_family}}', [
            'parentId'          => null,
            'name'              => 'Monitors and Projectors',
            'abbreviation_name' => 'MP',
            'status'            => 1,
            'createdAt'         => '1488153462',
            'updatedAt'         => '1488153462',
            'createdBy'         => '1',
            'updatedBy'         => '1',
        ]);

        $this->insert('{{%products_family}}', [
            'parentId'          => 4,
            'name'              => 'Monitores',
            'abbreviation_name' => '',
            'status'            => 1,
            'createdAt'         => '1488153462',
            'updatedAt'         => '1488153462',
            'createdBy'         => '1',
            'updatedBy'         => '1',
        ]);

        $this->insert('{{%products_family}}', [
            'parentId'          => 4,
            'name'              => 'Proyectores',
            'abbreviation_name' => '',
            'status'            => 1,
            'createdAt'         => '1488153462',
            'updatedAt'         => '1488153462',
            'createdBy'         => '1',
            'updatedBy'         => '1',
        ]);
        // end Monitors and Projectors

        // creates index for column `parentId`
        $this->createIndex(
            'idx-products_family-parentId',
            'products_family',
            'parentId'
        );

        // add foreign key for table `parentId`
        $this->addForeignKey(
            'fk-products_family-parentId',
            'products_family',
            'parentId',
            'products_family',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropTable('{{%products_family}}');
    }
}
