<?php 
namespace app\models\api;

use yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

use app\models\api\LiveChatApi;
use app\models\api\TwitterApi;
use app\models\filebase\Filebase;

use Stringizer\Stringizer;

/**
 * Wrap class for call other api models
 */
class BaseApi extends Model
{
	public $alertId;
	public $words;
	public $products_models;
	public $resources;
	public $start_date;
	public $end_date;

	//live chat options
	public $live_chat_page = 1;

	//Twitter Options
	public $twitter_query;
	public $twitter_lang = 'es';
	public $twitter_result_type = 'recent';
	public $twitter_count = '20';
	// limits
	public $twitter_limit = 10;
	public $api_limit = 5;

	// own Base api search
	const TWITTER = 'TWITTER';
	const LIVECHAT = 'LIVECHAT';

	const COLOR = [
		'Palabras Libres' => '#14112C',
		'MH Series' => '#FF6B6B',
		'Positivos MH Series' => '#89E62D',
		'Negativos MH Series' => '#AE1E11',
		'Buenas' => '#6454D8',
		'Malas' => '#570F09',
		'Kws Positivos' => '#7A6AEE',
		'Kws Negativos' => '#8C3131',
		'Frases Negativas' => '#5D2A00',
		'Frases Positivas' => '#89E62D',
	];
	
	/**
	 * @param array
	 */
	function __construct($params)
	{
		foreach ($params as $key => $value) {
			$this->$key = $value;
		}

		if ($this->isAwarioFile()) {
			$path =  $this->isAwarioFile();
			$data =  $this->getSearchDataAwario($path);
			$model = $this->setSearchDataAwario($data);
			$this->saveJsonFile($model);
		}
		
	}

	public function callApiResources()
	{
		$resource = array_flip($this->resources);

		$tweets = [];
		if (isset($resource['Twitter'])) {

			$data = $this->getSearchTwitter();
			$tweets = $this->setSearchDataTwitter($data);

		}
		
		$tickets = [];
		if (isset($resource['Live Chat'])) {
			$data = $this->getSearchLiveChat();
			$tickets = $this->setSearchDataTickets($data);
		}

		$chats = [];
		if (isset($resource['Live Chat Conversations'])){
			$chats = $this->setLiveChatCoversations();
		}
		
		$model = ArrayHelper::merge($tweets,$tickets);
		$model = ArrayHelper::merge($model,$chats);
		//$this->saveJsonFile(ArrayHelper::merge($tweets,$tickets));
		
		$this->saveJsonFile($model);

	}


	public function countAndSearchWords()
	{
		$filebase = new Filebase();
		$filebase->alertId = $this->alertId;
		$db = $filebase->getFilebase();

		// we go through the json 
		$data = $db->field('data');
		// delete web
		unset($data['WEB']);
		$model = [];

		$resource = array_flip($this->resources);

		if (isset($resource['Twitter'])) {
			$countByCategoryInTweet['countByCategoryInTweet'] = $this->countWordsInTweetsByCategory($data);
		
			if (!is_null($countByCategoryInTweet['countByCategoryInTweet'])) {
				
				$sentences['sentences'] = $this->addTagsSentenceFoundInTweets($data);
				$countWords['countWords'] = $this->addWordsInTweet($data);

				//join tweets
				$model['tweets'] = ArrayHelper::merge($sentences,$countWords);
				$model['tweets'] = ArrayHelper::merge($countByCategoryInTweet,$model['tweets']);
			}
		}
		
		if (isset($resource['Live Chat'])) {
			$countByCategoryInLiveChat['countByCategoryInLiveChat'] = $this->countWordsInLiveChatByCategory($data);

			if (!is_null($countByCategoryInLiveChat['countByCategoryInLiveChat'])) {
				
				$sentences_live['sentences_live'] = $this->addTagsSentenceFoundInLive($data);
				
				$countWords_live['countWords_live'] = $this->addWordsInLive($data);

				//join Live
				$model['liveChat'] = ArrayHelper::merge($sentences_live,$countWords_live);
				$model['liveChat'] = ArrayHelper::merge($countByCategoryInLiveChat,$model['liveChat']);
				// total ticket
				$total_tickets['total'] = $this->getTotalTicket($sentences_live); 
				$model['liveChat'] = ArrayHelper::merge($total_tickets,$model['liveChat']);
			}
		}

		if(isset($resource['Live Chat Conversations'])){
			$liveChat = new LiveChatApi();
			$words = $this->words;
			$liveChatCoversations['count_category_conversations'] = $liveChat->countByCategoryInLiveChatConversations($data,$words);
			if(!is_null($liveChatCoversations['count_category_conversations'])){

				$liveChatCoversations['count_words_conversations'] = $liveChat->addWordsInLiveChatConversations($data,$words);
				$liveChatCoversations['sentences_live_conversations'] = $liveChat->addTagsSentenceFoundInConversations($data,$words,self::COLOR);

				$model['live_conversations'] = $liveChatCoversations;

			}
			

		}

		if ($this->isAwarioFile()) {
			$awario_data = $this->searchProdductsInAwario($data);
			
			if (!is_null($awario_data)) {
				
				$awarioFile['countByCategoryInAwario'] = $this->countByCategoryAwario($awario_data);
				$awarioFile['sentence_awario'] = $this->addTagsSentenceFoundInAwario($awario_data);
				$awarioFile['countWords_awario'] = $this->addWordsInAwario($awario_data);
				
			
				//$model['awario'] = ArrayHelper::merge($awario_data,$countByCategoryInLive);
				$model['awario'] = $awarioFile;
				

			}
		}

		
		return $model;
		
	}

