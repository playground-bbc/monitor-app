<?php 
namespace app\models\api;

use yii\base\Model;


/**
 * Wrap class for call other api models
 */
class BaseApi extends Model
{
	public $words;
	public $products_models;
	public $resources;
	public $start_date;
	public $end_date;
	
	function __construct($params)
	{
		foreach ($params as $key => $value) {
			$this->$key = $value;
		}
	}
}

 ?>