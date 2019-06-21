<?php

namespace app\modules\monitor\controllers;

use yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

use app\models\api\LiveChatApi;

use app\models\Alerts;
use app\models\Products;
use app\models\SearchForm;
use app\models\Dictionary;
use app\models\ProductsFamily;
use app\models\ProductsModels;
use app\models\ProductCategory;
use app\models\ProductsModelsAlerts;
use app\models\CategoriesDictionary;
use app\models\search\ProductsModelsAlertsSearch;

class AlertController extends \yii\web\Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['create'],
                'rules' => [
                    [
                        'actions' => ['create'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['POST'],
                    'index' => ['GET', 'POST'],
                    
                ],
            ],
        ];
    }
   
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionDelete()
    {
        Yii::warning('start warning !!', __METHOD__);
      //  Yii::error('start error..');
        return $this->render('delete');
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionCreate()
    {
        $alert = new Alerts();
        $form_alert = new SearchForm();

        $form_alert->name = 'alert_'.uniqid();
        $form_alert->scenario = 'alert';

        if ($form_alert->load(Yii::$app->request->post()) && $alert->load(Yii::$app->request->post(),'SearchForm')) {
            $alert->start_date = strtotime($form_alert->start_date);
            $alert->end_date = strtotime($form_alert->end_date);
            $alert->userId = \Yii::$app->user->identity->id;
            
            if ($alert->save()) {
                $models_products = $this->getModelsProducts(Yii::$app->request->post('SearchForm')['products']);
                if (count($models_products)) {
                    foreach ($models_products as $key => $value) {
                        $products_alerts = new ProductsModelsAlerts();
                        $products_alerts->alertId = $alert->id;
                        $products_alerts->product_modelId = $key;
                        if ($products_alerts->validate()) {
                            $products_alerts->save();
                        }else{
                            var_dump($products_alerts->errors);
                            die();
                        }
                    }
                }
            }
            
        }
        

        return $this->render('create',['form_alert' => $form_alert]);
    }

    public function actionUpdate()
    {
        return $this->render('update');
    }

    public function actionView()
    {
        return $this->render('view');
    }


    public function getModelsProducts($products=[])
    {   
        $models_products = [];
        for ($i=0; $i <sizeof($products) ; $i++) { 
            $familyId = ProductsFamily::findOne(['name' => $products[$i]]);
            if ($familyId) {
                $categoriesId = ArrayHelper::map($familyId->getCategories()->all(),'id','familyId');
                $productsId = ArrayHelper::map(Products::find()->where(['categoryId' => array_keys($categoriesId)])->all(),'id','categoryId');
                $modelsId  =  ArrayHelper::map(ProductsModels::find()->where(['productId' => array_keys($productsId)])->all(),'id','serial_model');  
            }

            $categoryId = ArrayHelper::map(ProductCategory::find()->where(['name' => $products[$i]])->all(),'id','familyId');
            if ($categoryId) {
                $productsId = ArrayHelper::map(Products::find()->where(['categoryId' => array_keys($categoryId)])->all(),'id','categoryId');
                $modelsId  =  ArrayHelper::map(ProductsModels::find()->where(['productId' => array_keys($productsId)])->all(),'id','serial_model');
            }

            $models  =  ArrayHelper::map(ProductsModels::find()->where(['serial_model' => $products[$i]])->all(),'id','serial_model');
            if ($models) {
                $modelsId = $models;
            }

            array_push($models_products, $modelsId);
        }

        $temp = [];
        for ($i=0; $i <sizeof($models_products) ; $i++) { 
            foreach ($models_products[$i] as $key => $value) {
                if (!in_array($key, $temp)) {
                    $temp[$key] = $value;
                }
            }
        }
        $models_products = $temp;
        
        return $models_products;
    }

}
