<?php
namespace app\modules\monitor\controllers;

use app\models\api\LiveChatApi;
use app\models\Dictionary;
use app\models\ProductsCategories;
use app\models\ProductsModels;
use app\models\SearchForm;
use yii\web\Controller;
use yii;

/**
 * Default controller for the `monitor` module
 */
class LiveChatController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionCreate()
    {
        $form_model           = new SearchForm();
        $form_model->scenario = 'live-chat';

        if ($form_model->load(Yii::$app->request->post())) {
            var_dump(Yii::$app->request->post());
            die();
        }

        return $this->render('create',['form_model' => $form_model]);

    }

    public function actionUpload()
    {
        if (\Yii::$app->request->isAjax) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $data = \Yii::$app->request->post();
            var_dump($data);
            die();
            return [
                'data' => [
                    'success' => true,
                    'message' => 'Model has been saved.',
                    'postId'=>$data,
                ],
                'code' => 0,
            ];

        }
    }

}
