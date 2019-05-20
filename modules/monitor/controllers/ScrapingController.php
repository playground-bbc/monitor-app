<?php
namespace app\modules\monitor\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;

use app\models\WebPage;



class ScrapingController extends Controller
{
   
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
       return $this->render('index');
    }


    /**
     * Renders the create view for the module
     * @return string
     */
    public function actionCreate()
    {
      $model = new WebPage();
       return $this->render('create',['model' => $model]);
    }

}
