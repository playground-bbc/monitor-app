<?php

use yii\db\Migration;

/**
 * Class m190531_234725_products_categories
 */
class m190531_234725_products_categories extends Migration
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

        $this->createTable('{{%products_categories}}', [
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

        $this->insert('{{%products_categories}}', [
            'parentId'          => null,
            'name'              => 'Home Entertainment',
            'abbreviation_name' => 'HE',
            'status'            => 1,
            'createdAt'         => '1488153462',
            'updatedAt'         => '1488153462',
            'createdBy'         => '1',
            'updatedBy'         => '1',
        ]);

        $this->insert('{{%products_categories}}', [
            'parentId'          => 1,
            'name'              => 'Televisores',
            'abbreviation_name' => 'TV',
            'status'            => 1,
            'createdAt'         => '1488153462',
            'updatedAt'         => '1488153462',
            'createdBy'         => '1',
            'updatedBy'         => '1',
        ]);

        $this->insert('{{%products_categories}}', [
            'parentId'          => 1,
            'name'              => 'Audio',
            'abbreviation_name' => '',
            'status'            => 1,
            'createdAt'         => '1488153462',
            'updatedAt'         => '1488153462',
            'createdBy'         => '1',
            'updatedBy'         => '1',
        ]);

        // creates index for column `parentId`
        $this->createIndex(
            'idx-products_categories-parentId',
            'products_categories',
            'parentId'
        );

        // add foreign key for table `parentId`
        $this->addForeignKey(
            'fk-products_categories-parentId',
            'products_categories',
            'parentId',
            'products_categories',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropTable('{{%products_categories}}');
    }
}