	/**
	 * @return array
	 */
	private function getSearchTwitter()
	{
		$twitterApi = new TwitterApi();
		
		$params = [
			//'geocode' => '-33.459229,-70.645348,50000km',
            'lang' => 'es',
            'result_type' => 'mixed',
            'count' => '100',
            'until' => Yii::$app->formatter->asDate($this->end_date,'yyyy-MM-dd'),
            'max_id' => 0,

        ];
        $dates = [
        	'start_date' => $this->start_date,
        	'end_date' => $this->end_date,
        ];
        

        $data = [];
        $categories = array_keys($this->products_models);


        
        foreach ($categories as $key) {
        	$params['q'] = $key;
        	$data[$key] = $twitterApi->search_tweets_by_date($params);
        	/*if ($data[$key]['rate']['remaining'] < $this->twitter_limit) {
        		break;
        	}*/
        }

        foreach ($this->products_models as $key => $value) {
        	foreach ($value as $model => $serial_model) {
        		$params['q'] = $model;
        	    $data[$model] = $twitterApi->search_tweets_by_date($params);
        	   /* if ($data[$key]['rate']['remaining'] < $this->twitter_limit) {
	        		break;
	        	}*/
	        	// new serial_model
	        	for ($i=0; $i <sizeof($serial_model) ; $i++) { 
	        		$params['q'] = $serial_model[$i];
	        		$data[$serial_model[$i]] = $twitterApi->search_tweets_by_date($params);
	        	    /*if ($data[$serial_model[$i]]['rate']['remaining'] < $this->twitter_limit) {
		        		break;
		        	}*/
	        	}
        	}



        	// serial_model
        	/*foreach ($value as $model => $serial_model) {
        		$params['q'] = $serial_model;
        	    $data[$serial_model] = $twitterApi->search_tweets($params);
        	    if ($data[$key]['rate']['remaining'] < $this->twitter_limit) {
	        		break;
	        	}
        	}*/


        }
        return $data;
	}
	/**
	 * @param array
	 */
	private function setSearchDataTwitter($data =[])
	{
		
		$tweets = [];
		$source = 'TWITTER';

	
		foreach ($data as $product => $object){
			$index = 0;
			for ($o = 0; $o < sizeof($object) ; $o++){
				if(!empty($object[$o]['statuses'])){
					for ($s =0; $s < sizeof($object[$o]['statuses']) ; $s++){
						$tweets[$product][$index]['source'] = self::TWITTER;
						
						
						if(isset($object[$o]['statuses'][$s]['entities']['urls'][0])){
							$tweets[$product][$index]['url'] = $object[$o]['statuses'][$s]['entities']['urls'][0]['url'];
						}else{
							$tweets[$product][$index]['url'] = '-';
						}

						if(array_key_exists('place', $object[$o])){
							if(!is_null($object[$o]['place'])){
								$tweets[$product][$index]['location'] = $object[$o]['place']['country'];
							}
						}else{
							$tweets[$product][$index]['location'] = "-";
						}
						
						$tweets[$product][$index]['created_at'] = $object[$o]['statuses'][$s]['created_at'];
						$tweets[$product][$index]['author_name'] = $object[$o]['statuses'][$s]['user']['name'];
						$tweets[$product][$index]['author_username'] = $object[$o]['statuses'][$s]['user']['screen_name'];
						$tweets[$product][$index]['followers_count'] = $object[$o]['statuses'][$s]['user']['followers_count'];
						$tweets[$product][$index]['post_from'] = $object[$o]['statuses'][$s]['text'];
						$index++;
					} // for each statuses
				} // if not empty statuses
			}// for each object twitter
		} // for each product

		return $tweets;

	}
	/**
	 * @return array
	 */
	private function getSearchLiveChat()
	{
		$liveChat = new LiveChatApi();
		
		$models_products_all = [];
		foreach ($this->products_models as $key => $value) {
			$models_products_all[] = $key;
			foreach ($value as $model => $serial_model) {
				$models_products_all[] = $model;
				foreach ($serial_model as $key => $value) {
					$models_products_all[] = $value;
				}
			}
		}



		$params = [
			'date_from' => Yii::$app->formatter->asDate($this->start_date,'yyyy-MM-dd'),
            'date_to' => Yii::$app->formatter->asDate($this->end_date,'yyyy-MM-dd'),
            'query' => $models_products_all,
            'page' => $this->live_chat_page,
        ];



       $data = $liveChat->loadParams($params)->getTickets()->all();
      
       
       return $data;


	}
	/**
	 * [getTotalTicket total of tickets since the beginning of time]
	 * @return [int] 
	 */
	private function getTotalTicket($sentences_live)
	{
		$liveChat = new LiveChatApi();
        $total['total'] = $liveChat->countTicketsAll();

        $status= ['open' => 0, 'pending'=> 0, 'solved'=> 0 ,'spam'=> 0];

        $tickets = ArrayHelper::getValue($sentences_live,'sentences_live');
        if ($tickets) {
        	$total['tickets'] = count($tickets);
        	for ($i=0; $i <sizeof($tickets) ; $i++) { 
        		if (in_array($tickets[$i]['status'], $status)) {
        			$status[$tickets[$i]['status']] += 1;
        		}
        	}
        }

        $total = ArrayHelper::merge($total,$status);
        
        return $total;
	}
	/**
	 * [setSearchDataTickets set la data from api twitter to array with standar key]
	 * @param array $data [description]
	 */
	private function setSearchDataTickets($data = [])
	{
		$tickets = [];
		$source = 'LIVECHAT';

		// order by total
		foreach ($data as $model => $obj) {
			if ($obj['total']) {
				for ($i=0; $i <sizeof($obj['tickets']) ; $i++) { 
					$tickets[$model][] = $obj['tickets'][$i];
				}
			}
		}
		$model_ticket = [];
		foreach ($tickets as $model => $obj) {
			for ($i=0; $i <sizeof($obj) ; $i++) { 
				
				$model_ticket[$model][$i]['source'] = self::LIVECHAT;
				$model_ticket[$model][$i]['type'] = 'Ticket';
				$model_ticket[$model][$i]['id'] = $obj[$i]->id;
				$model_ticket[$model][$i]['status'] = $obj[$i]->status;
				$model_ticket[$model][$i]['created_at'] = $obj[$i]->date;
				$model_ticket[$model][$i]['author_name'] = $obj[$i]->requester->name;
				$model_ticket[$model][$i]['username'] = $obj[$i]->requester->mail;
				$model_ticket[$model][$i]['title'] = $obj[$i]->subject;
				$model_ticket[$model][$i]['url'] = $obj[$i]->source->url;
				$model_ticket[$model][$i]['url_retail'] = parse_url($obj[$i]->source->url,PHP_URL_HOST);
				
				// get post_from
				for ($j=0; $j <sizeof($obj[$i]->events) ; $j++) { 
					if (isset($obj[$i]->events[$j]->message)) {						
						$model_ticket[$model][$i]['post_from'][] = [ $obj[$i]->events[$j]->author->type => trim(preg_replace('/\s+/', ' ', $obj[$i]->events[$j]->message))];
					}
				}
			}
		}
		/*var_dump($model_ticket);
		die();*/
		return $model_ticket;
		
	}

