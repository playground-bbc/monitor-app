<?php
namespace app\modules\monitor\controllers;

use app\models\api\LiveChatApi;
use app\models\Dictionary;
use app\models\ProductsCategories;
use app\models\ProductsModels;
use app\models\SearchForm;
use yii\web\Controller;

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

        return $this->render('create',['form_model' => $form_model]);

    }

    public function actionDefault()
    {
        
    }

}
