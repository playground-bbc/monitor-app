<?php 

namespace app\models\filebase;

use yii;

/**
 * Filebase wrapper
 */
class Filebase 
{

	private $_filebase;
	public $alertId;

    /**
     * [save save data in a json by name to alertid]
     * @param  [array] $data [data to save]
     * @return [null] 
     */
	public function save($data)
	{
        $file = $this->getFilebase();
		foreach ($data as $key => $value) {
            $file->$key = $value;
        }
	    $file->save();
	}

    /**
     * [getFilebase return object filebase]
     * @return [obj] [Filebase\Database]
     */
    public function getFilebase()
    {
        return $this->_filebase->get($this->alertId);
    }

	function __construct()
	{
		$this->_filebase = new \Filebase\Database([
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