	/**
	 * [isAwarioFile returns path the file awario on the serve]
	 * @return boolean [true is find or false is'nt]
	 */
	private function isAwarioFile()
	{
		// path to folder flat archives
		$s = DIRECTORY_SEPARATOR;
		
		$resourceName = 'awario';
		$fileName =  $this->alertId. '.csv';
		$folderOptions = [
            'name' => $this->alertId,
            'path' => '@monitor',
            'resource' => $resourceName,
        ];
        
		$path = \Yii::getAlias($folderOptions['path'])."{$s}".$folderOptions['resource']."{$s}". $folderOptions['name']. "{$s}";
        return is_dir($path) ? $path."{$s}{$fileName}" : false;
	}
	/**
	 * [getSearchDataAwario given a certain path opens the csv file and runs through it and returns its contents]
	 * @param  [type] $path [string of the path of the ejm file: root / monitor-app-data / awario / alertId / alertId.csv]
	 * @return [type]       [array]
	 */
	private function getSearchDataAwario($path)
	{
		$file = new \SplFileObject($path);
		$file->setFlags(\SplFileObject::READ_CSV);
		$file->setCsvControl(',', '"', '\\'); // this is the default anyway though
		$data = [];
		foreach ($file as $row) {
		    $data[] = $row;
		}
		return $data;
	}
	/**
	 * [setSearchDataAwario normalizes the array to be saved in the json file]
	 * @param array $data 
	 */
	private function setSearchDataAwario($data=[])
	{	
		$model = [];
		$source = 'AWARIO';
		$index = 0;
		for ($i = 1; $i < sizeof($data); $i++) {
			if (sizeof($data[0]) == sizeof($data[$i])) {
				if (isset($data[$i][0])) {
					$model[$source][$index]['source']      = mb_convert_encoding($data[$i][0], 'UTF-8');
				}
				if (isset($data[$i][1])) {
					$model[$source][$index]['url']         = mb_convert_encoding($data[$i][1], 'UTF-8');
				}
				if (isset($data[$i][2])) {
					$model[$source][$index]['created_at']  = mb_convert_encoding($data[$i][2], 'UTF-8');
				}
				if (isset($data[$i][3])) {
					$model[$source][$index]['author_name'] = mb_convert_encoding($data[$i][3], 'UTF-8');
				}
				if (isset($data[$i][4])) {
					$model[$source][$index]['username']    = mb_convert_encoding($data[$i][4], 'UTF-8');
				}
				if (isset($data[$i][4])) {
					$model[$source][$index]['author_username'] = mb_convert_encoding($data[$i][4], 'UTF-8');
				}
				if (isset($data[$i][5])) {
					$model[$source][$index]['title']       = mb_convert_encoding($data[$i][5], 'UTF-8');
				}
				if (isset($data[$i][6])) {
					$model[$source][$index]['post_from']   = mb_convert_encoding($data[$i][6], 'UTF-8');
				}
				if (isset($data[$i][7])) {
					$model[$source][$index]['reach']       = mb_convert_encoding($data[$i][7], 'UTF-8');
				}
				if (isset($data[$i][8])) {
					$model[$source][$index]['sentiment']   = mb_convert_encoding($data[$i][8], 'UTF-8');
				}
				if (isset($data[$i][9])) {
					$model[$source][$index]['starred']     = mb_convert_encoding($data[$i][9], 'UTF-8');
				}
				if (isset($data[$i][10])) {
					$model[$source][$index]['done']     = mb_convert_encoding($data[$i][10], 'UTF-8');
				}

				// only index -1 to conserve index
			}else{ $index -= 1;}
			
			$index++;
        }
        return $model;
	}

