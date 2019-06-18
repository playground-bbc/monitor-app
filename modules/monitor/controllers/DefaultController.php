<?php
namespace app\modules\monitor\controllers;

use yii;
use yii\web\Controller;
use app\models\ProductsFamily;
use yii\helpers\ArrayHelper;


require_once(Yii::getAlias('@vendor').'\autoload.php');
//require_once Yii::getAlias('@vendor').'\google\apiclient\src\Google\Client.php'  ;




/**
 * Default controller for the `monitor` module
 */
class DefaultController extends Controller
{

	public function actions()
	{
		return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
	}

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        
        return $this->render('index');
	}

	public function actionCreate()
	{
		return $this->render('create');
	}

	public function actionCreatefamily()
	{
		$model = new ProductsFamily();
		$parents = ArrayHelper::map(ProductsFamily::find()->where(['or', ['parentId' => 0], ['parentId' => null]])->all(),'id','name'); 

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return null;
		}

		return $this->render('family/create',[
			'model' => $model,
			'parents' => $parents,
		]);
	}

	public function actionDrive()
	{
		// Get the API client and construct the service object.
		
		$client = $this->getClient();
		$service = new \Google_Service_Sheets($client);

		// Prints the names and majors of students in a sample spreadsheet:
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

		return $this->render('drive/index');
	}


	public function getClient()
	{
		$client = new \Google_Client();
		$http = new \GuzzleHttp\Client([
		    'verify' => 'c:\cert\cacert.pem'
		]);
		$client->setHttpClient($http);
	    $client->setAuthConfig(Yii::getAlias('@drive_account'));
	    $client->setApplicationName('Drive Diccionario Listening');
	    $client->setScopes(\Google_Service_Sheets::SPREADSHEETS_READONLY);
	    
	  
		return $client;
		
	}

	public function actionError()
	{
	    $exception = Yii::$app->errorHandler->exception;
	    if ($exception !== null) {
	        return $this->render('error', ['exception' => $exception]);
	    }
	}

}
