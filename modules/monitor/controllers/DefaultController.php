<?php

namespace app\modules\monitor\controllers;

use yii\web\Controller;
use LiveChat\Api\Client as LiveChat;

use Goutte\Client;
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
    	$words = ['Hello Crawler!'];
		$node = [];
		$html = '';

		$client = new Client();
		$guzzleClient = new GuzzleClient([
			'timeout' => 60,
			'allow_redirects' => false
		]);
		$client->setClient($guzzleClient);
		$type = 'GET';
		$urls = [
			'playground2'=> 'http://localhost/playground2/',
			'hola-mundo'=>'http://localhost/playground2/index.php/2019/05/13/hola-mundo/'
		];
        /*$crawler = $client->request($type,$url);*/
        

        //$product = $crawler->filter('#section-counter');

		//$status_code = $client->getResponse()->getStatus();
		$searchword = ['Edítala','tu primera entrada'];
		$rules = [
        	'h1'=> "//h1[text()]",
        	'h2'=>"//h2[text()]",
        	'h3'=>"//h3[text()]",
        	'h4'=>"//h4[text()]",
        	'p'=>"//p[text()]",
        	//'text_paragraph' => "//p[text()[contains(.,".$word .")]]",
            //'text_title_h1' => "//h1[text()[contains(.,".$word .")]]",
        ];

        /*$node = [
        	'title_page'=>[
        		word => [
					'body' =>[
	        			[
	        				'id' => 'some',
	        				'_text' => 'some some',
	        				'percent' => 3232332
	        			],
	        			[
	        				'id' => 'some2',
	        				'_text' => 'anoter text',
	        				'percent' => 3232332
	        			],
	        		]

        		]
        	]
        ];*/


        foreach ($urls as $domain => $url) {
        	$crawler = $client->request($type,$url);
        	$status_code = $client->getResponse()->getStatus();
        	if ($status_code == 200) {
        		$content_type = $client->getResponse()->getHeader('Content-Type');
        		if (strpos($content_type, 'text/html') !== false) {
        			foreach ($searchword as $word) {
        				foreach ($rules as $title => $rule) {
	        					$node[$domain][$word][$title] = $crawler->filterXpath($rule)->each(function (Crawler $node,$i) use ($searchword,$word) {
							    $text = $node->text();
							    $isSubstring = $this->isSubstring($word, $text);
							    return ['id' => $node->extract(['id']),
							    		'_text' => $text,
							    		'isSubstring' => ($isSubstring == -1) ? false : true
									];
							});
	        			}
        			}
        		}
        	}
        }


		/*if ($status_code == 200) {
			$content_type = $client->getResponse()->getHeader('Content-Type');
			if (strpos($content_type, 'text/html') !== false) {
				foreach ($rules as $rule) {
					//$node[$rule]['id'] = $crawler->filterXpath("//body")->evaluate($rule)->extract(['id']);
					/*similar_text($searchword, $node[$rule]['_text'], $percent);
					$node[$rule]['percent'] = $percent;
				}
			}
		}*/

		/*if($status_code == 200){
			$content_type = $client->getResponse()->getHeader('Content-Type');
			if (strpos($content_type, 'text/html') !== false) {
				$out = $crawler->filterXpath("//body")->evaluate("//h3[text()]")->extract(['id','class','_text']);
			}
		}

		*/
		
		/*$var_1 = 'Pizza';
		$var_2 = 'italian Pizza';
		similar_text($var_1, $var_2, $percent);
		echo '<pre>';
		    print_r($percent);
		    die();
		echo '</pre>';*/
		 
		
		/*foreach ($product as $domElement) {
		    foreach($domElement->childNodes as $node) {
		        $html.= $domElement->ownerDocument->saveHTML($node);
		    }
		}*/


		/*$corpus = array(
		    1 => 'hello worldd',
		);
				
		$match_results=$this->get_similar_documents($searchword,$corpus);
		echo '<pre>';
		    print_r($match_results);
		    die();
		echo '</pre>';*/

		

		echo "<pre>";
		var_dump($node);
		echo "</pre>";
		die();
        

        return $this->render('index');

		
    }


    // Returns true if s1 is substring of s2 
	function isSubstring($s1, $s2) 
	{ 
		$M = strlen($s1); 
		$N = strlen($s2); 

		// A loop to slide 
		// pat[] one by one 
		for ($i = 0; $i <= $N - $M; $i++) 
		{ 
			$j = 0; 

			// For current index i, 
			// check for pattern match 
			for (; $j < $M; $j++) 
				if ($s2[$i + $j] != $s1[$j]) 
					break; 

			if ($j == $M) 
				return $i; 
		} 

		return -1; 
	} 
}
