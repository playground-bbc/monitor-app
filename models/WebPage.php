<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "web_page".
 *
 * @property int $id
 * @property string $name
 * @property string $url_web_page
 * @property int $category_id
 *
 * @property Category $category
 */
class WebPage extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'web_page';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'url_web_page', 'category_id'], 'required'],
            [['category_id'], 'integer'],
            [['name'], 'string', 'max' => 100],
            [['url_web_page'], 'string', 'max' => 255],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['category_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'url_web_page' => 'Url Web Page',
            'category_id' => 'Category ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }
}
