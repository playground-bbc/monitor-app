<?php

use yii\db\Migration;

/**
 * Class m190601_223716_categories_dictionary
 */
class m190601_223716_categories_dictionary extends Migration
{
    /**
     * {@inheritdoc}
     */

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%categories_dictionary}}', [
            'id'        => $this->primaryKey(),
            'name'      => $this->string(),
            'createdAt' => $this->integer(),
            'updatedAt' => $this->integer(),
            'createdBy' => $this->integer(),
            'updatedBy' => $this->integer(),

        ], $tableOptions);

        $this->insert('{{%categories_dictionary}}', [
            'name'      => 'Buenas',
            'updatedAt' => '1488153462',
            'createdBy' => '1',
            'updatedBy' => '1',
        ]);

        $this->insert('{{%categories_dictionary}}', [
            'name'      => 'Palabras Libres',
            'updatedAt' => '1488153462',
            'createdBy' => '1',
            'updatedBy' => '1',
        ]);

        $this->insert('{{%categories_dictionary}}', [
            'name'      => 'Malas',
            'createdAt' => '1488153462',
            'updatedAt' => '1488153462',
            'createdBy' => '1',
            'updatedBy' => '1',
        ]);

    }

    public function down()
    {
        echo "m190601_223716_categories_dictionary cannot be reverted.\n";

        return false;
    }
    /*
 */
}
