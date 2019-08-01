<?php

namespace app\modules\monitor\controllers;

use yii;

use app\models\ProductsModels;
use app\models\scraping\Crawler;

use app\modules\monitor\controllers\AlertController;

class WebController extends \yii\web\Controller
{
    public function actionDelete()
    {
        return $this->render('delete');
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionSearchWeb()
    {   
        
        if(Yii::$app->request->isAjax){
            $data = \Yii::$app->request->post();
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $product = $data['product_name'];
            $models_products = AlertController::getProducts([$product]);
            $products_models = [];
            foreach (ProductsModels::find()->where(['serial_model' => $models_products])->with('product')->each() as $model) {
                // batch query with eager loading
                $products_models[$model->product->category->name][$model->product->name][] = $model->serial_model;
            }

            $web = [
                'alertId'         => $data['alert_name'],
                'resources'       => $data['url'],
                'products_models' => $products_models,
            ];
            $crawling = new Crawler($web); 
            $crawling->callCrawling();

            return [
                'data' => [
                    'success' =>true,
                    'message' => $models_products,
                ],
                'code' => 0,
            ];
        }
    }



}
