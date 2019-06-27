<?php

namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;

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
    const POSITIVE = 1;
    const NEGATIVE = 2;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dictionary';
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class'      => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['createdAt', 'updatedAt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updatedAt'],
                ],
            ],
            [
                'class'              => BlameableBehavior::className(),
                'createdByAttribute' => 'createdBy',
                'updatedByAttribute' => 'updatedBy',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['alertId', 'category_dictionaryId', 'createdAt', 'updatedAt', 'createdBy', 'updatedBy'], 'integer'],
            [['word'], 'string', 'max' => 255],
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

   
}