	private function setLiveChatCoversations(){
		
		$liveChat = new LiveChatApi();

		$models_products_all = [];
		foreach ($this->products_models as $key => $value) {
			$models_products_all[] = $key;
			foreach ($value as $model => $serial_model) {
				$models_products_all[] = $model;
				foreach ($serial_model as $key => $value) {
					$models_products_all[] = $value;
				}
			}
		}


		$params = [
			'date_from' => Yii::$app->formatter->asDate($this->start_date,'yyyy-MM-dd'),
            'date_to' => Yii::$app->formatter->asDate($this->end_date,'yyyy-MM-dd'),
            'query' => $models_products_all,
            'page' => $this->live_chat_page,
            'timezone' => 'America/Santiago',
        ];

        $data = $liveChat->getChats($params,$this->alertId);
        return $data;
	}

	/**
	 * [lastUpdateJsonFile set true if the document is created in more that 5 minutes]
	 * @return [boolean] [if true call the api if false el document json is created and update in less that 5 minutes]
	 */
	private function lastUpdateJsonFile()
	{
		$filebase = new Filebase();
		$filebase->alertId = $this->alertId;
		$isFile = $filebase->getFilebase()->toArray();

		if (count($isFile)) {
			$updatedAt = $filebase->getFilebase()->updatedAt();
			$update_date = new \DateTime($updatedAt);
			$datediff = $update_date->diff(new \DateTime(date('Y-m-d H:i:s')));
			return ($datediff->i >= $this->api_limit) ? true : false;
		}

		return true;
	}

	
	/**
	 * [countWordsInTweetsByCategory count the words by the dictionary categories by product model example: HD => ['Good words' => 1, 'bad' => 2]]
	 * @param  [type] $data [get json]
	 * @return [type]       [array / null]
	 */
	private function countWordsInTweetsByCategory($data)
	{
		// set array analisys Model => dictionary_title => word
		$products = array_keys($data);
		$countByCategory = [];
		for ($i=0; $i <sizeof($products) ; $i++) { 
			foreach ($this->words as $categories => $words) {
                $countByCategory[$products[$i]][$categories] = 0;
            }
		}
		foreach ($data as $model => $value) {
			for ($i=0; $i <sizeof($value) ; $i++) { 
				if ($value[$i]['source'] == self::TWITTER) {
					$stringizer = new Stringizer($value[$i]['post_from']);
					foreach ($this->words as $categories => $words) {
		                for ($j=0; $j <sizeof($words) ; $j++) { 
		                	if ($stringizer->contains($words[$j])) {
		                		$countByCategory[$model][$categories] += $stringizer->containsCount($words[$j]) ;
		                	}
		                }
		            }
				}
			}
		}

		return (count($countByCategory)) ? $countByCategory : null;
		
	}

