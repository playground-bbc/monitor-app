<?php
namespace app\models\api;

use Filebase\Database as Filebase;
use LiveChat\Api\Client as LiveChat;
use Stringizer\Stringizer;
use Yii;
use yii\web\HttpException;
use yii\base\Model;
use yii\helpers\ArrayHelper;



use app\models\ProductsModels;
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

        $this->alertId = $params['alertId'];
        $params['date_from'] = ($params['date_from']) ?  Yii::$app->formatter->asDate($params['date_from'],'yyyy-MM-dd') : '';
        $params['date_to'] = ($params['date_to']) ? Yii::$app->formatter->asDate($params['date_to'],'yyyy-MM-dd'): '';
        unset($params['alertId']);

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
        
        $file = $this->_filebase->get("{$this->baseName}_{$this->alertId}");
        foreach ($this->data as $key => $value) {
            $file->$key = $value;
        }
        $file->save();
    }

    public function getTicketsByProduct($product='')
    {
        
        $data = ArrayHelper::map($this->_filebase->query()->select("{$product}.tickets")->results(),'0',"{$product}.tickets");
        $value = ArrayHelper::getValue($data, '');

        return $value;

    }

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
        $this->_filebase = new \Filebase\Database([
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
