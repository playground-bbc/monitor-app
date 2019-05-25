<?php

namespace app\modules\monitor\controllers;

use yii\web\Controller;
use LiveChat\Api\Client as LiveChat;

use Goutte\Client;
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
    	$html = <<<'HTML'
			<!DOCTYPE html>
			<html>
			    <body>
			        <p class="message">Hello World!</p>
			        <div id="test">
			        	<p>Hello Crawler!</p>
			        </div>
			    </body>
			</html>
			HTML;

		$words = ['Hello Crawler!'];
		$node = [];

		$client = new Client();
        $crawler = $client->request('GET', 'http://localhost/monitor-app/alfa/web/index.php');

		foreach ($crawler as $domElement) {
		    $node[] =  $domElement->parentNode;
		}
		echo "<pre>";
		var_dump($node);
		echo "</pre>";
		die();
        return $this->render('index');
    }
}
