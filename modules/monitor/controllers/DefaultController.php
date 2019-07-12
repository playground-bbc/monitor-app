<?php
namespace app\modules\monitor\controllers;

use yii;
use yii\web\Controller;
use app\models\ProductsFamily;
use app\models\api\DriveProductsApi;
use yii\helpers\ArrayHelper;

use app\models\SearchForm;


use LiveChat\Api\Client as LiveChat;
use GuzzleHttp\Client as GuzzleClient;
use Stringizer\Stringizer;


use Goutte\Client;

use GuzzleHttp\Promise;

use Symfony\Component\DomCrawler\Crawler;



/**
 * Default controller for the `monitor` module
 */
class DefaultController extends Controller
{

	public function actions()
	{
		return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
	}

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
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
			'lgblog'=> 'https://www.lgblog.cl/novedades/tutoriales-lg/descubre-como-preparar-mac-cheese-en-casa/',
		
		];
		
		$rules = [
        	'title'=> "//title",
        	'h1'=> "//h1",
        	'h2'=> "//h2",
        	'h3'=> "//h3",
        	'h4'=> "//h4",
        	'h5'=> "//h5",
    		'strong'=> "//strong",
    		'a' => "//a",
    		'span' => '//span',
    		'li' => '//li',
    		'address' => '//address',
    		'article' => '//div/article',
    		'aside' => '//aside',
    		'hgroup' => '//hgroup',
        	'p'=> "//p",
    		'footer' => '//footer/div',
        ];

       
        $resource = [];
        $crawler = [];
        foreach ($urls as $domain => $url) {
        	$resource[$domain] = $client->request($type,$url);
        	$status_code = $client->getResponse()->getStatus();
        	if ($status_code == 200) {
        		$content_type = $client->getResponse()->getHeader('Content-Type');
        		if (strpos($content_type, 'text/html') !== false) {
        			$crawler[$domain] = $resource[$domain];
        		}
        	}
        }
        $data = [];
        $attributes = ['_name', '_text', 'id'];
        foreach ($crawler as $domain => $craw) {
        	foreach ($rules as $title => $rule) {
        		$data[$domain][] = $craw->filterXpath($rule)->each(function (Crawler $node,$i)
        		{
        			return [
	    				'id' => $node->extract(['id']),
						'_text' =>  $node->text(),
					];
        		});
        	}
        }

       

        $rowData = [];
       


        $index = 0;
        foreach ($urls as $domain => $url) {
        	for ($i=0; $i <sizeof($data[$domain]) ; $i++) { 
        		for ($j=0; $j <sizeof($data[$domain][$i]) ; $j++) { 
        			$s = new Stringizer($data[$domain][$i][$j]['_text']);
        			if (!$s->isEmpty()) {
        				$text = trim($data[$domain][$i][$j]['_text']);
        			    $rowData[$domain][$index]['text'] = $text;
        			    if ($data[$domain][$i][$j]['id'][0] != '') {
        			    	$id = $data[$domain][$i][$j]['id'][0];
        			    	$rowData[$domain][$index]['id'] = $id;
        			    }
        			    $index ++;
        			}
        			
        		}
        	}
        }
        
    

		

		echo "<pre>";
		print_r($rowData);
		echo "</pre>";
		die();
        

        return $this->render('index');
	}

	
	public function actionTest()
	{
		$client = new Client();
		
		$guzzleClient = new GuzzleClient([
			'timeout' => 60,
			'verify' => false
		]);
		
		$client->setClient($guzzleClient);
		
		$type = 'GET';
		
		$urls = [
			//'lgblog'=> 'https://www.lgblog.cl/',
			//'lgonline.cl' => 'http://localhost/blog/post-page.html',
			'novedades' => 'https://www.lgblog.cl/novedades/tutoriales-lg/descubre-como-preparar-mac-cheese-en-casa/'
		
		];

		$products = ['LG NeoChef','microondas'];

		$words = ['No te compliques'];

		
		$domains = [];

		/*// get all links
		foreach ($urls as $domain => $url) {
			$crawler = $client->request($type, $url);
			$status_code = $client->getResponse()->getStatus();
			if ($status_code== 200) {
				
				$links_count = $crawler->filter('a')->count();
				if ($links_count > 0) {
					$links = $crawler->filter('a')->links();

					$all_links = [];
		            foreach ($links as $link) {
		                $all_links[] = $link->getURI();
		            } // for each links
		            $all_links = array_unique($all_links);
		            $domains[$domain] = $all_links;
				} // if there link
			} // if 200 request
		}// for each url*/

		
		$rules = [
        	'document_title'=> "//title",
        	'cabezera_1'=> "//h1",
        	'cabezera_2'=> "//h2",
        	'cabezera_3'=> "//h3",
        	'cabezera_4'=> "//h4",
        	'cabezera_5'=> "//h5",
    		'negrita'=> "//strong",
    		'link' => "//a",
    		'contenedor' => '//span',
    		'ítem' => '//li',
    		'address' => '//address',
    		'article' => '//div/article',
    		'aside' => '//aside',
    		'hgroup' => '//hgroup',
        	'p'=> "//p",
    		'footer' => '//footer/div',
        ];


       
        $resource = [];
        $crawler = [];
        /*foreach ($domains as $domain => $urls) {

			foreach ($urls as $url) {
				$resource[$domain][] = $client->request($type,$url);
	        	$status_code = $client->getResponse()->getStatus();
	        	if ($status_code == 200) {
	        		$content_type = $client->getResponse()->getHeader('Content-Type');
	        		if (strpos($content_type, 'text/html') !== false) {
	        			$crawler[$domain][$url] = $resource[$domain];
	        		}
	        	}
			}	        	
        }// for each group domains*/

        foreach ($urls as $domain => $url) {
        	$resource[$domain][] = $client->request($type,$url);
        	$status_code = $client->getResponse()->getStatus();
        	if ($status_code == 200) {
        		$content_type = $client->getResponse()->getHeader('Content-Type');
        		if (strpos($content_type, 'text/html') !== false) {
        			$crawler[$domain][$url] = $resource[$domain];
        		}
        	}
        }


        $data = [];
        $attributes = ['_name', '_text', 'id'];
        
        foreach ($crawler as $domain => $urls) {
        	foreach ($urls as $url => $craws) {
        		for ($c=0; $c <sizeof($craws) ; $c++) { 
	        		foreach ($rules as $title => $rule) {
		        		$data[$domain][$url][$title][] = $craws[$c]->filterXpath($rule)->each(function (Crawler $node,$i)
		        		{
		        			$sentences = new Stringizer($node->text());

		        			if (!$sentences->isBlank()) {
		        				return [
				    				'id' => $node->extract(['id']),
									'_text' =>  $sentences->trim(),
								];
		        			}
		        			return null;

		        		});
		        	}
	        	}
        	}
        }

        /*echo "<pre>";
       // var_dump(ArrayHelper::index($data['lgonline.cl']['http://localhost/blog/home-page.html'],null,'_text'));
        print_r($data);*/
        
       // print_r($data);

        $model = [];

        foreach ($data as $domain => $webpages) {
        	foreach ($webpages as $webpage => $labels) {
        		foreach ($labels as $label => $data) {
        			if (!empty($data)) {
        				for ($l=0; $l <sizeof($data) ; $l++) { 
	        				if (!ArrayHelper::isAssociative($data[$l])) {
	        					if (count($data[$l])) {
	        						for ($i=0; $i <sizeof($data[$l]) ; $i++) { 
	        							
	        							if (!empty($data[$l][$i]['_text'])) {
	        								$section['id'] = $data[$l][$i]['id']; 
		        							$section['_text'] = trim($data[$l][$i]['_text']); 
		        							$model[$domain][$webpage][$label][] = $section; 
	        							}
	        						}
	        					}
	        				}
	        			}
        			}
        		}
        	}

        }
        
        /*echo "<pre>";
        print_r($model);
        die();*/

        $search = [];
        // search by procuts
        foreach ($model as $domain => $webpages) {
        	foreach ($webpages as $webpage => $labels) {
        		foreach ($labels as $label => $data) {
        			for ($d=0; $d <sizeof($data) ; $d++) { 
        				$sentence = new Stringizer($data[$d]['_text']);
        				foreach ($products as $product) {
        					if ($sentence->containsCountIncaseSensitive($product)) {
        						$search[$domain][$webpage][$label][] = $data[$d];
        					}
        				}
        			}
        		}
        	}
        }
       
        /*echo "<pre>";
        print_r($search);
        die();*/


        $contains = [];
        // search by words
        foreach ($search as $domain => $webpages) {
        	foreach ($webpages as $webpage => $labels) {
        		foreach ($labels as $label => $data) {
        			for ($d=0; $d <sizeof($data) ; $d++) { 
        				$sentence_in_word = new Stringizer($data[$d]['_text']);
	        			foreach ($words as $word) {
	        				if ($sentence_in_word->containsCountIncaseSensitive($word)) {
								$contains[$domain][$webpage][$label] = $data[$d];
							}
	        			}
        			}
        		}
        	}
        }
        
        echo "<pre>";
        print_r($contains);
        die();
	}

	public function actionCreate()
	{
		return $this->render('create');
	}

	public function actionDrive()
	{
		
		return $this->render('drive/index');
	}

	public function actionSync()
	{
		if (Yii::$app->request->post()) {
			$drive = new DriveProductsApi();
			$data = $drive->getContentDocument();
		}
		return $this->render('drive/index');
	}

	
	public function actionScrapping()
	{
		$form_model = new SearchForm();
		$form_model->scenario = 'scrapping';

		return $this->render('create',['form_model' => $form_model]);
	}


	public function actionError()
	{
	    $exception = Yii::$app->errorHandler->exception;
	    if ($exception !== null) {
	        return $this->render('error', ['exception' => $exception]);
	    }
	}

}
