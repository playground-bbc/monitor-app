<?php
namespace app\modules\monitor\controllers;

use yii;
use yii\web\Controller;
use app\models\ProductsFamily;
use app\models\api\DriveProductsApi;
use yii\helpers\ArrayHelper;


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

	public function actionDrive()
	{
		
		return $this->render('drive/index');
	}

	public function actionSync()
	{
		if (Yii::$app->request->post()) {
			$drive = new DriveProductsApi();
			$data = $drive->getContentDocument();
		}
		return $this->render('drive/index');
	}

	
	public function actionError()
	{
	    $exception = Yii::$app->errorHandler->exception;
	    if ($exception !== null) {
	        return $this->render('error', ['exception' => $exception]);
	    }
	}

}
