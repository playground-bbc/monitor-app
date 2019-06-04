<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "products_categories".
 *
 * @property int $id
 * @property int $parentId
 * @property string $name
 * @property string $abbreviation_name
 * @property int $status
 * @property int $createdAt
 * @property int $updatedAt
 * @property int $createdBy
 * @property int $updatedBy
 *
 * @property Products[] $products
 * @property ProductsCategories $parent
 * @property ProductsCategories[] $productsCategories
 */
class ProductsCategories extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'products_categories';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['parentId', 'status', 'createdAt', 'updatedAt', 'createdBy', 'updatedBy'], 'integer'],
            [['name', 'abbreviation_name'], 'string', 'max' => 255],
            [['parentId'], 'exist', 'skipOnError' => true, 'targetClass' => ProductsCategories::className(), 'targetAttribute' => ['parentId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parentId' => 'Parent ID',
            'name' => 'Name',
            'abbreviation_name' => 'Abbreviation Name',
            'status' => 'Status',
            'createdAt' => 'Created At',
            'updatedAt' => 'Updated At',
            'createdBy' => 'Created By',
            'updatedBy' => 'Updated By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(Products::className(), ['categoryId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(ProductsCategories::className(), ['id' => 'parentId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductsCategories()
    {
        return $this->hasMany(ProductsCategories::className(), ['parentId' => 'id']);
    }
}
