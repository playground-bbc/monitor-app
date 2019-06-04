<?php
namespace app\modules\monitor\controllers;

use app\models\api\LiveChatApi;
use app\models\ProductsModels;
use app\models\ProductsCategories;
use app\models\Dictionary;
use app\models\SearchForm;
use yii\web\Controller;
use yii\helpers\ArrayHelper;

/**
 * Default controller for the `monitor` module
 */
class LiveChatController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionView()
    {
        $form_model           = new SearchForm();
        $form_model->scenario = 'live-chat';

        $params = [
            'date_from' => '',
            'date_to'   => '',
        ];
        $liveChat = new LiveChatApi();
        $liveChat->setParams($params);

        $products_models = ProductsModels::find()->asArray()->all();
        $dictionary  = new Dictionary;
        $productsCategories =  ProductsCategories::find()->where(['or', ['parentId' => 0], ['parentId' => null]])->all();

        //$pages = $liveChat->chatByQuery($products_models, 1);
        
        $count_words = $liveChat->searchAndCountBywords($dictionary->orderedwords,1);
        
        return $this->render('view',['productsCategories' => $productsCategories]);
    }

}
