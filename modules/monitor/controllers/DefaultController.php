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
			'pizza'=> 'http://localhost/pizza/',
		//	'menu'=>'http://localhost/pizza/menu.html'
		];
        /*$crawler = $client->request($type,$url);*/
        

        //$product = $crawler->filter('#section-counter');

		//$status_code = $client->getResponse()->getStatus();
		$searchword = ['Italian Cuizine','Pizza'];
		$rules = [
        	'h1'=> "//h1[text()]",
        	//'h2'=>"//h2[text()]",
        	//'h3'=>"//h3[text()]",
        	//'h4'=>"//p[text()]",
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
							    similar_text($word, $text, $percent);
							    return ['id' => $node->extract(['id']),
							    		'_text' => $text,
							    		'percent' => $percent
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


    function get_corpus_index($corpus = array(), $separator=' ') {

	    $dictionary = array();
	    $doc_count = array();

	    foreach($corpus as $doc_id => $doc) {
	        $terms = explode($separator, $doc);
	        $doc_count[$doc_id] = count($terms);

	        // tf–idf, short for term frequency–inverse document frequency, 
	        // according to wikipedia is a numerical statistic that is intended to reflect 
	        // how important a word is to a document in a corpus

	        foreach($terms as $term) {
	            if(!isset($dictionary[$term])) {
	                $dictionary[$term] = array('document_frequency' => 0, 'postings' => array());
	            }
	            if(!isset($dictionary[$term]['postings'][$doc_id])) {
	                $dictionary[$term]['document_frequency']++;
	                $dictionary[$term]['postings'][$doc_id] = array('term_frequency' => 0);
	            }

	            $dictionary[$term]['postings'][$doc_id]['term_frequency']++;
	        }

	        //from http://phpir.com/simple-search-the-vector-space-model/

	    }

	    return array('doc_count' => $doc_count, 'dictionary' => $dictionary);
	}

	function get_similar_documents($query='', $corpus=array(), $separator=' '){

	    $similar_documents=array();

	    if($query!=''&&!empty($corpus)){

	        $words=explode($separator,$query);
	        $corpus= $this->get_corpus_index($corpus);
	        $doc_count=count($corpus['doc_count']);

	        foreach($words as $word) {
	            //$entry = $corpus['dictionary'][$word];
	            $entry = (isset($corpus['dictionary'][$word])) ? $corpus['dictionary'][$word] : '';
	            foreach($entry['postings'] as $doc_id => $posting) {

	                //get term frequency–inverse document frequency
	                $score=$posting['term_frequency'] * log($doc_count + 1 / $entry['document_frequency'] + 1, 2);

	                if(isset($similar_documents[$doc_id])){
	                    $similar_documents[$doc_id]+=$score;
	                }
	                else{
	                    $similar_documents[$doc_id]=$score;
	                }

	            }
	        }

	        // length normalise
	        foreach($similar_documents as $doc_id => $score) {
	            $similar_documents[$doc_id] = $score/$corpus['doc_count'][$doc_id];
	        }

	        // sort fro  high to low
	        arsort($similar_documents);
	    }   
	    return $similar_documents;
	}
}
