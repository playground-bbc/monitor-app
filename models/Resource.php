<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "resource".
 *
 * @property int $id
 * @property string $name
 * @property string $url
 * @property int $typeResourceId
 * @property int $status
 * @property int $createdAt
 * @property int $updatedAt
 * @property int $createdBy
 * @property int $updatedBy
 *
 * @property TypeResource $typeResource
 */
class Resource extends \yii\db\ActiveRecord
{

    const TYPE_WEB = 1;
    const TYPE_SOCIAL = 2;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'resource';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['typeResourceId'], 'required'],
            [['typeResourceId', 'status', 'createdAt', 'updatedAt', 'createdBy', 'updatedBy'], 'integer'],
            [['name', 'url'], 'string', 'max' => 255],
            [['url'],'url','defaultScheme' => 'https://'],
            [['typeResourceId'], 'exist', 'skipOnError' => true, 'targetClass' => TypeResource::className(), 'targetAttribute' => ['typeResourceId' => 'id']],
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
            'url' => 'Url',
            'typeResourceId' => 'Type Resource ID',
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
    public function getTypeResource()
    {
        return $this->hasOne(TypeResource::className(), ['id' => 'typeResourceId']);
    }
}
