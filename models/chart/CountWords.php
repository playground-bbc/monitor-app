<?php 


namespace app\models\chart;

use yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

use app\models\api\BaseApi;



/**
 * wraper count words chart
 */
class CountWords extends Model
{
	private $targets = ['countWords','countWords_live'];
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
		if (isset($this->model_data[$target])) {
			// get product name
			$index = 0;
			foreach ($this->model_data[$target] as $product => $categories) {
				$series[$index]['name'] = str_replace('"', " ", $product);
				$total = 0;
				foreach ($categories as $category => $value) {
					$total += array_sum($value);				
				}
				$series[$index]['y'] = $total;
				$series[$index]['drilldown'] = str_replace('"', " ", $product);
				$index++;
			}

		}
		
		return $series;
	}


	public function getDrilldownSeries($target)
	{
		$series = [];
		if (isset($this->model_data[$target])) {
			// get product name
			$index = 0;
			foreach ($this->model_data[$target] as $product => $categories) {
				$series[$index]['name'] = '';
				$series[$index]['id'] = str_replace('"', " ", $product);
				$data =[];
				$string = '';
				foreach ($categories as $category => $values) {
					$string = "{$category}: ";
					foreach ($values as $word => $count) {
						$data[] = [$string."{$word}",$count];
					}
					$series[$index]['data'] = $data;			
				}
				$index++;
			}

		}
		return $series;
	}


}