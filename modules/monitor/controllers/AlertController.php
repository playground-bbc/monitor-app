<?php

namespace app\modules\monitor\controllers;

use yii;
use app\models\SearchForm;

class AlertController extends \yii\web\Controller
{
   
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
            var_dump(Yii::$app->request->post());
            die();
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
