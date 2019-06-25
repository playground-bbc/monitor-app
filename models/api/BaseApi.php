<?php 
namespace app\models\api;

use yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

use app\models\api\LiveChatApi;
use app\models\api\TwitterApi;
use app\models\filebase\Filebase;

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
	
	/**
	 * @param array
	 */
	function __construct($params)
	{
		foreach ($params as $key => $value) {
			$this->$key = $value;
		}
		$data = $this->getSearchTwitter();
		$tweets = $this->setSearchDataTwitter($data);
		//$this->saveJsonFile($tweets);
		$data = $this->getSearchLiveChat();
		$tickets = $this->setSearchDataTickets($data);
		$this->saveJsonFile(ArrayHelper::merge($tweets,$tickets));
		
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

        ];

        $data = [];
        $categories = array_keys($this->products_models);
        
        foreach ($categories as $key) {
        	$params['q'] = $key;
        	$data[$key] = $twitterApi->search_tweets($params);
        	if ($data[$key]['rate']['remaining'] < $this->twitter_limit) {
        		break;
        	}
        }
        foreach ($this->products_models as $key => $value) {
        	foreach ($value as $model => $serial_model) {
        		$params['q'] = $model;
        	    $data[$model] = $twitterApi->search_tweets($params);
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
				
				// get url from tweet
				if (count($value['statuses'][$i]['entities']['urls'])) {
					$tweets[$key][$source]['url'] = $value['statuses'][$i]['entities']['urls'][0]['url'];
				}
				// get created_at from tweet
				if (isset($value['statuses'][$i]['created_at'])) {
					$tweets[$key][$source]['created_at'] = $value['statuses'][$i]['created_at'];
				}
				// get author_name from tweet
				if (isset($value['statuses'][$i]['user']['name'])) {
					$tweets[$key][$source]['author_name'] = $value['statuses'][$i]['user']['name'];
				}
				// get author_username from tweet
				if (isset($value['statuses'][$i]['user']['screen_name'])) {
					$tweets[$key][$source]['author_username'] = $value['statuses'][$i]['user']['screen_name'];
				}

				// get Post from tweet
				if (isset($value['statuses'][$i]['text'])) {
					$tweets[$key][$source]['post_from'] = $value['statuses'][$i]['text'];
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
         //   'date_to' => Yii::$app->formatter->asDate($this->start_date,'yyyy-MM-dd'),
          //  'date_from' => Yii::$app->formatter->asDate($this->end_date,'yyyy-MM-dd'),
            'query' => $models_products_all,
            'page' => $this->live_chat_page,
        ];


       $data = $liveChat->loadParams($params)->getTickets()->all();
       
       return $data;


	}

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
				$model_ticket[$model][$source][$i]['created_at'] = $obj[$i]->date;
				$model_ticket[$model][$source][$i]['author_name'] = $obj[$i]->requester->name;
				$model_ticket[$model][$source][$i]['username'] = $obj[$i]->requester->mail;
				$model_ticket[$model][$source][$i]['title'] = $obj[$i]->subject;
				$model_ticket[$model][$source][$i]['url'] = $obj[$i]->source->url;
				
				// get post_from
				for ($j=0; $j <sizeof($obj[$i]->events) ; $j++) { 
					if (isset($obj[$i]->events[$j]->message)) {
						$model_ticket[$model][$source][$i]['post_from'][] = [ $obj[$i]->events[$j]->author->type => trim($obj[$i]->events[$j]->message)];
					}
				}
			}
		}

		/*var_dump($model_ticket['Full HD']['LIVECHAT'][2]);
		die();*/
		
		return $model_ticket;
		
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