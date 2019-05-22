<?php
namespace app\modules\monitor\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;

use app\models\Resource;
use app\models\TypeResource;
use app\models\SearchForm;

use Goutte\Client;



class ScrapingController extends Controller
{
   
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
      $form_model = new SearchForm();

      if ($form_model->load(Yii::$app->request->post())) {
          var_dump(Yii::$app->request->post());
          die();
        //return $this->redirect(['view','id' => $resource->id]);

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

        return $this->redirect(['view','id' => $resource->id]);

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
      /*
      $resource = Resource::findOne($id);
      $client = new Client();
      $crawler = $client->request('GET', 'https://www.yiiframework.com/doc/guide/2.0/en/db-migrations');
       //var_dump($crawler);
       //die();
    
      $name = $resource->name;
      $searchword = "Tim";
      //$out = [];
      //$out = $crawler->evaluate("//p[text()[contains(.,".$searchword .")]]")->each(function ($node) use($searchword)
      $out = $crawler->evaluate("//p[text()[contains(.,".$searchword .")]]")->each(function ($node) use($searchword)
      {
        if (preg_match("/".$searchword."/i",$node->text())) {
          return $node->text();
        }
        
      });
     // $stripped = preg_replace('/\s+/', ' ', $out[0]);
      echo "<pre>";
      var_dump(array_filter($out));
      echo "</pre>";
      die();
      */

       
      return $this->render('view');
    }

}
