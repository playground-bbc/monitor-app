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
            //[['url'],'url','defaultScheme' => 'https://'],
            [['url'],'url'],
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

    public static function get_domain($url)
    {
        $urlobj = parse_url($url);
        if(isset($urlobj['host'])){
            $domain = $urlobj['host'];
            if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)){
                return strtok($regs['domain'], '.');
            }
        }

        return false;
    }

    public static function isWebResource($resource)
    {
        $resource = Resource::find()->where(['name' => $resource,'typeResourceId' => self::TYPE_WEB])->exists();
        return $resource;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTypeResource()
    {
        return $this->hasOne(TypeResource::className(), ['id' => 'typeResourceId']);
    }
}
