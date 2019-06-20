<?php

namespace app\modules\monitor\controllers;

use yii;
use app\models\SearchForm;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

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
   
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionDelete()
    {
        return $this->render('delete');
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionCreate()
    {
        $form_alert = new SearchForm();
        $form_alert->name = 'alert_'.uniqid();
        $form_alert->scenario = 'alert';

        if ($form_alert->load(Yii::$app->request->post())) {
            
        }

        return $this->render('create',['form_alert' => $form_alert]);
    }

    public function actionUpdate()
    {
        return $this->render('update');
    }

    public function actionView()
    {
        return $this->render('view');
    }

}
