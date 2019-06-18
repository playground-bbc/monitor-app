<?php 
namespace app\models\api;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\base\ErrorException;

require_once(Yii::getAlias('@vendor').'\autoload.php'); // call google client
/**
 * class that will be in charge of synchronizing the products with google drive of the document called Drive Listening Dictionary
 */
class DriveProductsApi extends Model
{
	private $_data;

	private $_productsFamily = ['HA','HE','MC','Monitores y proyectores'];
	private $_headers = [
		'CATEGORÍA' => 'ProductsFamily',
		'SUBCATEGORÍA' => 'ProductsFamily',
		'MODELO' => 'ProductCategory',
		'PRODUCTO' => 'Products',
		'CÓDIGO' => 'ProductsModels',
	];

	/**
	 *   $models = [
			'products' => [
				'CATEGORÍA' => 'HA',
				'SUBCATEGORÍA' => ['refrigerador','some else'],
				'MODELO' => ['Top freezer','Bottom freezer'],
				'PRODUCTO' => [
					'Bottom Freezer con capacidad total de 277 L' => [
						'codigo1','codigo2'
					],
					'Bottom Freezer con capacidad total de 318 L' => [
						'codigo1','codigo2'
					]
				],
				[
				'CATEGORÍA' => 'HE',
				'SUBCATEGORÍA' => ['refrigerador','some else'],
				'MODELO' => ['Top freezer','Bottom freezer'],
				'PRODUCTO' => [
					'Bottom Freezer con capacidad total de 277 L' => [
						'codigo1','codigo2'
					],
					'Bottom Freezer con capacidad total de 318 L' => [
						'codigo1','codigo2'
					]
				]

			]
	 	]
	 */
	public function orderByHeaders($values)
	{
		$models = [];

		$index = 0;
		foreach ($values as $family => $property) {
			for ($i=0; $i <sizeof($property) ; $i++) { 
				$models['products'][$index]['CATEGORÍA'] = $family;
			}
			$models['products'][$index]['SUBCATEGORÍA'] = [];
			for ($j=1; $j <sizeof($property) ; $j++) {
				if (!in_array($property[$j][1], $models['products'][$index]['SUBCATEGORÍA'])) {
				 	$models['products'][$index]['SUBCATEGORÍA'][] = $property[$j][1];
				 } 
				
			}
			/*$models['products'][$index]['MODELO'] = [];
			for ($k=1; $k <sizeof($property) ; $k++) {
				if (!in_array($property[$k][2], $models['products'][$index]['MODELO'])) {
				 	$models['products'][$index]['MODELO'][] = $property[$k][2];
				 } 
				
			}

			$models['products'][$index]['PRODUCTO'] = [];
			for ($p=1; $p <sizeof($property) ; $p++) {
				if (!in_array($property[$p][3], $models['products'][$index]['PRODUCTO'])) {
				 	$models['products'][$index]['PRODUCTO'][$property[$p][3]][] = $property[$p][4];
				 } 
				
			}*/

			$index++;
		}
		
		var_dump($models['products']);
		die();

	}

	public function getContentDocument()
	{
		// Get the API client and construct the service object.
		$client = $this->getClient();
		$service = new \Google_Service_Sheets($client);

		$spreadsheetId = Yii::$app->params['drive']['Drive Diccionario Listening'];

		$response = $service->spreadsheets->get($spreadsheetId);
		$sheetNames = $this->_productsFamily;

		$values = [];
		foreach ($sheetNames as $id => $sheetName) {
			$response = $service->spreadsheets_values->get($spreadsheetId, $sheetName);
			$values[$sheetName] = $response->getValues();
		}

		/*var_dump($values);
		die();*/
		$this->orderByHeaders($values);
		
		/*try {
			$this->orderByHeaders($values);
		} catch (ErrorException $e) {
		    Yii::error("Error in the Document :( ");
		}*/
		//return $values;

	}

	private function getClient()
	{
		$client = new \Google_Client();
		$http = new \GuzzleHttp\Client([
		    'verify' => 'c:\cert\cacert.pem'
		]);
		$client->setHttpClient($http);
	    $client->setAuthConfig(Yii::getAlias('@drive_account'));
	    $client->setApplicationName('monitor-app');
	    $client->setScopes(\Google_Service_Sheets::SPREADSHEETS_READONLY);

		return $client;
		
	}
	
}



 ?>