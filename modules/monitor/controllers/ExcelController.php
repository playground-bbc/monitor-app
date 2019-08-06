<?php

namespace app\modules\monitor\controllers;

use yii;
use app\models\ProductsModelsAlerts;
use app\models\Alerts;

class ExcelController extends \yii\web\Controller
{
    public function actionDelete()
    {
        return $this->render('delete');
    }

    public function actionIndex()
    {
        return $this->render('index');
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



}
