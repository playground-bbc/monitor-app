<?php

namespace app\modules\monitor\controllers;

use yii;
use  yii\web\Session;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;
use yii\data\ArrayDataProvider;

use yii2tech\spreadsheet\Spreadsheet;
use Stringizer\Stringizer;

use app\models\api\BaseApi;
use app\models\api\TwitterApi;
use app\models\api\LiveChatApi;

use app\models\filebase\Filebase;

use app\models\scraping\Crawler;

use app\models\chart\CountByCategory;
use app\models\chart\CountWords;
use app\models\chart\CountTicket;
use app\models\chart\CountByCategoryAwario;

use app\models\Alerts;
use app\models\Products;
use app\models\Resource;
use app\models\SearchForm;
use app\models\Dictionary;
use app\models\ProductsFamily;
use app\models\ProductsModels;
use app\models\AlertResources;
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
    /**
     * @return [array]
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }
    /**
     * @param  string
     * @param  string
     * @return view
     */
    public function actionError($message,$id = '')
    {
        Yii::error('Upps error .. !!', __METHOD__);
        return $this->render('delete',['message' => $message,'id' =>$id]);
    }
    /**
     * @return view
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
    /**
     * @return view
     */
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
                $this->setAwarioFile($form_alert,$alert->name);

                // send to view
                //return $this->redirect(['view', 'alertId' => $alert->id]);
                
                return $this->redirect(['show', 'alertId' => $alert->id]);
            }
            
        }
        

        return $this->render('create',['form_alert' => $form_alert]);
    }

    /**
     * @param  int
     * @return view
     */
    public function actionView($alertId)
    {
      $alert = Alerts::findOne($alertId);
      $nameAlert = $alert->name;

     //   $this->syncDictionaryByAlertId($alertId);
        
        $products_models = [];
        // models products
        /*foreach (ProductsModelsAlerts::find()->where(['alertId' => $alertId])->with('productModel')->each() as $product) {
            // batch query with eager loading
            $products_models[$product->productModel->product->category->name][$product->productModel->product->name] = $product->productModel->serial_model;
        }*/
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
            'alertId' => $nameAlert,
            'words' => $words,
            //'products_models' => $products_models,
            'resources' => $resources,
            /*'start_date' => $start_date,
            'end_date' => $end_date,*/
        ]; 

        $baseApi = new BaseApi($params);
        $color = $baseApi::COLOR;
        
        //$model = $baseApi->countAndSearchWords();

        $cache = Yii::$app->cache; // Could be Yii::$app->cache
        $model =  $cache->getOrSet($alertId, function () use ($baseApi) {
            return $baseApi->countAndSearchWords();;
        }, 1000);


        return $this->render('view',[
            'model' => $model,
            'color' => $color

        ]);
    }


    public function actionShow($alertId)
    {
      $alert = Alerts::findOne($alertId);
      $nameAlert = $alert->name;
      $start_date = $alert->start_date;
      $end_date = $alert->end_date;

      $words = [];
      // words
      foreach ($alert->dictionaries as $key => $value) {
          $words[$value->category->name][] = $value->word;
      }

      $start_date = $alert->start_date;
      $end_date = $alert->end_date;
      

      $resource = [];

      foreach ($alert->alertResources as $alert => $resources) {
          for ($i=0; $i <sizeof($resources->resources) ; $i++) { 
              if($resources->resources[$i]->typeResourceId == 1){
                $resource ['web'][] = $resources->resources[$i]->name;
              }elseif($resources->resources[$i]->typeResourceId == 2){
                $resource ['social'][] = $resources->resources[$i]->name;
              }
          }
      }

      $products_models = [];
        // models products
        foreach (ProductsModelsAlerts::find()->where(['alertId' => $alertId])->with('productModel')->each() as $product) {
            // batch query with eager loading
            $products_models[$product->productModel->product->category->name][$product->productModel->product->name][] = $product->productModel->serial_model;
        }

      $social = [
          'alertId' => $nameAlert,
          'words' => $words,
          'resources' => $resource['social'],
          'products_models' => $products_models,
          'start_date' => $start_date,
          'end_date' => $end_date,
      ]; 

      $web = [
        'alertId' => $nameAlert,
        'words' => $words,
        'resources' => (isset($resource['web'])) ? $resource['web'] : [],
        'products_models' => $products_models,
      ];


      $baseApi  = new BaseApi($social);
      $crawling = new Crawler($web); 
      //$crawling->callCrawling();
      // $baseApi->callApiResources();
      
      

      $cache = Yii::$app->cache;
      $model =  $cache->getOrSet($alertId, function () use ($baseApi,$crawling) {
          $model_api = $baseApi->countAndSearchWords();
          $model_web = $crawling->countAndSearchWords();
          return ArrayHelper::merge($model_api,$model_web);
      }, 1000);



	    //$cache->delete($alertId);	
      
      
      
      $chartCategories = new CountByCategory($model);
      $chartWords      = new CountWords($model);
      $chartLive       = new CountTicket($model);
      $chartAwario     = new CountByCategoryAwario($model);
      
      $alert = Alerts::findOne($alertId);




      return $this->render('show',[
        'alert' => $alert,
        'model' => $model,
        'chartCategories' => $chartCategories,
        'chartWords' => $chartWords,
        'chartLive' => $chartLive,
        'chartAwario' => $chartAwario,
      ]);
      
    }

    

    /**
     * @return view
     */
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

    /**
     * @param  array
     * @return [array]
     */
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

            $productsId = ArrayHelper::map(Products::find()->where(['name' => $products[$i]])->all(),'id','categoryId');
            if ($productsId) {
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



    /**
     * @param array
     * @param int
     */
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


    public function actionInsertProduct()
    {
      if (Yii::$app->request->isAjax) {
        $data = \Yii::$app->request->post();
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $product = $data['product_name'];

        $models_products = $this->getModelsProducts([$product]);

        $products_models = [];
        foreach (ProductsModels::find()->where(['serial_model' => $models_products])->with('product')->each() as $model) {
            // batch query with eager loading
            $products_models[$model->product->category->name][$model->product->name][] = $model->serial_model;
        }

        $social = [2 => 'Twitter', 3 => 'Live Chat',15 => 'Live Chat Conversations'];
        $res = [];
        foreach ($data['resource'] as $key => $resource) {
            if(array_key_exists($resource,$social)){
             $res[] = $social[$resource]; 
            }
        }
        
        
        $params = [
            'alertId' => $data['alert_name'],
            'products_models' => $products_models,
            'resources' => $res,
            'start_date' => $data['start_date'],
            'end_date' => $data['start_end'],
        ];

        $baseApi = new BaseApi($params);
        $baseApi->callApiResources();

        return [
            'data' => [
                'message' => 'some',
            ],
            'code' => 0,
        ];

      }
    }
    
    public function actionDeleteProduct(){
		
  		if (Yii::$app->request->isAjax){
  			$data = \Yii::$app->request->post();
  			\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
  			return [
  			  'data' => [
  				  'message' => $data['product_name'],
  				  'success' => true
  			  ],
  			  'code' => 0,
  		  ];
  		}
	
	  }
    /**
     * @param array
     * @param int
     */
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

       $neutral_words = ($form_alert->positive_words != '') ? explode(',', $form_alert->positive_words) : null;

       $models = [];
       if (!is_null($neutral_words)) {
           for ($i=0; $i <sizeof($neutral_words) ; $i++) { 
               $models[] = [$alertId,2,$neutral_words[$i]];
           }
       }

       if (!empty($models)) {
            // save positives and negatives
           Yii::$app->db->createCommand()->batchInsert('dictionary', ['alertId','category_dictionaryId', 'word'],$models)
            ->execute();
       }

    }

    public function syncDictionaryByAlertId($alertId)
    {
        $drive = new DriveProductsApi();
        
        $dictionaries_title = $drive->titleDictionary;

        foreach ($dictionaries_title as $dictionary) {
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
    /**
     * @param array
     * @param int
     */
    public function setSocialResources($form_alert = [],$alertId)
    {


        if (empty($form_alert->social_resources)) {
            Yii::warning("problems when saving the social resources in the alert with id: {$alertId}", __METHOD__);
            return $this->redirect(['error', 'message' => Yii::t('app','Error save Products Models Products'),'id' => $alert->id]);      
        }

        if (!empty($form_alert->web_resource)) {
          $web_resources = explode(',', $form_alert->web_resource);

          foreach ($web_resources as $web_resource) {
            $name_web = Resource::get_domain($web_resource);
            
            if (!Resource::find()->where(['name' => $name_web,'url' => $web_resource ])->exists()) {
              $model_resource = new Resource();
              // asign
              $model_resource->name = $name_web;
              $model_resource->url = $web_resource;
              $model_resource->typeResourceId = Resource::TYPE_WEB;
              if ($model_resource->save()) {
                array_push($form_alert->social_resources, $model_resource->id);
              }
            }else{
              $model_resource = Resource::find()->where(['name' => $name_web,'url' => $web_resource])->select('id')->one();
              array_push($form_alert->social_resources, $model_resource->id);
            }
          }
        }

        for ($i=0; $i <sizeof($form_alert->social_resources) ; $i++) { 
            $model = new AlertResources();
            $model->idResources = $form_alert->social_resources[$i];
            $model->idAlert = $alertId;
            $model->save();
        }

    }
    /**
     * @param array
     * @param id
     */
    public function setAwarioFile($form_alert = [],$alertId)
    {
        if (UploadedFile::getInstance($form_alert, 'awario_file')) {
            $file = UploadedFile::getInstance($form_alert, 'awario_file');
            $resourceName = 'awario';

            $folderOptions = [
                'name' => $alertId,
                'path' => '@monitor',
                'resource' => $resourceName,
            ];
            $path = $this->setFolderPath($folderOptions);
            $fileName = $alertId . '.' . $file->extension;
            if (!$file->saveAs($path . $fileName)) {
              var_dump("expression");
              die();
                Yii::warning("problems when saving the awario file in the alert with id: {$alertId}", __METHOD__);
            }
            return $path;
        }
        return false;
    }


    /**
     * @return $path string or boolean
     * create or return the  valid path for save images
     */
    public function setFolderPath($folderOptions)
    {
        // path to folder flat archives
        $s = DIRECTORY_SEPARATOR;

        $path = \Yii::getAlias($folderOptions['path'])."{$s}".$folderOptions['resource']."{$s}". $folderOptions['name']. "{$s}";
        
        
        if (!is_dir($path)) {
           $folder = FileHelper::createDirectory($path, $mode = 0775,$recursive = true);
           
           return ($folder) ? $path : false; 
        }

        return $path;
    }

    public function setDataTwitterWords($models)
    {
        $countWord = [];
        if (ArrayHelper::keyExists('tweets', $models, false)) {
            $tweets = ArrayHelper::getValue($models,'tweets');
            if (ArrayHelper::keyExists('countWords', $tweets, false)) {
                $countWord = ArrayHelper::getValue($tweets,'countWords');
            }
        }
        return (!empty($countWord)) ? $countWord : false;
    }


    public function actionExcelAwario($alertId,$resource_name)
    {
      $alert = Alerts::findOne($alertId);
      $nameAlert = $alert->name;
      $start_date = $alert->start_date;
      $end_date = $alert->end_date;

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
      $products_models = [];
        // models products
        foreach (ProductsModelsAlerts::find()->where(['alertId' => $alertId])->with('productModel')->each() as $product) {
            // batch query with eager loading
            $products_models[$product->productModel->product->category->name][$product->productModel->product->name][] = $product->productModel->serial_model;
        }

      $params = [
          'alertId' => $nameAlert,
          'words' => $words,
          'resources' => $resources,
          'products_models' => $products_models,
          'start_date' => $start_date,
          'end_date' => $end_date,
      ]; 

      

      $baseApi = new BaseApi($params);
      $crawling = new Crawler($params); 
      // $baseApi->callApiResources();
      
      

      $cache = Yii::$app->cache;

      $model =  $cache->getOrSet($alertId, function () use ($baseApi) {
          return $baseApi->countAndSearchWords();
      }, 1000);

      

        $exporter = new Spreadsheet([
        'dataProvider' => new ArrayDataProvider([
            'allModels' => $model['awario']['sentence_awario']
        ]),
          'columns' => [
              [
                  'attribute' => 'source',
                  'contentOptions' => [
                      'alignment' => [
                          'horizontal' => 'center',
                          'vertical' => 'center',
                      ],
                  ],
              ],
              [
                  'attribute' => 'url',
              ],
              [
                  'attribute' => 'created_at',
              ],
              [
                  'label' => 'author_name',
                  'value' => function($model) {
                    return preg_replace('/[[:^print:]]/', '', $model['author_name']);
                  },

              ],
              [
                  'label' => 'title',
                  'value' => function($model) {
                    return preg_replace('/[[:^print:]]/', '', $model['title']);
                  },

              ],
              [
                  'label' => 'post_from',
                  'value' => function($model) {
                    if(ArrayHelper::keyExists('post_from_orign',$model)){
                      $sentences = preg_replace('/[[:^print:]]/', '', $model['post_from_orign']);
                    }else{
                      $sentences = preg_replace('/[[:^print:]]/', '', $model['post_from']);
                    }

                    return $sentences;
                  },

              ],
              
          ],
      ]);
      return $exporter->send($resource_name.".xls");

    }

    public function actionExcelTweets($alertId,$resource_name)
    {
      $alert = Alerts::findOne($alertId);
      $nameAlert = $alert->name;
      $start_date = $alert->start_date;
      $end_date = $alert->end_date;

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
      $products_models = [];
        // models products
        foreach (ProductsModelsAlerts::find()->where(['alertId' => $alertId])->with('productModel')->each() as $product) {
            // batch query with eager loading
            $products_models[$product->productModel->product->category->name][$product->productModel->product->name][] = $product->productModel->serial_model;
        }

      $params = [
          'alertId' => $nameAlert,
          'words' => $words,
          'resources' => $resources,
          'products_models' => $products_models,
          'start_date' => $start_date,
          'end_date' => $end_date,
      ]; 

      

      $baseApi = new BaseApi($params);
      $crawling = new Crawler($params); 
      // $baseApi->callApiResources();
      
      

      $cache = Yii::$app->cache;
      $model =  $cache->getOrSet($alertId, function () use ($baseApi,$crawling) {
          $model_api = $baseApi->countAndSearchWords();
          $model_web = $crawling->countAndSearchWords();
          return ArrayHelper::merge($model_api,$model_web);
      }, 1000);


        $exporter = new Spreadsheet([
        'dataProvider' => new ArrayDataProvider([
            'allModels' => $model['tweets']['sentences']
        ]),
          'columns' => [
            [
                  'attribute' => 'product',
                  'contentOptions' => [
                      'alignment' => [
                          'horizontal' => 'center',
                          'vertical' => 'center',
                      ],
                  ],
              ],
              [
                  'attribute' => 'source',
                  'contentOptions' => [
                      'alignment' => [
                          'horizontal' => 'center',
                          'vertical' => 'center',
                      ],
                  ],
              ],
              [
                  'attribute' => 'url',
              ],
              [
                  'attribute' => 'created_at',
              ],
              [
                  'label' => 'author_name',
                  'value' => function($model) {
                    return preg_replace('/[[:^print:]]/', '', $model['author_name']);
                  },

              ],
              [
                  'label' => 'author_username',
                  'value' => function($model) {
                    // replace emojis
                    $author_name = preg_replace('/[[:^print:]]/', '', $model['author_username']);
                    return $author_name;
                  },

              ],
              [
                  'label' => 'post_form',
                  'value' => function($model) {
                    return preg_replace('/[[:^print:]]/', '', $model['post_from'][1]);
                  },

              ],
              
          ],
      ]);
      return $exporter->send($resource_name.".xls");

    }

    public function actionExcelLive($alertId,$resource_name)
    {
      $alert = Alerts::findOne($alertId);
      $nameAlert = $alert->name;
      $start_date = $alert->start_date;
      $end_date = $alert->end_date;

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
      $products_models = [];
        // models products
        foreach (ProductsModelsAlerts::find()->where(['alertId' => $alertId])->with('productModel')->each() as $product) {
            // batch query with eager loading
            $products_models[$product->productModel->product->category->name][$product->productModel->product->name][] = $product->productModel->serial_model;
        }

      $params = [
          'alertId' => $nameAlert,
          'words' => $words,
          'resources' => $resources,
          'products_models' => $products_models,
          'start_date' => $start_date,
          'end_date' => $end_date,
      ]; 

      

      $baseApi = new BaseApi($params);
      $crawling = new Crawler($params); 
      // $baseApi->callApiResources();
      
      

      $cache = Yii::$app->cache;
      $model =  $cache->getOrSet($alertId, function () use ($baseApi,$crawling) {
          $model_api = $baseApi->countAndSearchWords();
          $model_web = $crawling->countAndSearchWords();
          return ArrayHelper::merge($model_api,$model_web);
      }, 1000);

      
        $exporter = new Spreadsheet([
        'dataProvider' => new ArrayDataProvider([
            'allModels' => $model['liveChat']['sentences_live']
        ]),
          'columns' => [
              [
                  'attribute' => 'product',
                  'contentOptions' => [
                      'alignment' => [
                          'horizontal' => 'center',
                          'vertical' => 'center',
                      ],
                  ],
              ],
              [
                  'attribute' => 'title',
              ],
              [
                  'attribute' => 'source',
                  'contentOptions' => [
                      'alignment' => [
                          'horizontal' => 'center',
                          'vertical' => 'center',
                      ],
                  ],
              ],
              [
                  'attribute' => 'sentence_said',
              ],
              [
                  'attribute' => 'created_at',
              ],
              [
                  'attribute' => 'author_name',
              ],
              [
                  'attribute' => 'entity',
              ],
              [
                  'attribute' => 'status',
              ],
              [
                  'attribute' => 'url_retail',
              ],
          ],
      ]);
      return $exporter->send($resource_name.".xls");

    }

    public function actionExcelLiveConversations($alertId,$resource_name)
    {
      $alert = Alerts::findOne($alertId);
      $nameAlert = $alert->name;
      $start_date = $alert->start_date;
      $end_date = $alert->end_date;

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
      $products_models = [];
        // models products
        foreach (ProductsModelsAlerts::find()->where(['alertId' => $alertId])->with('productModel')->each() as $product) {
            // batch query with eager loading
            $products_models[$product->productModel->product->category->name][$product->productModel->product->name][] = $product->productModel->serial_model;
        }

      $params = [
          'alertId' => $nameAlert,
          'words' => $words,
          'resources' => $resources,
          'products_models' => $products_models,
          'start_date' => $start_date,
          'end_date' => $end_date,
      ]; 

      

      $baseApi = new BaseApi($params);
      $crawling = new Crawler($params); 
      // $baseApi->callApiResources();
      
      

      $cache = Yii::$app->cache;
      $model =  $cache->getOrSet($alertId, function () use ($baseApi,$crawling) {
          $model_api = $baseApi->countAndSearchWords();
          $model_web = $crawling->countAndSearchWords();
          return ArrayHelper::merge($model_api,$model_web);
      }, 1000);

      
        $exporter = new Spreadsheet([
        'dataProvider' => new ArrayDataProvider([
            'allModels' => $model['live_conversations']['sentences_live_conversations']
        ]),
          'columns' => [
              [
                  'attribute' => 'product',
                  'contentOptions' => [
                      'alignment' => [
                          'horizontal' => 'center',
                          'vertical' => 'center',
                      ],
                  ],
              ],
              [
                  'attribute' => 'id',
                  'contentOptions' => [
                      'alignment' => [
                          'horizontal' => 'center',
                          'vertical' => 'center',
                      ],
                  ],
              ],
              [
                  'attribute' => 'sentence_said',
              ],
              [
                  'attribute' => 'created_at',
              ],
              [
                  'attribute' => 'author_name',
              ],
              [
                  'attribute' => 'entity',
              ],
              [
                  'attribute' => 'status',
              ],
          ],
      ]);
      return $exporter->send($resource_name.".xls");

    }


    /**
     * [setDictionaries description]
     * @param [type] $form_alert [description]
     * @param [type] $alertId    [description]
     */
    public static function getProducts($product)
    {
        $moduleName = \Yii::$app->controller->module->name;
        $module = \Yii::$app->getModule($moduleName);
        $controller = new AlertController($module->name, $module);

        return $controller->getModelsProducts($product);
    }


}
