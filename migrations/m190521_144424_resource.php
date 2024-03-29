<?php

use yii\db\Migration;

/**
 * Class m190521_144424_resource
 */
class m190521_144424_resource extends Migration
{   
    /*
     * {@inheritdoc}
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%resource}}',[
            'id'        => $this->primaryKey(),
            'name'      => $this->string(),
            'url'       => $this->string(),
            'typeResourceId' => $this->integer()->notNull(),
            'status'    => $this->smallInteger(1)->defaultValue(1),
            'createdAt' => $this->integer(),
            'updatedAt' => $this->integer(),
            'createdBy' => $this->integer(),
            'updatedBy' => $this->integer(),

        ],$tableOptions);

        $this->insert('{{%resource}}', [
            'name' => 'hipertextual',
            'url' => 'https://hipertextual.com',
            'typeResourceId'  => 1,
            'status'  => 1,
            'createdAt'=> '1488153462',
            'updatedAt'=> '1488153462',
            'createdBy'=> '1',
            'updatedBy'=> '1',
        ]);

        // creates index for column `typeResourceId`
        $this->createIndex(
            'idx-type-resourceId',
            'resource', // nombre de la tabla relacionada
            'typeResourceId'
        );

        // add foreign key for table `resource`
        $this->addForeignKey(
            'fk-type-resourceId',
            'resource', 
            'typeResourceId',
            'type_resource',
            'id',
            'CASCADE',
            'CASCADE'
        );

    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable('{{%resource}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190521_144424_resource cannot be reverted.\n";

        return false;
    }
    */
}
