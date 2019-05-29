<?php
namespace app\modules\monitor\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;

use app\models\Resource;
use app\models\TypeResource;
use app\models\SearchForm;
use app\models\Crawler;





class ScrapingController extends Controller
{
   
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
      $form_model = new SearchForm();
      $model = new Crawler();

      if ($form_model->load(Yii::$app->request->post())) {
         


        
       // return $this->redirect(['view','id' => $resource->id]);

      }

      return $this->render('index',['form_model' => $form_model]);
    }


    /**
     * Renders the create view for the module
     * @return string
     */
    public function actionCreate()
    {
      $resource = new Resource();
      $typeResource = TypeResource::find()->all();
      

      if ($resource->load(Yii::$app->request->post()) && $resource->save()) {

        return $this->redirect('index');

      }

      return $this->render('create',[
        'resource' => $resource,
        'typeResource' => $typeResource
      ]);
    }


    /**
     * Renders the view view for the module
     * @return string
     */
    public function actionView($id)
    {
       
      return $this->render('view');
    }

}
