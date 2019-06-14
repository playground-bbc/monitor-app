<?php
namespace app\modules\monitor\controllers;

use yii;
use yii\web\Controller;
use app\models\ProductsFamily;
use yii\helpers\ArrayHelper;


/**
 * Default controller for the `monitor` module
 */
class DefaultController extends Controller
{

	/*public function actions()
	{
		return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
	}*/
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


	/*public function actionError()
	{
	    $exception = Yii::$app->errorHandler->exception;
	    if ($exception !== null) {
	        return $this->render('error', ['exception' => $exception]);
	    }
	}*/

}