	/**
	 * [addTagsSentenceFoundInTweets return tweets with html tag depending on the type of word found]
	 * @param  [type] $data [get json]
	 * @return [type]       [array / null]
	 */
	private function addTagsSentenceFoundInTweets($data)
	{
		$tweets = [];
		set_time_limit(500);
		foreach ($data as $model => $value) {
			for ($i=0; $i <sizeof($value) ; $i++) { 
				if ($value[$i]['source'] == self::TWITTER) {
					$stringizer = new Stringizer($value[$i]['post_from']);
					$tmp = [];
					foreach ($this->words as $categories => $words) {
		                for ($j=0; $j <sizeof($words) ; $j++) { 
		                	if ($stringizer->contains($words[$j])) {
		                		$background = self::COLOR[$categories];
		                		$sentence = (array) $stringizer->replaceIncaseSensitive($words[$j], "<span style='background: {$background}'>{$words[$j]}</span>");
		                		$value[$i]['post_from'] = array_values($sentence);
		                		$value[$i]['product'] = $model;
		                		$tmp[] = $value[$i];
		                	}
		                }
		            }
		            if (!empty($tmp)) {
			        	$tweets[] = end($tmp);
			        }
				}
			}
		}
		return $tweets;
	}


	private function addWordsInTweet($data)
	{
		// set array analisys Model => dictionary_title => word
		$products = array_keys($data);
		$countByWords = [];
		for ($i=0; $i <sizeof($products) ; $i++) { 
			foreach ($this->words as $categories => $words) {
                for ($j=0; $j <sizeof($words) ; $j++) { 
                	$countByWords[$products[$i]][$categories][$words[$j]] = 0;
                }
            }
		}

		foreach ($data as $products => $value) {
			for ($i=0; $i <sizeof($value) ; $i++) { 
				if ($value[$i]['source'] == self::TWITTER) {
					$stringizer = new Stringizer($value[$i]['post_from']);
					foreach ($this->words as $categories => $words) {
		                for ($j=0; $j <sizeof($words) ; $j++) { 
		                	if ($stringizer->contains($words[$j]) && isset($countByWords[$products][$categories][$words[$j]])) {
		                		$countByWords[$products][$categories][$words[$j]] += $stringizer->containsCount($words[$j]) ;
		                	}
		                }
		            }
				}
			}
		}

		
		$data = [];
		foreach ($countByWords as $products => $categories_words) {
			foreach ($categories_words as $words => $word) {
				foreach ($word as $key => $value) {
					if ($value) {
						$data[$products][$words][$key] = $value;
					}
				}
			}
		}
		unset($countByWords);

		return $data;

	}

