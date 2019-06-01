<?php
namespace app\modules\monitor\controllers;

use yii\web\Controller;

use Goutte\Client;
use LiveChat\Api\Client as LiveChat;
use GuzzleHttp\Client as GuzzleClient;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Default controller for the `monitor` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        
        return $this->render('index');
	}

}
