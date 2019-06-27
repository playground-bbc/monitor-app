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
		'Buenas' => 'green',
		'Malas' => 'red',
		'Kws Positivos' => 'blue',
		'Kws Negativos' => 'orange',
		'Frases Negativas' => 'blue',
		'Frases Positivas' => 'yellow',
	];
	
	/**
	 * @param array
	 */
	function __construct($params)
	{
		foreach ($params as $key => $value) {
			$this->$key = $value;
		}
		$resource = array_flip($this->resources);
		
		if (!$this->lastUpdateJsonFile()) {
			$tweets = [];
			if (isset($resource['Twitter'])) {
				$data = $this->getSearchTwitter();
				$tweets = $this->setSearchDataTwitter($data);
				$this->saveJsonFile($tweets);
			}
			
			$tickets = [];
			if (isset($resource['Live Chat'])) {
				$data = $this->getSearchLiveChat();
				$tickets = $this->setSearchDataTickets($data);
			}
			
			$this->saveJsonFile(ArrayHelper::merge($tweets,$tickets));

			if ($this->isAwarioFile()) {
				$path =  $this->isAwarioFile();
				$data =  $this->getSearchDataAwario($path);
				$model = $this->setSearchDataAwario($data);
				
				$this->saveJsonFile($model);
			}
		}

		$this->countAndSearchWords();
		
	}
	/**
	 * @return array
	 */
	private function getSearchTwitter()
	{
		$twitterApi = new TwitterApi();
		$params = [
           // 'q' => $key,
            'lang' => 'es',
            'result_type' => 'recent',
            'count' => '100',
            'until' => date('yyyy-MM-dd'),

        ];
        /*var_dump(Yii::$app->formatter->asDate($this->start_date,'yyyy-MM-dd'));
        die();*/

        $data = [];
        $categories = array_keys($this->products_models);
        
        foreach ($categories as $key) {
        	$params['q'] = $key;
        	$data[$key] = $twitterApi->search_tweets($params,true);
        	if ($data[$key]['rate']['remaining'] < $this->twitter_limit) {
        		break;
        	}
        }
        foreach ($this->products_models as $key => $value) {
        	foreach ($value as $model => $serial_model) {
        		$params['q'] = $model;
        	    $data[$model] = $twitterApi->search_tweets($params,true);
        	    if ($data[$key]['rate']['remaining'] < $this->twitter_limit) {
	        		break;
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
		
		foreach ($data as $key => $value) {
			for ($i=0; $i <sizeof($value['statuses']) ; $i++) { 
				
				// set source
				$tweets[$key][$i]['source'] = self::TWITTER;

				// get url from tweet
				if (count($value['statuses'][$i]['entities']['urls'])) {
					$tweets[$key][$i]['url'] = $value['statuses'][$i]['entities']['urls'][0]['url'];
				}
				// get created_at from tweet
				if (isset($value['statuses'][$i]['created_at'])) {
					$tweets[$key][$i]['created_at'] = $value['statuses'][$i]['created_at'];
				}
				// get author_name from tweet
				if (isset($value['statuses'][$i]['user']['name'])) {
					$tweets[$key][$i]['author_name'] = $value['statuses'][$i]['user']['name'];
				}
				// get author_username from tweet
				if (isset($value['statuses'][$i]['user']['screen_name'])) {
					$tweets[$key][$i]['author_username'] = $value['statuses'][$i]['user']['screen_name'];
				}

				// get Post from tweet
				if (isset($value['statuses'][$i]['text'])) {
					$tweets[$key][$i]['post_from'] = $value['statuses'][$i]['text'];
				}
			}
		}

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
				$models_products_all[] = $serial_model;
			}
		}
		 

		$params = [
            'date_to' => Yii::$app->formatter->asDate($this->start_date,'yyyy-MM-dd'),
            'date_from' => Yii::$app->formatter->asDate($this->end_date,'yyyy-MM-dd'),
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
	private function getTotalTicket()
	{
		$liveChat = new LiveChatApi();
        $total = $liveChat->countTicketsAll();
        
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
				$model_ticket[$model][$i]['id'] = $obj[$i]->id;
				$model_ticket[$model][$i]['created_at'] = $obj[$i]->date;
				$model_ticket[$model][$i]['author_name'] = $obj[$i]->requester->name;
				$model_ticket[$model][$i]['username'] = $obj[$i]->requester->mail;
				$model_ticket[$model][$i]['title'] = $obj[$i]->subject;
				$model_ticket[$model][$i]['url'] = $obj[$i]->source->url;
				
				// get post_from
				for ($j=0; $j <sizeof($obj[$i]->events) ; $j++) { 
					if (isset($obj[$i]->events[$j]->message)) {						
						$model_ticket[$model][$i]['post_from'][] = [ $obj[$i]->events[$j]->author->type => trim(preg_replace('/\s+/', ' ', $obj[$i]->events[$j]->message))];
					}
				}
			}
		}

		
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
				$model[$source][$index]['source']      = mb_convert_encoding($data[$i][0], 'UTF-8', 'UTF-8');
				$model[$source][$index]['url']         = mb_convert_encoding($data[$i][1], 'UTF-8', 'UTF-8');
				$model[$source][$index]['created_at']  = mb_convert_encoding($data[$i][2], 'UTF-8', 'UTF-8');
				$model[$source][$index]['author_name'] = mb_convert_encoding($data[$i][3], 'UTF-8', 'UTF-8');
				$model[$source][$index]['username']    = mb_convert_encoding($data[$i][4], 'UTF-8', 'UTF-8');
				$model[$source][$index]['title']       = mb_convert_encoding($data[$i][5], 'UTF-8', 'UTF-8');
				$model[$source][$index]['post_from']   = mb_convert_encoding($data[$i][6], 'UTF-8', 'UTF-8');
				$model[$source][$index]['reach']       = mb_convert_encoding($data[$i][7], 'UTF-8', 'UTF-8');
				$model[$source][$index]['sentiment']   = mb_convert_encoding($data[$i][8], 'UTF-8', 'UTF-8');
				$model[$source][$index]['starred']     = mb_convert_encoding($data[$i][9], 'UTF-8', 'UTF-8');
				$model[$source][$index]['done']        = mb_convert_encoding($data[$i][10], 'UTF-8', 'UTF-8');
				// only index -1 to conserve index
			}else{ $index -= 1;}
			
			$index++;
        }
        return $model;
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

	private function countAndSearchWords()
	{
		$filebase = new Filebase();
		$filebase->alertId = $this->alertId;
		$db = $filebase->getFilebase();

		// we go through the json 
		$data = $db->field('data');
		

		$countByCategory = $this->countWordsInTweetsByCategory($data);
		var_dump($countByCategory);
		if (!is_null($countByCategory)) {
			$sentence = $this->addTagsSentenceFoundInTweets($data);
			var_dump($sentence);
			die();
		}
		
		
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
                for ($j=0; $j <sizeof($words) ; $j++) { 
                	$countByCategory[$products[$i]][$categories] = 0;
                }
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
		foreach ($data as $model => $value) {
			for ($i=0; $i <sizeof($value) ; $i++) { 
				if ($value[$i]['source'] == self::TWITTER) {
					$stringizer = new Stringizer($value[$i]['post_from']);
					foreach ($this->words as $categories => $words) {
		                for ($j=0; $j <sizeof($words) ; $j++) { 
		                	if ($stringizer->contains($words[$j])) {
		                		$background = self::COLOR[$categories];
		                		$sentence = (array) $stringizer->replaceIncaseSensitive($words[$j], "<span style='background: {$background}'>{$words[$j]}</span>");
		                		$value[$i]['post_from'] = array_values($sentence);
		                		$tweets[$model][] = $value[$i];
		                	}
		                }
		            }
				}
			}
		}
		return $tweets;
	}


	private function addCounts($value='')
	{
		
		return null;

	}
	/**
	 * @param  array
	 * @return null
	 */
	private function saveJsonFile($data = [])
	{
		$filebase = new Filebase();
		$filebase->alertId = $this->alertId;
		$filebase->save($data);
	}


}

 ?>