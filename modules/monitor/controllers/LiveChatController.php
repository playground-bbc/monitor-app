<?php

namespace app\modules\monitor\controllers;

use yii;
use yii\web\Controller;
use app\models\SearchForm;
use LiveChat\Api\Client as LiveChat;


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
    	$LiveChatAPI = new LiveChat(Yii::$app->params['liveChat']['apiLogin'], Yii::$app->params['liveChat']['apiKey']);
		
    	$params = [
    		//'status' => 'solved',
    		//'query' => 'malo',
    		'source' => 'chat-window', // 'lc2', 'chat-window', 'mail', 'facebook:conversation', 'facebook:post' or 'agent-app-manual'.
    		'page' => '1'

    	];
		$tickets = $LiveChatAPI->tickets->get($params);
		$ticketsId = $LiveChatAPI->tickets->getSingleTicket('Q0EYH');
		
    	return $this->render('index',['form_model' => $form_model]);
    }	
 
}
