<?php 
namespace app\models\api;

use Yii;
use yii\base\Model;
use LiveChat\Api\Client as LiveChat;
use Filebase\Database as Filebase;
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
    public $params = [];
    public $data;


    // api live chat property
    public $assignee = [];
    public $events = [];
    public $id;
    public $requester = [];
    public $groups = [];
    public $status;
    public $subject;
    public $modified;
    public $source = [];
    public $opened = [];
    public $firstResponse = [];
    public $ccs = [];
    public $tags = [];
    public $rate;
    public $date;
    public $currentGroup = [];


    public function getByParams($params)
    {
      $params = [
            //'status' => 'solved',
            //'query' => 'malo',
            'source' => 'chat-window', // 'lc2', 'chat-window', 'mail', 'facebook:conversation', 'facebook:post' or 'agent-app-manual'.
            'page' => '1'

        ];
        $tickets = $this->_liveChat->tickets->get($params);
        return $tickets;
    }

    public function saveByname($name='')
    {
      $item = $this->_filebase->get($this->baseName);
      $item->name = $name;
      $item->save();
    }
    


    public function __construct() {
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
        'validate' => [
        'name'   => [
        'valid.type' => 'string',
        'valid.required' => true
            ]
           ]
      ]);
       parent::__construct();
    }

       
}



 ?>