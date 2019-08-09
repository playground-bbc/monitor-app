<?php 
namespace app\models\chart;

use yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

use app\models\api\BaseApi;


/**
 * wrapper for chart data
 */
class CountByCategory extends Model
{
	private $targets = ['countByCategoryInTweet','countByCategoryInLiveChat','countByCategoryInAwario','countByCategoryInWeb','count_category_conversations'];
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
	/**
	 * [getSeries get the series for Highcharts]
	 * @param  [string] $target [target for looking for in the model data]
	 * @return [array]          [series the data model by target]
	 */
	public function getSeries($target)
	{
		$series = [];
		$dictionaries = [];
		$color = BaseApi::COLOR;

		if (isset($this->model_data[$target])) {
				// get dictionary name
				foreach ($this->model_data[$target] as $product => $categories) {
					foreach ($categories as $category => $value) {
						if (!in_array($category, $dictionaries)) {
							$dictionaries[] = $category;
						}				
					}
				}
				// set series
				$index = 0;
				foreach ($dictionaries as $dictionary) {
					$series[$index]['name'] = $dictionary;
					$series[$index]['color'] = $color[$dictionary];
					foreach ($this->model_data[$target] as $product => $categories) {
						$series[$index]['data'][] = $categories[$dictionary];
					}
					$index++;
				}

		}
		return $series;
	}

	/**
	 * [getCategories get the categories for Highcharts]
	 * @param  [type] $target [target for looking for in the model data]
	 * @return [array]        [categories the data model by target]
	 */
	public function getCategories($target)
	{
		$products = [];
		if (isset($this->model_data[$target])) {
			// get dictionary name
			foreach ($this->model_data[$target] as $product => $categories) {
				$products[] = $product;
			}
		}
		return $products;
	}


}