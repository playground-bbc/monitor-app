<?php

use yii\db\Migration;

/**
 * Class m190531_234728_product_category
 */
class m190531_234728_product_category extends Migration
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

        $this->createTable('{{%product_category}}', [
            'id'        => $this->primaryKey(),
            'familyId'  => $this->integer(),
            'name'      => $this->string(),
            'status'    => $this->smallInteger(1)->defaultValue(1),
            'createdAt' => $this->integer(),
            'updatedAt' => $this->integer(),
            'createdBy' => $this->integer(),
            'updatedBy' => $this->integer(),

        ], $tableOptions);
        //1
        /*$this->insert('{{%product_category}}', [
            'familyId'  => 2,
            'name'      => 'HD',
            'status'    => 1,
            'createdAt' => '1488153462',
            'updatedAt' => '1488153462',
            'createdBy' => '1',
            'updatedBy' => '1',
        ]);*/


        // index
        // creates index for column `familyId`
        $this->createIndex(
            'idx-products_models-familyId',
            'product_category',
            'familyId'
        );

        // relation
        // add foreign key for table `familyId`
        $this->addForeignKey(
            'fk-products_models-familyId',
            'product_category',
            'familyId',
            'products_family',
            'id',
            'CASCADE',
            'CASCADE'
        );


    }


    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190608_233311_product_category cannot be reverted.\n";

        return false;
    }
}
