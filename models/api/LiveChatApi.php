<?php
namespace app\models\api;

//use Filebase\Database as Filebase;
use Yii;
use LiveChat\Api\Client as LiveChat;
use Stringizer\Stringizer;
use yii\web\HttpException;
use yii\base\Model;
use yii\helpers\ArrayHelper;


use app\models\ProductsModels;
use app\models\filebase\Filebase;
use app\models\ProductsCategories;
use app\models\CategoriesDictionary;
use app\models\Dictionary;

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
    
    public $alertId;

    
    public $exclude = [
        'resolutionDate',
        'firstResponse',
        'ccs',
        'tags',
        'rate',
        'currentGroup'

    ];

    

    public function loadParams($params)
    {

        $params['date_from'] = ($params['date_from']) ?  Yii::$app->formatter->asDate($params['date_from'],'yyyy-MM-dd') : '';
        $params['date_to'] = ($params['date_to']) ? Yii::$app->formatter->asDate($params['date_to'],'yyyy-MM-dd'): '';

        foreach ($params as $key => $value) {
            $this->params[$key] = $value; 
        }

        return $this;
    }

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


    public function all()
    {   

        return $this->data;
    }

    public function saveJson()
    {
        
        $this->_filebase->alertId = $this->alertId;
        $this->_filebase->save($this->data);
    }

    public function orderTicketsByWordsMentioned()
    {
        
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

    private function _orderbyTicket()
    {
        foreach ($this->_data as $key => $value) {
            foreach ($value as $obj => $property) {
               //$this->data[$key]['pages'] = $property->pages;
               $this->data[$key][$this->baseName]['total'] = $property->total;
               foreach ($property->tickets as $ticket ) {
                   $data = $this->_exclude($ticket);
                   $this->data[$key][$this->baseName]['tickets'][]= $data;
               }
            }
        }

    }

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

    private function _get($params)
    {

        if ($this->_liveChat->status->get()) {
            return $this->_liveChat->tickets->get($params);
        }
        throw new HttpException(404, "The requested Api {$this->baseName} could not be found.");
    }

    public function __construct()
    {
        $this->_liveChat = new LiveChat(Yii::$app->params['liveChat']['apiLogin'], Yii::$app->params['liveChat']['apiKey']);
        $this->_filebase = new Filebase();

        parent::__construct();
    }

}
