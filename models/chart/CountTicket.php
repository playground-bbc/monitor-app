<?php 
namespace app\models\chart;

use yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

use app\models\api\BaseApi;


/**
 * wrapper for chart live
 */
class CountTicket extends Model
{
	private $targets = ['total'];
	private $model_data;


	//chart type
	
	
	function __construct($model)
	{
		foreach ($model as $targets => $headers) {
			foreach ($this->targets as $target) {

				if (in_array($target, array_keys($headers))) {
					$this->model_data[$target] = ArrayHelper::getValue($headers, $target);
				}
			}
		}
		
	}

	public function getSeries($target)
	{
		$series = [];
		$index = 0;
		if (isset($this->model_data[$target])) {
			foreach ($this->model_data[$target] as $target => $value) {
				$series[$index] = [$target,$value];
				$index++;
			}

		}
		return $series;
	}



}

?>