	/**
	 * [countWordsInLiveChatByCategory account the words grouping by the name of the dictionaries]
	 * @param  [array] $data 
	 * @return [array]       
	 */
	private function countWordsInLiveChatByCategory($data)
	{
		// set array analisys Model => dictionary_title => word

		$products = array_keys($data);
		$countByCategory = [];
		for ($i=0; $i <sizeof($products) ; $i++) { 
			foreach ($this->words as $categories => $words) {
                for ($j=0; $j <sizeof($words) ; $j++) { 
                	$countByCategory[$products[$i]][$categories] = 0;
                }
            }
		}
		unset($countByCategory['WEB']);
		unset($countByCategory['AWARIO']);

		set_time_limit(500);

		foreach ($data as $model => $value) {
			for ($i=0; $i <sizeof($value) ; $i++) { 
				if (($value[$i]['source'] == self::LIVECHAT) && ($value[$i]['type'] == 'Ticket') ) {
					// count by title
					$stringizer_title = new Stringizer($value[$i]['title']);
					foreach ($this->words as $categories => $words) {
		                for ($j=0; $j <sizeof($words) ; $j++) { 
		                	if ($stringizer_title->containsCountIncaseSensitive($words[$j])) {
		                		$countByCategory[$model][$categories] += $stringizer_title->containsCountIncaseSensitive($words[$j]) ;
		                	}
		                }
		            }
		            // count by post_form
					for ($p=0; $p <sizeof($value[$i]['post_from']) ; $p++) { 

						if(isset($value[$i]['post_from'][$p]['client'])){
							$said = array_values($value[$i]['post_from'][$p]);
							$stringizer = new Stringizer($said[0]);
							foreach ($this->words as $categories => $words) {
				                for ($j=0; $j <sizeof($words) ; $j++) { 
				                	if ($stringizer->containsCountIncaseSensitive($words[$j])) {
				                		$countByCategory[$model][$categories] += $stringizer->containsCountIncaseSensitive($words[$j]) ;
				                	}
				                } // for each word
				            } // for each dictionaries
						} // only client
					}// for each post_from
				} // if livechat source
			}
		}


		
		
		
		return (count($countByCategory)) ? $countByCategory : null;

	}

	/**
	 * [addWordsInLive add the occurrences individually]
	 * @param [array] $data
	 */
	private function addWordsInLive($data)
	{
		// set array analisys Model => dictionary_title => word
		$products = array_keys($data);
		$countByWords = [];
		for ($i=0; $i <sizeof($products) ; $i++) { 
			foreach ($this->words as $categories => $words) {
                for ($j=0; $j <sizeof($words) ; $j++) { 
                	$countByWords[$products[$i]][$categories][$words[$j]] = 0;
                }
            }
		}

		foreach ($data as $products => $value) {
			for ($i=0; $i <sizeof($value) ; $i++) { 
				if (($value[$i]['source'] == self::LIVECHAT) && ($value[$i]['type'] == 'Ticket')) {

					// check by title
					$stringizer_title = new Stringizer($value[$i]['title']);
					//count title
					foreach ($this->words as $categories => $words) {
		                for ($j=0; $j <sizeof($words) ; $j++) { 
		                	if ($stringizer_title->containsCountIncaseSensitive($words[$j]) && isset($countByWords[$products][$categories][$words[$j]])) {
		                		$countByWords[$products][$categories][$words[$j]] += $stringizer_title->containsCountIncaseSensitive($words[$j]) ;
		                	}
		                }
		            }


					for ($p=0; $p <sizeof($value[$i]['post_from']) ; $p++) { 
						if(isset($value[$i]['post_from'][$p]['client'])){
							$said = array_values($value[$i]['post_from'][$p]);
							$stringizer = new Stringizer($said[0]);
							foreach ($this->words as $categories => $words) {
				                for ($j=0; $j <sizeof($words) ; $j++) { 
				                	if ($stringizer->containsCountIncaseSensitive($words[$j]) && isset($countByWords[$products][$categories][$words[$j]])) {
				                		$countByWords[$products][$categories][$words[$j]] += $stringizer->containsCountIncaseSensitive($words[$j]) ;
				                	}
				                } // for each words
				            } // for each dictionaries
						} // only client
					} // for each post_from
				}
			}
		}

		
		$data = [];
		foreach ($countByWords as $products => $categories_words) {
			foreach ($categories_words as $words => $word) {
				foreach ($word as $key => $value) {
					if ($value) {
						$data[$products][$words][$key] = $value;
					}
				}
			}
		}
		unset($countByWords);

		return $data;
	}

