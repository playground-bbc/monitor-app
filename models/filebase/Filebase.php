<?php 

namespace app\models\filebase;

use yii;
use Filebase\Database;
/**
 * Filebase wrapper
 */
class Filebase 
{

	private $_filebase;
	public $alertId;

	public function save($data)
	{
        $file = $this->_filebase->get("{$this->alertId}");
		foreach ($data as $key => $value) {
            $file->$key = $value;
        }
	    $file->save();
	}
	
	function __construct()
	{
		$this->_filebase = new Database([
            'dir'            => Yii::getAlias('@resources'),
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
	}
}