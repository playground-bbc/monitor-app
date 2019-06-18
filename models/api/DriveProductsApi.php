<?php 
namespace app\models\api;

use Yii;
use yii\base\Model;

require_once(Yii::getAlias('@vendor').'\autoload.php'); // call google client
/**
 * class that will be in charge of synchronizing the products with google drive of the document called Drive Listening Dictionary
 */
class DriveProductsApi extends Model
{

	public function actionDrive()
	{
		// Get the API client and construct the service object.
		$client = $this->getClient();
		$service = new \Google_Service_Sheets($client);

		$spreadsheetId = Yii::$app->params['drive']['Drive Diccionario Listening'];

		$response = $service->spreadsheets->get($spreadsheetId);
		$sheetNames = ArrayHelper::map($response['sheets'],'properties.index','properties.title');

		$values = [];
		foreach ($sheetNames as $id => $sheetName) {
			$response = $service->spreadsheets_values->get($spreadsheetId, $sheetName);
			$values[] = $response->getValues();
		}

		var_dump($values);
		die();	

	}

	public function getClient()
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