<?php

namespace app\modules\monitor\controllers;

use yii;
use yii\web\Controller;
use app\models\SearchForm;
use app\models\LiveChatApi;



/**
 * Default controller for the `monitor` module
 */
class LiveChatController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $form_model = new SearchForm();
        $form_model->scenario = 'live-chat';

        $liveChat = new LiveChatApi();
        $chat = $liveChat->getByParams('Eduardo');
        echo "<pre>";
        print_r($chat);
        echo "</pre>";
        die();
    	
        
		
    	
		
    	return $this->render('index',['form_model' => $form_model]);
    }	
 
}
