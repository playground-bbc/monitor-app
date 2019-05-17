<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\data\ArrayDataProvider;
use app\models\LoginForm;
use app\models\ContactForm;


use \Codebird\Codebird;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            /*
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            */
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
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        
        Codebird::setConsumerKey(Yii::$app->params['twitter']['api_key'], Yii::$app->params['twitter']['api_secret_key']);
        $cb = Codebird::getInstance();

        if (!Yii::$app->session->has('oauth_token_twitter')) {
            $reply = $cb->oauth_requestToken([
                'oauth_callback' => 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']
            ]);

              // store the token
              $cb->setToken($reply->oauth_token, $reply->oauth_token_secret);
              Yii::$app->session->set('oauth_token_twitter',$reply->oauth_token);
              Yii::$app->session->set('oauth_token_secret_twitter',$reply->oauth_token_secret);
              Yii::$app->session->set('oauth_verify_twitter',true);
              

              // redirect to auth website
              $auth_url = $cb->oauth_authorize();
              header('Location: ' . $auth_url);
              die();
        }elseif (isset($_GET['oauth_verifier']) && isset($_SESSION['oauth_verify'])) {
          // verify the token
          $cb->setToken(Yii::$app->session->get('oauth_token_twitter'), Yii::$app->session->get('oauth_token_secret_twitter'));
          Yii::$app->session->remove('oauth_verify');

          // get the access token
          $reply = $cb->oauth_accessToken([
            'oauth_verifier' => $_GET['oauth_verifier']
          ]);

          // store the token (which is different from the request token!)
          Yii::$app->session->set('oauth_token_twitter',$reply->oauth_token);
          Yii::$app->session->set('oauth_token_secret_twitter',$reply->oauth_token_secret);

          // send to same URL, without oauth GET parameters
          header('Location: ' . basename(__FILE__));
          die();
        }
        


       if (Yii::$app->request->post('search_twitter')) {
           # code...
            $params = [
                'q' => Yii::$app->request->post('search_twitter'),
                'lang' => 'es',
                'result_type' => 'recent',
                'count' => '20',

            ];
            $reply = (array) $cb->search_tweets($params, true); 

            return $this->render('index',[
                'reply' => $reply['statuses']
            ]);
       }

            

        return $this->render('index',[
            'reply' => []
        ]);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
       
        /*
            Yii::$app->session->remove('oauth_token_twitter');
            Yii::$app->session->remove('oauth_token_secret_twitter');
            Yii::$app->session->remove('oauth_verify_twitter');
        */


        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