	/**
	 * [addTagsSentenceFoundInLive add html tags to words that match]
	 * @param [array] $data 
	 */
	private function addTagsSentenceFoundInLive($data)
	{
		$live = [];
		set_time_limit(500);
		foreach ($data as $model => $value) {
			for ($i=0; $i <sizeof($value) ; $i++) { 
				if (($value[$i]['source'] == self::LIVECHAT) && ($value[$i]['type'] == 'Ticket')) {
					// check by title
					$stringizer_title = new Stringizer($value[$i]['title']);
				
		            //check by post_from
		            for ($p=0; $p <sizeof($value[$i]['post_from']) ; $p++) { 
	            		$entity = array_keys($value[$i]['post_from'][$p]);
	            		$said = array_values($value[$i]['post_from'][$p]);

	            		$stringizer_sentences = new Stringizer($said[0]);
						$tmp = [];
	            		foreach ($this->words as $categories => $words) {
							$background = self::COLOR[$categories];
			                for ($j=0; $j <sizeof($words) ; $j++) {
								if ($stringizer_title->containsCountIncaseSensitive($words[$j])){
									$sentence_title = (array) $stringizer_title->replaceIncaseSensitive($words[$j], "<span style='background: {$background}'>{$words[$j]}</span>");
									$title_tags = array_values($sentence_title);
									$value[$i]['product'] = $model;
									$value[$i]['title'] = $title_tags[0];
									$tmp[] = $value[$i];
								} 
			                	if ($stringizer_sentences->containsCountIncaseSensitive($words[$j])) {
			                		
			                		if ($entity[0] == 'client'){
			                			$sentence = (array) $stringizer_sentences->replaceIncaseSensitive($words[$j], "<span style='background: {$background}'>{$words[$j]}</span>");
			                			$sentence = array_values($sentence);
			                			$value[$i]['sentence'] = $sentence[0];
			                		}else{
			                			$value[$i]['sentence'] = $said[0];
			                		}
			                		
			                		
			                		$value[$i]['entity'] = $entity[0];
			                		$value[$i]['product'] = $model;
			                		$value[$i]['sentence_said'] = $said[0];
			                		//$live[] = $value[$i];
			                		$tmp[] = $value[$i];
			                		
			                	}// end if contains word
			                }// for each words
							
			            } // for each dictionaries
						if (!empty($tmp))
						{
							$live[] = end($tmp);
						}
	            	} // for each post_from

				} // if livechat source
				
			}
		}
		
		  
		return $live;
	}
	

	private function searchProdductsInAwario($data)
	{
		$awario_data =[];
		// lets find out
		set_time_limit(500);
		if (isset($data['AWARIO'])) {
			// set array analisys Model => dictionary_title => word
			$products = [];
			foreach ($this->products_models as $key => $value) {
				$products[] = $key;
				foreach ($value as $model => $serial_model) {
					$products[] = $model;
					foreach ($serial_model as $serial => $model_value) {
						$products[] = $model_value;
					}
				}
			}
			
			

			for ($a=0; $a <sizeof($data['AWARIO']) ; $a++) { 
				// search by title 
				$stringizer_title = new Stringizer($data['AWARIO'][$a]['title']);

				// search by post_from
				$stringizer = new Stringizer($data['AWARIO'][$a]['post_from']);
				
				for ($p=0; $p <sizeof($products) ; $p++) { 

					if ($stringizer_title->containsCountIncaseSensitive($products[$p])) {
						$data['AWARIO'][$a]['products'] = $products[$p];
						$awario_data['AWARIO'][$products[$p]][] = $data['AWARIO'][$a];
					}

					if ($stringizer->containsCountIncaseSensitive($products[$p])) {
						$data['AWARIO'][$a]['products'] = $products[$p];
						$awario_data['AWARIO'][$products[$p]][] = $data['AWARIO'][$a];
					}
				}
			}
			
		}
		
		return (count($awario_data)) ? $awario_data : null;
	}


	private function countByCategoryAwario($awario_data)
	{
		// set array analisys Model => dictionary_title => word
		$products = array_keys($awario_data['AWARIO']);
		$countByCategory = [];
		for ($i=0; $i <sizeof($products) ; $i++) { 
			foreach ($this->words as $categories => $words) {
                for ($j=0; $j <sizeof($words) ; $j++) { 
                	$countByCategory[$products[$i]][$categories] = 0;
                }
            }
		}

		foreach ($awario_data['AWARIO'] as $product => $value) {
			for ($i=0; $i <sizeof($value) ; $i++) { 
				// count by title
				$stringizer_title = new Stringizer($value[$i]['title']);
				foreach ($this->words as $categories => $words) {
	                for ($j=0; $j <sizeof($words) ; $j++) { 
	                	if ($stringizer_title->containsCountIncaseSensitive($words[$j])) {
	                		$countByCategory[$product][$categories] += $stringizer_title->containsCountIncaseSensitive($words[$j]) ;
	                	}
	                }
	            }
	            // count by post_form
	            $stringizer = new Stringizer($value[$i]['post_from']);
	            foreach ($this->words as $categories => $words) {
	                for ($j=0; $j <sizeof($words) ; $j++) { 
	                	if ($stringizer->containsCountIncaseSensitive($words[$j])) {
	                		$countByCategory[$product][$categories] += $stringizer->containsCountIncaseSensitive($words[$j]) ;
	                	}
	                }
	            }
			}
		}

		return (count($countByCategory)) ? $countByCategory : null;

	}

