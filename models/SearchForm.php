<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

use app\models\Resource;
use app\models\TypeResource;
use app\models\ProductsFamily;
use app\models\ProductsModels;
use app\models\ProductCategory;


use app\models\api\DriveProductsApi;
/**
 * SearchForm is the model behind the search form.
 */
class SearchForm extends Model
{

    const TYPE_SOCIAL = 'Social Media';

    public $name;
    public $keywords = [];
    public $products = [];
    public $web_resource;
    
    public $negative_words;
    public $positive_words;
    public $drive_dictionary =[];

    public $social_resources;

    public $query_search;
    public $categories_dictionary = [];
   
   
    public $start_date;
    public $end_date;
    public $text_search;

    //alert
    public $is_dictionary;
    public $awario_file;
    // only to lookup file
    public $path;



    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // scraping
            [['name','products','web_resource'], 'required', 'on' => 'scraping'],
            

            // alert
            [['name','products','start_date','end_date'], 'required', 'on' => 'alert'],
            // awario validator
            [['awario_file'], 'file','skipOnEmpty' => false,'extensions' => 'csv','maxSize' => 20000000, 'on' => 'alert'], // see php ini upload_max_filesize and post_max_size values 
			// date validator
			['start_date','compare','compareAttribute'=>'end_date','operator'=>'<=','on' => 'alert'],
            ['end_date','compare','compareAttribute'=>'start_date','operator'=>'>=','on' => 'alert'],
            [['start_date','end_date'], 'date','format' => 'mm/dd/yyyy','on' => 'alert'],
            
             ['social_resources', function ($attribute, $params, $validator) {
                if (!ctype_alnum($this->$attribute)) {
                    $this->addError($attribute, 'The token must contain letters or digits.');
                }
            }]
        ];
    }
    
    public function validateDates($attribute, $params, $validator){
		if (!in_array($this->$attribute, ['USA', 'Indonesia'])) {
            $this->addError($attribute, 'The country must be either "USA" or "Indonesia".');
        }
	}

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['alert'] = ['name','web_resource','social_resources','products','drive_dictionary','positive_words','start_date','end_date'];
        $scenarios['live-chat'] = ['products','positive_words','negative_words','start_date','end_date'];
        return $scenarios;
    }

    public function getProducts()
    {
        $family['Products Family'] = ArrayHelper::map(ProductsFamily::find()->andFilterCompare('parentId','null','<>')->all(),'name','name');
        $categories['Product Category'] = ArrayHelper::map(ProductCategory::find()->andFilterCompare('familyId','null','<>')->all(),'name','name');
        $products['Product'] = ArrayHelper::map(Products::find()->andFilterCompare('categoryId','null','<>')->all(),'name','name');
        $products_models['Product Models'] = ArrayHelper::map(ProductsModels::find()->andFilterCompare('productId','null','<>')->all(),'serial_model','serial_model');

        $data = ArrayHelper::merge($family,$categories);
        $data = ArrayHelper::merge($products,$data);
        $data = ArrayHelper::merge($products_models,$data);
        return $data;

    }

    public function getDictionaryNameOnDrive()
    {
        $drive = new DriveProductsApi();
        
        return $drive->titleDictionary;
    }

    public function getSocialResources()
    {
       $type = TypeResource::findOne(['name'=> self::TYPE_SOCIAL]);
       $model = Resource::find()->where(['typeResourceId' => $type->id])->all();
       return ($model) ? ArrayHelper::map($model,'id','name') : []; 
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'text_search' => 'Search text',
            'start_date' => 'start date',
            'start_end' => 'start end',
            'keywords' => 'keywords',
            'products' => 'Productos - Model - Code',
            'categories_dictionary' => 'categories dictionary',
            'positive_words' => 'free words',
           // 'negative_words' => 'negative words',
            'is_dictionary' => 'Add Dictionary',
            'awario_file' => 'Add Awario File',
        ];
    }



}
