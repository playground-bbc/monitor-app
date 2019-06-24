<?php

namespace app\modules\monitor\controllers;

use yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

use app\models\api\BaseApi;
use app\models\api\TwitterApi;
use app\models\api\LiveChatApi;

use app\models\Alerts;
use app\models\AlertResources;
use app\models\Products;
use app\models\SearchForm;
use app\models\Dictionary;
use app\models\ProductsFamily;
use app\models\ProductsModels;
use app\models\ProductCategory;
use app\models\api\DriveProductsApi;
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

    public function actionError($message,$id = '')
    {
        Yii::error('Upps error .. !!', __METHOD__);
        return $this->render('delete',['message' => $message,'id' =>$id]);
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
                if (!$this->setProductsModelsAlert($models_products,$alert->id)) {
                    return $this->redirect(['error', 'message' => Yii::t('app','Error save Products Models Products'),'id' => $alert->id]);
                }
                $this->setDictionariesAlert($form_alert,$alert->id);
                $this->setSocialResources($form_alert,$alert->id);

                // send to view
                $this->redirect(['view', 'alertId' => $alert->id]);
            }
            
        }
        

        return $this->render('create',['form_alert' => $form_alert]);
    }


    public function actionView($alertId)
    {
        $alert = Alerts::findOne($alertId);

        $products_models = [];
        // models products
        foreach (ProductsModelsAlerts::find()->where(['alertId' => $alertId])->with('productModel')->each() as $product) {
            // batch query with eager loading
            $products_models[] = $product->productModel->serial_model;
        }
        $words = [];
        // words
        foreach ($alert->dictionaries as $key => $value) {
            $words[$value->category->name][] = $value->word;
        }

        $start_date = $alert->start_date;
        $end_date = $alert->end_date;
        $resources = [];
        // resources
        foreach ($alert->alertResources as $alert => $alert_value) {
            foreach ($alert_value->resources as $key => $value) {
                $resources[] = $value->name;
            }
        }

        $params = [
            'words' => $words,
            'products_models' => $products_models,
            'resources' => $resources,
            'start_date' => $start_date,
            'end_date' => $end_date,
        ]; 

        $baseApi = new BaseApi($params);

        var_dump($baseApi);
        die();     

        return $this->render('view');
    }

    public function actionTwitter()
    {
        $twitterApi = new TwitterApi();

        if (!\Yii::$app->session->has('oauth_token_twitter')) {
              $twitterApi->authenticate();
        }elseif (isset($_GET['oauth_verifier']) && isset($_SESSION['oauth_verify'])) {
           $twitterApi->redirect_to_monitor();
        }

        return $this->redirect('create');
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

    public function setProductsModelsAlert($models_products,$alertId)
    {
       if (count($models_products)) {
            foreach ($models_products as $key => $value) {
                $products_alerts = new ProductsModelsAlerts();
                $products_alerts->alertId = $alertId;
                $products_alerts->product_modelId = $key;
                if ($products_alerts->validate()) {
                    $products_alerts->save();
                }else{
                    Yii::warning("problems when saving the models of the products in the alert with id: {$alertId}", __METHOD__);
                }
            }
        }
        return (count($models_products) && empty($products_alerts->errors)) ? true : false;
    }

    public function setDictionariesAlert($form_alert = [],$alertId)
    {
       $drive = new DriveProductsApi();
            
       if (ArrayHelper::isIndexed($form_alert->drive_dictionary)) {
           $models = [];
           foreach ($form_alert->drive_dictionary as $dictionary) {
                $categoryId = CategoriesDictionary::find()->where(['name' => $dictionary])->select('id')->one();
                $data = $drive->getContentDictionaryByTitle([$dictionary]);
               
                foreach ($data[$dictionary] as $key => $word) {
                    $models[] = [$alertId,$categoryId->id,$word];
                    
                 }
                
            } 
            // save words from drive 
            Yii::$app->db->createCommand()->batchInsert('dictionary', ['alertId','category_dictionaryId', 'word'],$models)
            ->execute();
       }

       $positive_words = ($form_alert->positive_words != '') ? explode(',', $form_alert->positive_words) : null;
       $negative_words = ($form_alert->negative_words != '') ? explode(',', $form_alert->negative_words) : null;

       $models = [];
       if (!is_null($positive_words)) {
           for ($i=0; $i <sizeof($positive_words) ; $i++) { 
               $models[] = [$alertId,1,$positive_words[$i]];
           }
       }

       if (!is_null($negative_words)) {
           for ($i=0; $i <sizeof($negative_words) ; $i++) { 
               $models[] = [$alertId,2,$negative_words[$i]];
           }
       }

       if (!empty($models)) {
            // save positives and negatives
           Yii::$app->db->createCommand()->batchInsert('dictionary', ['alertId','category_dictionaryId', 'word'],$models)
            ->execute();
       }

    }

    public function setSocialResources($form_alert = [],$alertId)
    {
        
        if (empty($form_alert->social_resources)) {
            Yii::warning("problems when saving the social resources in the alert with id: {$alertId}", __METHOD__);
            return $this->redirect(['error', 'message' => Yii::t('app','Error save Products Models Products'),'id' => $alert->id]);      
        }

        for ($i=0; $i <sizeof($form_alert->social_resources) ; $i++) { 
            $model = new AlertResources();
            $model->idResources = $form_alert->social_resources[$i];
            $model->idAlert = $alertId;
            $model->save();
        }

    }

}
