<?php
namespace app\modules\monitor\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\data\ArrayDataProvider;
use app\models\TwitterApi;


use \Codebird\Codebird;

class TwitterController extends Controller
{
   
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
     
      $twitterApi = new TwitterApi();
        if (!\Yii::$app->session->has('oauth_token_twitter')) {
              $twitterApi->authenticate();
        }elseif (isset($_GET['oauth_verifier']) && isset($_SESSION['oauth_verify'])) {
           $twitterApi->redirect_to_monitor();
        }

       if (\Yii::$app->request->post('search_twitter')) {
           # code...
            $params = [
                'q' => Yii::$app->request->post('search_twitter'),
                'lang' => 'es',
                'result_type' => 'recent',
                'count' => '20',

            ];
            $reply = $twitterApi->search_tweets($params, true); 
            return $this->render('index',[
                'reply' => $reply['statuses']
            ]);
        }

        
       
       return $this->render('index');
    }

    public function actionLogout()
    {
        $twitterApi = new TwitterApi();
        $twitterApi->logout();
      return $this->goHome();
    }
}
