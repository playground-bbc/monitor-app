<?php
namespace app\modules\monitor\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;

use app\models\Resource;
use app\models\TypeResource;



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
      $model = new Resource();
      $categories = TypeResource::find()->all();

      if ($model->load(Yii::$app->request->post()) && $model->save()) {

        return $this->redirect(['view','id' => $model->id]);

      }

      return $this->render('create',[
        'model' => $model,
        'categories' => $categories
      ]);
    }


    /**
     * Renders the view view for the module
     * @return string
     */
    public function actionView()
    {
       return $this->render('view');
    }

}
