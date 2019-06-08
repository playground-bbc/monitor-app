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
            'date_from' => '2018-06-05',
            'date_to'   => '2019-06-06',
          //  'query' => "50UK6300PSB"
        ];
        $liveChat = new LiveChatApi();
        $liveChat->setParams($params);

        $products_models = ProductsModels::find()->asArray()->all();
        $dictionary  = new Dictionary;
        $productsCategories =  ProductsCategories::find()->where(['or', ['parentId' => 0], ['parentId' => null]])->all();

        $pages = $liveChat->chatByQuery($products_models, 1);
        
        var_dump($liveChat->getData());
        /*$count_words = $liveChat->searchAndCountBywords($dictionary->orderedwords,1);
        $words_per_product_line = $liveChat->add_words_per_product_line($count_words);
        $total_tickets['total'] = $liveChat->get_total_tickets(1);
        $get_tickets_number_of_tickets_status = $liveChat->get_tickets_number_of_tickets_status(1);


        $total_tickets = ArrayHelper::merge($total_tickets,$get_tickets_number_of_tickets_status);

        return $this->render('view',[
            'productsCategories' => $productsCategories,
            'words_per_product_line' => $words_per_product_line,
            'count_words' => $count_words,
            'total_tickets' => $total_tickets,
        ]);*/
    }

}
