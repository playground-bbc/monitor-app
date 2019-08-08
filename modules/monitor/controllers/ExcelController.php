<?php

namespace app\modules\monitor\controllers;

use yii;
use yii\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;

use yii2tech\spreadsheet\Spreadsheet;

use app\models\api\BaseApi;

use app\models\Alerts;
use app\models\scraping\Crawler;
use app\models\ProductsModelsAlerts;

/**
 *  wrapper to export excel document
 */
class ExcelController extends \yii\web\Controller
{
    /**
     * [actionExcel temporal view to give the document to the user]
     * @param  [int] $alertId          [the current alertId display show view]
     * @param  [string] $resource_name [only twitter,liveChat, LiveConversations and awario]
     * @return [document]              [excel document]
     */
    public function actionExcel($alertId,$resource_name)
    {
      $alert = Alerts::findOne($alertId);
      $nameAlert = $alert->name;
      $start_date = $alert->start_date;
      $end_date = $alert->end_date;
      

      $params = [
          'alertId' => $nameAlert,
          'words' => $this->getWords($alert),
          'resources' => [$resource_name],
          'products_models' => $this->getProductModel($alertId),
          'start_date' => $start_date,
          'end_date' => $end_date,
      ]; 

      $baseApi = new BaseApi($params);
      
      $cache = Yii::$app->cache;
      $model =  $cache->getOrSet($alertId, function () use ($baseApi) {
          $model_api = $baseApi->countAndSearchWords();
          return $model_api;
      }, 1000);

      $nickname = $this->bringNickname($resource_name);
      $exporter = new Spreadsheet([
        'dataProvider' => new ArrayDataProvider([
            'allModels' => $model[$nickname]['sentences']
        ]),
          'columns' => $this->bringColumnsByNickname($nickname,$model) ,
      ]);
      
      return $exporter->send($resource_name.".xls");

    }
    /**
     * [actionExcelWeb temporal only view to give the document data web to the user]
     * @param  [int] $alertId          [the current alertId display show view]
     * @param  [string] $resource_name [only awario]
     * @return [document]              [excel document]
     */
    public function actionExcelWeb($alertId,$resource_name){
      $alert = Alerts::findOne($alertId);
      $nameAlert = $alert->name;

      $words = $this->getWords($alert);
      
      $web = [
        'alertId' => $nameAlert,
        'words' => $words['Palabras Libres'],
        'resources' => $this->getResourceWeb($alert),
        'products_models' => $this->getProductModel($alertId),
      ];

      $crawling = new Crawler($web); 
      $crawling->callCrawling();

      $cache = Yii::$app->cache;
      $model =  $cache->getOrSet($alertId, function () use ($crawling) {
          $model_web = $crawling->countAndSearchWords();
          return $model_web;
      }, 1000);

      $exporter = new Spreadsheet([
        'dataProvider' => new ArrayDataProvider([
            'allModels' => $model['web']['sentences_web']
        ]),
          'columns' => $this->bringColumnsByNickname('web',$model) ,
      ]);
      
      return $exporter->send($resource_name.".xls");
    }
    /**
     * [getWords give dictionaries by alert object]
     * @param  [obj] $alert   [the current alert]
     * @return [array]        [array 'dictionaries_title' => [words]]
     */
    public function getWords($alert){
      // words
      $words = [];
      foreach ($alert->dictionaries as $key => $value) {
          $words[$value->category->name][] = $value->word;
      }
      return $words;
    
    }
    /**
     * [getProductModel give prodcuts by alert id]
     * @param  [int] $alertId   [the current alertId display show view]
     * @return [array]          [array 'products' => [products_models]]
     */
    public function getProductModel($alertId){
        $products_models = [];
        // models products
        foreach (ProductsModelsAlerts::find()->where(['alertId' => $alertId])->with('productModel')->each() as $product) {
            // batch query with eager loading
            $products_models[$product->productModel->product->category->name][$product->productModel->product->name][] = $product->productModel->serial_model;
        }
        return $products_models;
    }
    /**
     * [getResourceWeb give only resource web url by alert object]
     * @param  [obj] $alert  [object alert current]
     * @return [array]       [array 'domain' => 'url' ]
     */
    public function getResourceWeb($alert){

      $resource = [];

      foreach ($alert->alertResources as $alert => $resources) {
          for ($i=0; $i <sizeof($resources->resources) ; $i++) { 
              if($resources->resources[$i]->typeResourceId == 1){
                $resource ['web'][] = $resources->resources[$i]->url;
              }
          }
      }
      return $resource['web'];

    }
    /**
     * [bringNickname get "nickname" by resource_name]
     * @param  [string] $resource_name [name of resource]
     * @return [string]                [nickname]
     */
    public function bringNickname($resource_name){
        $nickname =[
            'Twitter' => 'tweets',
            'Live Chat' => 'liveChat',
            'Live Chat Conversations' => 'live_conversations',
            'awario' => 'awario',
        ];
        return $nickname[$resource_name];
    }
    /**
     * [bringColumnsByNickname get kind the columns for Spreadsheet object calling to their respective function by nickname]
     * @param  [string] $nickname [nickname]
     * @param  [obj] $model       [object with data processed]
     * @return [array]            [array comming their function]
     */
    public function bringColumnsByNickname($nickname,$model){
        switch($nickname){
            case "tweets":
                return $this->bringTwitterColumns($model);
                break;
            case "liveChat": 
                return $this->bringLiveChatColumns($model);
                break;   
            case "live_conversations":
                return $this->bringLiveConversationsColumns($model);
            case "awario":
                return $this->bringAwarioColumns($model);   
            case "web":
                return $this->bringWebColumns($model);     
            default:
                null;
                break;    
        }
    }
    /**
     * [bringTwitterColumns give twitter columns for Spreadsheet object]
     * @param  [obj] $model       [object with data processed]
     * @return [array]            [array for columns twitter]
     */
    public function bringTwitterColumns($model){
        return [
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
              
          ];
    }
    /**
     * [bringLiveChatColumns give live chat columns for Spreadsheet object]
     * @param  [obj] $model       [object with data processed]
     * @return [array]            [array for columns liveChat]
     */
    public function bringLiveChatColumns($model){
        return [
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
          ];

    }
    /**
     * [bringLiveConversationsColumns give live chat conversations columns for Spreadsheet object]
     * @param  [obj] $model       [object with data processed]
     * @return [array]            [array for columns liveChat conversations]
     */
    public function bringLiveConversationsColumns($model){

        return [
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
          ];

    }
    /**
     * [bringAwarioColumns give awario columns for Spreadsheet object]
     * @param  [obj] $model       [object with data processed]
     * @return [array]            [array for columns awario conversations]
     */
    public function bringAwarioColumns($model){
        return [
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
              
          ];
    }
    /**
     * [bringWebColumns give web columns for Spreadsheet object]
     * @param  [obj] $model       [object with data processed]
     * @return [array]            [array for columns web scraping]
     */
    public function bringWebColumns($model){
      return [
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
              ],
              [
                  'label' => 'post_from',
                  'value' => function($model) {
                    return $model['post_from'][0];
                  },

              ],
              [
                  'attribute' => 'tag',
              ],
              [
                  'attribute' => 'url',
              ],
              
          ];
    }
}
