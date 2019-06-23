<?php
namespace app\models\api;

use app\models\ProductCategory;
use app\models\Products;
use app\models\ProductsFamily;
use app\models\ProductsModels;
use Yii;
use yii\base\Model;

require_once Yii::getAlias('@vendor') . '/autoload.php'; // call google client
/**
 * class that will be in charge of synchronizing the products with google drive of the document called Drive Listening Dictionary
 */
class DriveProductsApi extends Model
{
    private $_data;

    private $_productsFamily = ['HA', 'HE', 'MC', 'Monitores y proyectores'];
    //private $_productsFamily = ['MC'];

    public function SaveToDatabase($values)
    {
        for ($i = 0; $i < sizeof($values); $i++) {
            for ($j = 1; $j < sizeof($values[$i]); $j++) {
                $id         = $this->saveFamily(trim($values[$i][$j][0]));
                $familyId   = $this->saveParent($id, trim($values[$i][$j][1]));
                $categoryId = $this->saveProductCategory($familyId, trim($values[$i][$j][2]));
                $productId  = $this->saveProduct($categoryId, trim($values[$i][$j][3]));
                $modelId    = $this->saveProductsModel($productId, trim($values[$i][$j][4]));
            }
        }

    }

    public function getContentDocument()
    {
        $service = $this->_getServices();

        $spreadsheetId = Yii::$app->params['drive']['Drive Diccionario Listening test'];

        $sheetNames = $this->_productsFamily;

        $values = [];

        foreach ($sheetNames as $id => $sheetName) {
            $response = $service->spreadsheets_values->get($spreadsheetId, $sheetName);
            $values[] = $response->getValues();
        }
      
        $this->SaveToDatabase($values);

        /*try {
        $this->orderByHeaders($values);
        } catch (ErrorException $e) {
        Yii::error("Error in the Document :( ");
        }*/
        //return $values;

    }

    public function getContentDictionaryByTitle($sheetNames = [])
    {
        $service = $this->_getServices();

        $spreadsheetId = Yii::$app->params['drive']['Drive Diccionario Listening test'];
        $response      = $service->spreadsheets->get($spreadsheetId);

        $values = [];
        foreach ($sheetNames as $sheetName) {
            $response           = $service->spreadsheets_values->get($spreadsheetId, $sheetName);
            $values[$sheetName] = $response->getValues();
        }
        if (count($values)) {
            foreach ($values as $key => $words) {
                for ($i = 0; $i < sizeof($words); $i++) {
                    $values[$key][$i] = trim($words[$i][0]);
                }
            }
        }

        return (count($values)) ? $values : null;

    }

    public function getTitleDictionary()
    {
        // Get the API client and construct the service object.
        $service = $this->_getServices();

        $spreadsheetId = Yii::$app->params['drive']['Drive Diccionario Listening test'];
        $response      = $service->spreadsheets->get($spreadsheetId);
        $sheetNames    = [];
        for ($i = 0; $i < sizeof($response->sheets); $i++) {
            if (!in_array($response->sheets[$i]->properties->title, $this->_productsFamily)) {
                $sheetName[$i] = $response->sheets[$i]->properties->title;
            }
        }
        return (count($sheetName)) ? $sheetName : null;

    }

    public function saveFamily($value)
    {
        $params = [
            'abbreviation_name' => $value,
        ];
        $model = ProductsFamily::findOne($params);
        if (is_null($model)) {
            $model                    = new ProductsFamily;
            $model->name              = $value;
            $model->abbreviation_name = $value;
            $model->save();
        }
        return $model->id;
    }

    public function saveParent($familyId, $value)
    {
        $params = [
            'parentId' => $familyId,
            'name'     => $value,
        ];
        $model = ProductsFamily::findOne($params);
        if (is_null($model)) {
            $model           = new ProductsFamily;
            $model->parentId = $familyId;
            $model->name     = $value;
            $model->save();
            $familyId = $model->id;
        }
        return $model->id;
    }

    public function saveProductCategory($familyId, $value)
    {
        $params = [
            'familyId' => $familyId,
            'name'     => $value,
        ];
        $model = ProductCategory::find()->where($params)->one();
        if (is_null($model)) {
            $model           = new ProductCategory;
            $model->familyId = $familyId;
            $model->name     = $value;
            $model->save();
        }
        return $model->id;
    }

    public function saveProduct($categoryId, $value)
    {
        $params = [
            'categoryId' => $categoryId,
            'name'       => $value,
        ];
        $model = Products::findOne($params);
        if (is_null($model)) {
            $model             = new Products;
            $model->categoryId = $categoryId;
            $model->name       = $value;
            $model->save();
        }
        return $model->id;

    }

    public function saveProductsModel($productId, $value)
    {
        $params = [
            'productId'    => $productId,
            'serial_model' => $value,
        ];
        $model = ProductsModels::findOne($params);
        if (is_null($model)) {
            $model               = new ProductsModels;
            $model->productId    = $productId;
            $model->serial_model = $value;
            $model->save();
            $modelId = $model->id;
        }
        return $model->id;
    }

    private function _getClient()
    {
        $client = new \Google_Client();
        $http   = new \GuzzleHttp\Client([
            //'verify' => 'c:\cert\cacert.pem'
        ]);
        $client->setHttpClient($http);
        $client->setAuthConfig(Yii::getAlias('@drive_account'));
        $client->setApplicationName('monitor-app');
        $client->setScopes(\Google_Service_Sheets::SPREADSHEETS_READONLY);

        return $client;

    }

    private function _getServices()
    {
        // Get the API client and construct the service object.
        $client  = $this->_getClient();
        $service = new \Google_Service_Sheets($client);
        return $service;
    }

}