	private function addTagsSentenceFoundInAwario($awario_data)
	{
        $data = [];
		// lets find out
		set_time_limit(500);
        foreach ($awario_data['AWARIO'] as $products => $value ) {
        	for ($i=0; $i <sizeof($value) ; $i++) { 
        		// search by title
        		$stringizer_title = new Stringizer($value[$i]['title']);
        		// search by post_form
        		$stringizer = new Stringizer($value[$i]['post_from']);
        		
        		foreach ($this->words as $categories => $words) {
	                for ($j=0; $j <sizeof($words) ; $j++) { 
	                	
	                	if ($stringizer_title->containsCountIncaseSensitive($words[$j])) {
	                		$background = self::COLOR[$categories];
	                		$sentence = (array) $stringizer_title->replaceIncaseSensitive($words[$j], "<span style='background: {$background}'>{$words[$j]}</span>");
	                		$value[$i]['title_orign'] = $value[$i]['title'];
	                		$value[$i]['title'] = array_shift($sentence);
	                	}

	                	if ($stringizer->containsCountIncaseSensitive($words[$j])) {
	                		$background = self::COLOR[$categories];
	                		$sentence = (array) $stringizer->replaceIncaseSensitive($words[$j], "<span style='background: {$background}'>{$words[$j]}</span>");
	                		$value[$i]['post_from_orign'] = $value[$i]['post_from'];
	                		$value[$i]['post_from'] = array_shift($sentence);
	                	}
	                }// for each words
	            } // for each dictionary
	            $data[] = $value[$i];
        	} // for records awario
        } // for each prodcuts awario
        return $data;
	}


	private function addWordsInAwario($awario_data){

		// set array analisys Model => dictionary_title => word
		$products = array_keys($awario_data['AWARIO']);
		$countByWords = [];
		for ($i=0; $i <sizeof($products) ; $i++) { 
			foreach ($this->words as $categories => $words) {
                for ($j=0; $j <sizeof($words) ; $j++) { 
                	$countByWords[$products[$i]][$categories][$words[$j]] = 0;
                }
            }
		}

		foreach ($awario_data['AWARIO'] as $product => $value) {
			for ($i=0; $i <sizeof($value) ; $i++) { 
				// count by title
				$stringizer_title = new Stringizer($value[$i]['title']);
				foreach ($this->words as $categories => $words) {
	                for ($j=0; $j <sizeof($words) ; $j++) { 
	                	if ($stringizer_title->containsCountIncaseSensitive($words[$j])) {
	                		$countByWords[$product][$categories][$words[$j]] += $stringizer_title->containsCountIncaseSensitive($words[$j]) ;
	                	}
	                }
	            }
	            // count by post_form
	            $stringizer = new Stringizer($value[$i]['post_from']);
	            foreach ($this->words as $categories => $words) {
	                for ($j=0; $j <sizeof($words) ; $j++) { 
	                	if ($stringizer->containsCountIncaseSensitive($words[$j])) {
	                		$countByWords[$product][$categories][$words[$j]] += $stringizer->containsCountIncaseSensitive($words[$j]) ;
	                	}
	                }
	            }
			}
		}

		$data = [];
		//delete word in zero
		foreach ($countByWords as $products => $categories_words) {
			foreach ($categories_words as $words => $word) {
				foreach ($word as $key => $value) {
					if ($value) {
						$data[$products][$words][$key] = $value;
					}
				}
			}
		}
		unset($countByWords);

		return (count($data)) ? $data : null;



	}

	
	/**
	 * @param  array
	 * @return null
	 */
	public function saveJsonFile($data = [])
	{
		$filebase = new Filebase();
		$filebase->alertId = $this->alertId;
		$filebase->save($data);
	}


}

 ?>
