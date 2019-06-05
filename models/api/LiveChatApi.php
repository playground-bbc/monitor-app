<?php
namespace app\models\api;

use Filebase\Database as Filebase;
use LiveChat\Api\Client as LiveChat;
use Stringizer\Stringizer;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

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

    // api live chat property
    public $assignee = [];
    public $events   = [];
    public $id;
    public $requester = [];
    public $groups    = [];
    public $status;
    public $subject;
    public $modified;
    public $source        = [];
    public $opened        = [];
    public $firstResponse = [];
    public $ccs           = [];
    public $tags          = [];
    public $rate;
    public $date;
    public $currentGroup = [];

    public function setParams($params = [])
    {
        $this->params = [
            'date_from' => (isset($params['date_from'])) ? $params['date_from'] : '',
            'date_to'   => (isset($params['date_to'])) ? $params['date_to'] : '',
            'page'      => (isset($params['page'])) ? $params['page'] : 1,
            'assigned'  => (isset($params['assigned'])) ? $params['assigned'] : '',
            'order'     => (isset($params['order'])) ? $params['order'] : '',
            'status'    => (isset($params['status'])) ? $params['status'] : '',
            'assignee'  => (isset($params['assignee'])) ? $params['assignee'] : '',
            'query'     => (isset($params['query'])) ? $params['query'] : '',
            'requester' => (isset($params['requester'])) ? $params['requester'] : '',
            'group'     => (isset($params['group'])) ? $params['group'] : '',
            'source'    => (isset($params['source'])) ? $params['source'] : '', // 'lc2', 'chat-window', 'mail', 'facebook:conversation', 'facebook:post' or 'agent-app-manual'.
            'tag'       => (isset($params['tag'])) ? $params['tag'] : '',
            'tagged'    => (isset($params['tagged'])) ? $params['tagged'] : '',

        ];
    }

    public function get_number_pages_by_query($model_products = [])
    {
        $pages = [];
        $products = [];
        foreach ($model_products as $key => $product) {
           $products[] = $product['serial_model'];
           $products[] = $product['name'];
        }

        
        foreach ($products as $key => $value) {
            $this->params['query'] = $value;
            $pages[$value] = $this->_liveChat->tickets->get($this->params)->pages;
            }
    
        return $pages;
    }

    public function chatByQuery($model_products,$alertId)
    {
        $file = $this->getFilename($alertId);
        $pages = $this->get_number_pages_by_query($model_products);
        
        foreach ($pages as $product => $page) {
            $this->params['query'] = $product;
            while ($this->params['page'] <= $page) {
                $this->_data[$product] = $this->_liveChat->tickets->get($this->params);
                $this->params['page'] ++;
            }
            $this->params['page'] = 1;
        }

        $this->saveDataJson($file);
    }

    /** go through the json located in the monitor_app folder the live_chat + id file
         'SMART TV LED 49" FHD' => 
            array (size=2)
              'Palabras Positivas' => 
                array (size=3)
                  'bueno' => int 0
                  'diferencia' => int 1
                  'media noche' => int 0
              'Palabras Negativas' => 
                array (size=3)
                  'diferencia' => int 1
                  'defecto' => int 0
                  'tengo un problema' => int 0
     */
    public function searchAndCountBywords($words,$alertId)
    {
        $fileName = $this->getFilename($alertId);
        $data = $fileName->field('data');

        $tickets = [];
       
        foreach ($data as $product => $value) {
            $tickets[$product] = $value['tickets'];
        }

        $events = [];
        foreach ($tickets as $ticket => $value) {
            for ($i=0; $i < sizeof($tickets[$ticket]) ; $i++) { 
                $events[$ticket][] = $tickets[$ticket][$i]['events'];
            }
        } 

      
        $message = [];
         foreach ($events as $key => $value) {
            for ($i=0; $i < sizeof($events[$key]) ; $i++) { 
                for ($j=0; $j <sizeof($events[$key][$i]) ; $j++) { 
                    $message[$key][] = $events[$key][$i][$j];
                    
                }
            }
        } 

        $message_client = [];
        foreach ($message as $key => $value) {
            for ($i=0; $i <sizeof($value) ; $i++) { 
                if (isset($value[$i]['message'])) {
                        $message_client[$key][] = $value[$i]['message'];
                }
            }
        }
        /*foreach ($message as $key => $value) {
            for ($i=0; $i <sizeof($value) ; $i++) { 
                if (isset($value[$i]['author']['type']) && $value[$i]['author']['type']== 'client') {
                    if (isset($value[$i]['message'])) {
                        $message_client[$key][] = $value[$i]['message'];
                    }
                }
            }
        }*/


        $model_by_word = [];
        /*foreach ($message_client as $key => $value) {
            foreach ($words as $category => $word) {
                for ($i=0; $i < sizeof($value) ; $i++) { 
                    $s = new Stringizer($value[$i]);
                    for ($j=0; $j <sizeof($word) ; $j++) { 
                        $model_by_word[$key][$category][$word[$j]] = $s->containsCountIncaseSensitive($word[$j]);
                    }
                }
            }
        }*/
        foreach ($message_client as $key => $value) {
            foreach ($words as $category => $word) {
                for ($i=0; $i < sizeof($value) ; $i++) { 
                    $s = new Stringizer($value[$i]);
                    $count = 0;
                    for ($j=0; $j <sizeof($word) ; $j++) { 
                        $count += $s->containsCountIncaseSensitive($word[$j]);
                        $model_by_word[$key][$category][$word[$j]] = $count;
                    }
                }
            }
        }

        
        return $model_by_word;

    }
    /**
     * it goes through all the data and converts it into a json file
     */
    public function saveDataJson($file)
    {
        foreach ($this->_data as $key => $value) {
            $file->$key = $value;
        }
        $file->save();
    }


    public function getFilename($alertId)
    {
        $fileName = $this->_filebase->get("{$this->baseName}_{$alertId}");
        return $fileName;
    }

    public function getData()
    {
        return $this->_data;
    }

    public function __construct()
    {
        $this->_liveChat = new LiveChat(Yii::$app->params['liveChat']['apiLogin'], Yii::$app->params['liveChat']['apiKey']);
        $this->_filebase = new Filebase([
            'dir'            => Yii::getAlias('@live-chat'),
            'backupLocation' => Yii::getAlias('@backup'),
            'format'         => \Filebase\Format\Json::class,
            'cache'          => true,
            'cache_expires'  => 1800,
            'pretty'         => true,
            'safe_filename'  => true,
            'read_only'      => false,
            'validate'       => [
                /* 
                'name' => [
                    'valid.type'     => 'string',
                    'valid.required' => true,
                ],
                */
            ],
        ]);
        parent::__construct();
    }

}
