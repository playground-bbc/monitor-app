<?php
namespace app\modules\monitor\controllers;


use yii;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;


use app\models\Alerts;
use app\models\Products;
use app\models\SearchForm;
use app\models\Dictionary;
use app\models\ProductsFamily;
use app\models\ProductsModels;
use app\models\api\LiveChatApi;
use app\models\ProductCategory;
use app\models\ProductsModelsAlerts;
use app\models\CategoriesDictionary;

/**
 * Default controller for the `monitor` module
 */
class LiveChatController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionCreate()
    {
        $form_model = new SearchForm();
        $alert_model = new Alerts();
        
        //////test ///////////////
        $save_testing_only = true;
        ///////////////////////////

        $form_model->scenario = 'live-chat';
        $form_model->name = $form_model->scenario."_".uniqid(); 

        if ($form_model->load(Yii::$app->request->post())) {
            $alert_model->start_date = strtotime($form_model->start_date);
            $alert_model->end_date = strtotime($form_model->end_date);
            $alert_model->userId =  1; 
            $alert_model->name =  $form_model->name; 
            if ($alert_model->save($save_testing_only)) {
                $models_products = $this->getModelsProducts(Yii::$app->request->post('SearchForm')['products']);
                if (count($models_products)) {
                    foreach ($models_products as $key => $value) {
                        $products_alerts = new ProductsModelsAlerts();
                        $products_alerts->alertId = $alert_model->id;
                        $products_alerts->product_modelId = $key;
                        if ($products_alerts->validate()) {
                            $products_alerts->save($save_testing_only);
                        }else{
                            var_dump($products_alerts->errors);
                            die();
                        }
                    }
                }
                $files_positive_words = UploadedFile::getInstance($form_model, 'positive_words');
                $this->saveDictionary($files_positive_words,$alert_model->id,'Positive Words');
                $files_negative_words = UploadedFile::getInstance($form_model, 'negative_words');
                $this->saveDictionary($files_negative_words,$alert_model->id,'Negative Words');

                return $this->redirect(['view', 'alertId' => $alert_model->id]);
                
                
            }
            
            
        }

        return $this->render('create',['form_model' => $form_model]);

    }



    public function actionView($alertId)
    {
        $alert = Alerts::findOne($alertId);

        $products_model_alerts = ArrayHelper::map(ProductsModelsAlerts::find()->where(['alertId' => $alertId])->all(),'product_modelId','alertId');
        $models_products = ArrayHelper::map(ProductsModels::find()->where(['id' => array_keys($products_model_alerts)])->all(),'productId','serial_model');
        $products = ArrayHelper::map(Products::find()->where(['id' => array_keys($models_products)])->all(),'id','name');
        

        /*var_dump($products_model_alerts);
        var_dump($models_products);
        var_dump($products);
        var_dump(ArrayHelper::merge($models_products,$products));*/

        $params = [
            'alertId' => $alert->id,
            'date_from' => $alert->start_date,
            'date_to' => $alert->end_date,
            'page' => 1,
            'query' => $models_products
        ];

        $live_chat = new LiveChatApi();
        $tickets = $live_chat->loadParams($params)->getTickets();


        return $this->render('view');
    }


    public function actionConfig()
    {
        return $this->render('config/index');
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

    public function saveDictionary($document,$alertId,$type)
    {
        $categoryId = CategoriesDictionary::find()->where(['name' => $type])->select('id')->one();
        
        if ($document) {
            $file = fopen($document->tempName, "r");
            while($linea = fgets($file)) {
                if (feof($file)) break;
                $dictionary = new Dictionary();
                $dictionary->alertId = $alertId;
                $dictionary->category_dictionaryId = $categoryId->id;
                $dictionary->word = utf8_encode(fgets($file));
                if (!$dictionary->save()) {
                    var_dump($file);
                    die();
                }
            }
            fclose($file);
        }
    }


}
