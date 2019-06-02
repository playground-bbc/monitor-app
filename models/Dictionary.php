<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "dictionary".
 *
 * @property int $id
 * @property int $alertId
 * @property int $category_dictionaryId
 * @property string $name
 * @property string $word
 * @property int $createdAt
 * @property int $updatedAt
 * @property int $createdBy
 * @property int $updatedBy
 *
 * @property Alerts $alert
 * @property CategoriesDictionary $categoryDictionary
 */
class Dictionary extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dictionary';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['alertId', 'category_dictionaryId', 'createdAt', 'updatedAt', 'createdBy', 'updatedBy'], 'integer'],
            [['name', 'word'], 'string', 'max' => 255],
            [['alertId'], 'exist', 'skipOnError' => true, 'targetClass' => Alerts::className(), 'targetAttribute' => ['alertId' => 'id']],
            [['category_dictionaryId'], 'exist', 'skipOnError' => true, 'targetClass' => CategoriesDictionary::className(), 'targetAttribute' => ['category_dictionaryId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'alertId' => 'Alert ID',
            'category_dictionaryId' => 'Category Dictionary ID',
            'name' => 'Name',
            'word' => 'Word',
            'createdAt' => 'Created At',
            'updatedAt' => 'Updated At',
            'createdBy' => 'Created By',
            'updatedBy' => 'Updated By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAlert()
    {
        return $this->hasOne(Alerts::className(), ['id' => 'alertId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(CategoriesDictionary::className(), ['id' => 'category_dictionaryId']);
    }


    public function getOrderedWords()
    {
        $result =[];
        $words = $this->find()->with('category')->asArray()->all();
        for ($i=0; $i <sizeof($words) ; $i++) { 
            $result[$words[$i]['category']['name']][] = $words[$i]['name'] ;
        }
        return $result;
    }
}
