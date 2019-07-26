<?php
namespace app\modules\monitor\controllers;

use yii;
use yii\web\Controller;
use yii\helpers\ArrayHelper;

use app\models\Alerts;
use app\models\SearchForm;
use app\models\ProductsFamily;
use app\models\api\LiveChatApi;
use app\models\api\DriveProductsApi;

use Goutte\Client;
use GuzzleHttp\Promise;
use Stringizer\Stringizer;
use LiveChat\Api\Client as LiveChat;
use GuzzleHttp\Client as GuzzleClient;
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
        
        /*echo "<pre>";
        print_r($data);
        die();*/

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
        						$data[$d]['product'] = $product;
                                $search[$domain][$webpage][$label][] = $data[$d];
        					}
        				}
        			}
        		}
        	}
        }
       
        echo "<pre>";
        print_r($search);
        die();


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

	
	public function actionChat()
	{
        $chats = new LiveChatApi();

        $alert = Alerts::findOne(20);
     	$nameAlert = $alert->name;
        $start_date = $alert->start_date;
        $end_date = $alert->end_date;

      	$words = [];
        // words
      	foreach ($alert->dictionaries as $key => $value) {
	        $words[$value->category->name][] = $value->word;
	    }


        $params = [
          'alertId' => $nameAlert,
          'words' => $words,
          'resources' => ['Twitter'],
          'products_models' => ['NanoCell 4K','SMART TV LED 55" 4K UHD'],
          'start_date' => $start_date,
          'end_date' => $end_date,
      ]; 

        $tickets = new LiveChatApi($params); 
        var_dump($tickets->getChats());
        die();
		
	}


	public function actionError()
	{
	    $exception = Yii::$app->errorHandler->exception;
	    if ($exception !== null) {
	        return $this->render('error', ['exception' => $exception]);
	    }
	}


}
