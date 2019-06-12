<?php
namespace app\models\api;

use Filebase\Database as Filebase;
use LiveChat\Api\Client as LiveChat;
use Stringizer\Stringizer;
use Yii;
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


    

    public function loadParams($params)
    {

        $this->alertId = $params['alertId'];
        $params['date_from'] = Yii::$app->formatter->asDate($params['date_from'],'yyyy-MM-dd');
        $params['date_to'] = Yii::$app->formatter->asDate($params['date_to'],'yyyy-MM-dd');
        unset($params['alertId']);

        foreach ($params as $key => $value) {
            $this->params[$key] = $value; 
        }

        return $this;
    }

    public function getTickets()
    {
        $params = $this->params;
        var_dump($params);
        foreach ($this->params['query'] as $key => $value) {
            do {
                $page = $this->params['page'];
                $params['query'] = $value;
                $this->data[$value][$page] = $this->_liveChat->tickets->get($params);
                $this->params['page'] ++;

                

            } while ($this->params['page'] <= $this->data[$value][$page]->pages);

            $this->params['page'] = 1;
        }

        var_dump($this->data);
        die();
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
