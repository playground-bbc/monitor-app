<?php

use yii\db\Migration;

/**
 * Class m190521_144417_TypeResource
 */
class m190521_144417_type_resource extends Migration
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

        $this->createTable('{{%type_resource}}',[
            'id'        => $this->primaryKey(),
            'name'      => $this->string(),
            'createdAt' => $this->integer(),
            'updatedAt' => $this->integer(),
            'createdBy' => $this->integer(),
            'updatedBy' => $this->integer(),

        ],$tableOptions);

        $this->insert('{{%type_resource}}', [
            'name' => 'Blog Page',
            'createdAt'=> '1488153462',
            'updatedAt'=> '1488153462',
            'createdBy'=> '1',
            'updatedBy'=> '1',
        ]);

        $this->insert('{{%type_resource}}', [
            'name' => 'Social Media',
            'createdAt'=> '1488153462',
            'updatedAt'=> '1488153462',
            'createdBy'=> '1',
            'updatedBy'=> '1',
        ]);

    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable('{{%type_resource}}');
    }
}
