<?php
namespace app\models\api;

//use Filebase\Database as Filebase;
use Yii;
use LiveChat\Api\Client as LiveChat;
use Stringizer\Stringizer;
use yii\web\HttpException;
use yii\base\Model;
use yii\helpers\ArrayHelper;


use app\models\Dictionary;
use app\models\ProductsModels;
use app\models\filebase\Filebase;
use app\models\ProductsCategories;
use app\models\CategoriesDictionary;

/**
 * LiveChatApi is the model behind the login API.
 *
 */
class LiveChatApi extends Model
{
    private $_liveChat;
    private $_filebase;
    private $_data;

    public $baseName = 'live-chat';
    public $params   = [];
    public $data;

    // own Base search
    const LIVE_CHAT_COVERSATIONS = 'chat';
    const LIVE_CHAT_TICKETS = 'Ticket';
    const LIVECHAT = 'LIVECHAT';
    
    public $alertId;

    public $totalByQuery = [];

    
    public $exclude = [
        'resolutionDate',
        'firstResponse',
        'ccs',
        'tags',
        'rate',
        'currentGroup',
        'opened',
        'modified',
        'groups'

    ];

    
    /**
     * [loadParams load params to the class]
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function loadParams($params)
    {

        $params['date_from'] = (isset($params['date_from'])) ?  Yii::$app->formatter->asDate($params['date_from'],'yyyy-MM-dd') : '';
        $params['date_to'] = (isset($params['date_to'])) ? Yii::$app->formatter->asDate($params['date_to'],'yyyy-MM-dd'): '';

        
        foreach ($params as $key => $value) {
            $this->params[$key] = $value; 
        }

        return $this;
    }
    /**
     * [getTickets get tickets in live chats by params]
     * @return [type] [description]
     */
    public function getTickets()
    {
        $params = $this->params;
        
        foreach ($this->params['query'] as $key => $value) {
            do {
                
                $page = $this->params['page'];
                $params['query'] = $value;
                $params['page'] = $page;
                $this->_data[$value][$page] = $this->_get($params);
                $this->params['page'] ++;

                

            } while ($this->params['page'] <= $this->_data[$value][$page]->pages);

            $this->params['page'] = 1;
        }
        
        $this->_orderbyTicket();
        return $this;
    }

    /**
     * [all return all the data]
     * @return [type] [description]
     */
    public function all()
    {   

        return $this->data;
    }
    /**
     * [_orderbyTicket order the structure the data]
     * @return [type] [description]
     */
    private function _orderbyTicket()
    {
        foreach ($this->_data as $key => $value) {
            foreach ($value as $obj => $property) {
               //$this->data[$key]['pages'] = $property->pages;
               $this->data[$key]['total'] = $property->total;
               foreach ($property->tickets as $ticket ) {
                   $data = $this->_exclude($ticket);
                   $this->data[$key]['tickets'][]= $data;
               }
            }
        }

    }
    /**
     * [_exclude eclude the data that will not be used]
     * @param  [type] $ticket [description]
     * @return [type]         [description]
     */
    private function _exclude($ticket)
    {
        $data = [];
        for ($i=0; $i <sizeof($this->exclude) ; $i++) { 
            if (property_exists($ticket, $this->exclude[$i])) {
                $property = $this->exclude[$i];
                unset($ticket->$property);
            }
        }
        $data = $ticket;
        return $data;
    }
    /**
     * [_get check the status api and proced by the call]
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    private function _get($params)
    {
        if ($this->_liveChat->status->get()) {
            return $this->_liveChat->tickets->get($params);
        }
        throw new HttpException(404, "The requested Api {$this->baseName} could not be found.");
    }

    private function _getChat($params){
        if ($this->_liveChat->status->get()) {
            return $this->_liveChat->chats->get($params);
        }
        throw new HttpException(404, "The requested Api {$this->baseName} could not be found.");
    }
    /**
     * [saveJson save in json file]
     * @return [type] [description]
     */
    public function saveJson()
    {
        
        $this->_filebase->alertId = $this->alertId;
        $this->_filebase->save($this->data);
    }

