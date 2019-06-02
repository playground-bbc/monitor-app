<?php

use yii\db\Migration;

/**
 * Class m190601_225331_dictionary
 */
class m190601_225331_dictionary extends Migration
{
    /**
     * {@inheritdoc}
     */

    // Use up()/down() to run migration code without a transaction.
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%dictionary}}', [
            'id'                    => $this->primaryKey(),
            'alertId'               => $this->integer(),
            'category_dictionaryId' => $this->integer(),
            'name'                  => $this->string(),
            'word'                  => $this->string(),
            'createdAt'             => $this->integer(),
            'updatedAt'             => $this->integer(),
            'createdBy'             => $this->integer(),
            'updatedBy'             => $this->integer(),

        ], $tableOptions);

        $this->insert('{{%dictionary}}', [
            'alertId'               => 1,
            'category_dictionaryId' => 1,
            'name'                  => 'bueno',
            'word'                  => '',
            'createdAt'             => '1488153462',
            'updatedAt'             => '1488153462',
            'createdBy'             => '1',
            'updatedBy'             => '1',
        ]);

        $this->insert('{{%dictionary}}', [
            'alertId'               => 1,
            'category_dictionaryId' => 2,
            'name'                  => 'malo',
            'word'                  => '',
            'createdAt'             => '1488153462',
            'updatedAt'             => '1488153462',
            'createdBy'             => '1',
            'updatedBy'             => '1',
        ]);

        $this->insert('{{%dictionary}}', [
            'alertId'               => 1,
            'category_dictionaryId' => 2,
            'name'                  => 'defecto',
            'createdAt'             => '1488153462',
            'updatedAt'             => '1488153462',
            'createdBy'             => '1',
            'updatedBy'             => '1',
        ]);

        $this->insert('{{%dictionary}}', [
            'alertId'               => 1,
            'category_dictionaryId' => 2,
            'name'                  => 'tengo un problema',
            'createdAt'             => '1488153462',
            'updatedAt'             => '1488153462',
            'createdBy'             => '1',
            'updatedBy'             => '1',
        ]);

        // creates index for column `categoryId`
        $this->createIndex(
            'idx-dictionary-alertId',
            'dictionary',
            'alertId'
        );

        // add foreign key for table `category`
        $this->addForeignKey(
            'fk-dictionary-alertId',
            'dictionary',
            'alertId',
            'alerts',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // creates index for column `categoryId`
        $this->createIndex(
            'idx-dictionary-category',
            'dictionary',
            'category_dictionaryId'
        );

        // add foreign key for table `category`
        $this->addForeignKey(
            'fk-dictionary-category',
            'dictionary',
            'category_dictionaryId',
            'categories_dictionary',
            'id',
            'CASCADE',
            'CASCADE'
        );

    }

    public function down()
    {
        $this->dropTable('{{%dictionary}}');
    }

    /*
 */
}