    /**
     * [getChats description]
     * @param  [type] $params [ params for search chat]
     * @return [type]         [call to api liveChat and call _orderbyChat to order data]
     */
    public function getChats($params,$alertId){

        $chats = [];
        foreach ($params['query'] as $key => $value) {
            do {
                
                $page = $params['page'];
                $params['query'] = $value;
                $params['page'] = $page;

                $this->_data[$value][$page] = $this->_getChat($params);
                $params['page'] ++;

                

            } while ($params['page'] <= $this->_data[$value][$page]->pages);

            $params['page'] = 1;
        }

        $chats = $this->_orderbyChat();
        return $chats;
    }
    /**
     * [_orderbyChat order chat by query and id from conversations]
     * @return [array] [return model if there conversations or false isnt not]
     */
    private function _orderbyChat(){
        $chats = [];

        foreach ($this->_data as $product => $objects){
            foreach ($objects as $object => $property){
                $index = 0;
                for ($c =0; $c < sizeof($property->chats) ; $c++){
                    $chats[$product][$index]['source'] = 'LIVECHAT';
                    $chats[$product][$index]['id'] = $property->chats[$c]->id;
                    $chats[$product][$index]['type'] = $property->chats[$c]->type;
                    $chats[$product][$index]['created_at'] = $property->chats[$c]->started;
                    $chats[$product][$index]['author_name'] = $property->chats[$c]->visitor->name;
                    $chats[$product][$index]['author_name'] = $property->chats[$c]->visitor->name;
                    $chats[$product][$index]['retail'] = parse_url($property->chats[$c]->chat_start_url,PHP_URL_HOST);
                    for($m =0; $m < sizeof($property->chats[$c]->messages) ; $m++){
                        $chats[$product][$index]['post_from'][] = $property->chats[$c]->messages[$m];
                    }// for each messages in chat
                    $index++;
                }// for each group of chats
            }// for each object stdClass
        }// for each product
        
        return (!empty($chats)) ? $chats : [];
    }

    /**
     * [countByCategoryInLiveChatConversations return the count the words find in a category for dictionary]
     * @param  [array] $data  [data to the json]
     * @param  [array] $words [list de dictionaries and words]
     * @return [array]        [return the count to words finded by dictionary]
     */
    public function countByCategoryInLiveChatConversations($data,$words){
        // set array analisys Model => dictionary_title => word
        $products = array_keys($data);
        //only liveChat conversations
        unset($products['WEB']);
        unset($products['AWARIO']);

        $countByCategory = [];
        for ($i=0; $i <sizeof($products) ; $i++) { 
            foreach ($words as $categories => $word) {
                for ($j=0; $j <sizeof($word) ; $j++) { 
                    $countByCategory[$products[$i]][$categories] = 0;
                }
            }
        }
        // lets find out
        set_time_limit(500);
        foreach ($data as $model => $value) {
            for ($i=0; $i <sizeof($value) ; $i++) { 
                if (($value[$i]['source'] == self::LIVECHAT) && ($value[$i]['type'] == self::LIVE_CHAT_COVERSATIONS) ) {
                    // count by post_form
                    for ($p=0; $p <sizeof($value[$i]['post_from']) ; $p++) { 
                        if($value[$i]['post_from'][$p]['user_type'] == 'visitor'){
                            $stringizer = new Stringizer($value[$i]['post_from'][$p]['text']);
                            foreach ($words as $categories => $word) {
                                for ($j=0; $j <sizeof($word) ; $j++) { 
                                    if ($stringizer->containsCountIncaseSensitive($word[$j])) {
                                        $countByCategory[$model][$categories] += $stringizer->containsCountIncaseSensitive($word[$j]) ;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return (count($countByCategory)) ? $countByCategory : null;

    }
    /**
     * [addWordsInLiveChatConversations add words will find in the sencestes the array]
     * @param [array] $data  [data in the json]
     * @param [array] $words [list de dictionaries and words]
     * @return [array]        [return the count to words finded by word]
     */
    public function addWordsInLiveChatConversations($data, $words){

        // set array analisys Model => dictionary_title => word
        $products = array_keys($data);
        //only liveChat conversations
        unset($products['WEB']);
        unset($products['AWARIO']);

        $countByWords = [];
        for ($i=0; $i <sizeof($products) ; $i++) { 
            foreach ($words as $categories => $word) {
                for ($j=0; $j <sizeof($word) ; $j++) { 
                    $countByWords[$products[$i]][$categories][$word[$j]] = 0;
                }
            }
        }
        set_time_limit(500);
        foreach ($data as $products => $value) {
            for ($i=0; $i <sizeof($value) ; $i++) { 
                if (($value[$i]['source'] == self::LIVECHAT) && ($value[$i]['type'] == self::LIVE_CHAT_COVERSATIONS) ) {
                    for ($p=0; $p <sizeof($value[$i]['post_from']) ; $p++) { 
                        if($value[$i]['post_from'][$p]['user_type'] == 'visitor'){
                            $stringizer = new Stringizer($value[$i]['post_from'][$p]['text']);
                            foreach ($words as $categories => $word) {
                                for ($j=0; $j <sizeof($word) ; $j++) { 
                                    if ($stringizer->containsCountIncaseSensitive($word[$j])) {
                                        $countByWords[$products][$categories][$word[$j]] += $stringizer->containsCountIncaseSensitive($word[$j]);
                                    }
                                }
                            }
                        }// only client
                    } // for each post_from
                } // only livechat conversations
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

        return (count($data)) ? $data : null;
    }

    /**
     * [addTagsSentenceFoundInConversations add tags html to each word in the sentence]
     * @param [array] $data  [data in the json]
     * @param [array] $words [list de dictionaries and words]
     * @param [array] $color [ color hex html to mark the word]
     * @return [array] $live [ data mark with the word finded]
     */
    public function addTagsSentenceFoundInConversations($data, $words,$color){
        $live = [];
        
        foreach ($data as $model => $value) {
            for ($i=0; $i <sizeof($value) ; $i++) { 
                if (($value[$i]['source'] == self::LIVECHAT) && ($value[$i]['type'] == self::LIVE_CHAT_COVERSATIONS) ) {
                
                    //check by post_from
                    for ($p=0; $p <sizeof($value[$i]['post_from']) ; $p++) { 
                        $stringizer_sentences = new Stringizer($value[$i]['post_from'][$p]['text']);
                        $tmp = [];
                        foreach ($words as $categories => $word) {
                            $background = $color[$categories];
                            for ($j=0; $j <sizeof($word) ; $j++) {
                                if ($stringizer_sentences->containsCountIncaseSensitive($word[$j])) {
                                    
                                    if($value[$i]['post_from'][$p]['user_type'] == 'visitor'){
                                        $sentence = (array) $stringizer_sentences->replaceIncaseSensitive($word[$j], "<span style='background: {$background}'>{$word[$j]}</span>");
                                        $sentence = array_values($sentence);
                                        $tmp['sentence'] = $sentence[0];
                                    }else{
                                        $tmp['sentence'] = $value[$i]['post_from'][$p]['text'];
                                    }

                                    $tmp['id'] = $value[$i]['id'];
                                    $tmp['created_at'] = $value[$i]['created_at'];
                                    $tmp['sentence_said'] = $value[$i]['post_from'][$p]['text'];
                                    $tmp['author_name'] = $value[$i]['post_from'][$p]['author_name'];
                                    $tmp['entity'] = $value[$i]['post_from'][$p]['user_type'];
                                    $tmp['product'] = $model;
                                    
                                }// end if contains word
                            }// for each words
                            
                        } // for each dictionaries
                        if ((!empty($tmp)) && (!in_array($tmp,$live)) )
                        {
                            $live[] = $tmp;
                        }                     
                    } // for each post_from
                } // if livechat source
                
            }
        }
        return $live;
    }

    public function getTicketsByProduct($product='')
    {
        
        $baseName = $this->baseName;
        $data = ArrayHelper::map($this->_filebase->get()->query()->select("{$product}.{$baseName}.tickets")->results(),'0',"{$product}.{$baseName}.tickets");
        $value = ArrayHelper::getValue($data, '');

        return $value;

    }

    public function getWordsToSearch()
    {
        $dictionaries = Dictionary::find()->where(['alertId'=> $this->alertId])->with('category')->asArray()->all();
        $words = ArrayHelper::map($dictionaries,'id','word','category.name');
        return $words;
    }

    public function countTicketsAll()
    {
        $tickets =  $this->_liveChat->tickets->get($params = array());
        return $tickets->total;
    }

    public function gettotalByQuery()
    {
        return $this->totalByQuery;
    }



    public function __construct($params = [])
    {
        $this->_liveChat = new LiveChat(Yii::$app->params['liveChat']['apiLogin'], Yii::$app->params['liveChat']['apiKey']);
        $this->_filebase = new Filebase();

        parent::__construct();
    }

}
