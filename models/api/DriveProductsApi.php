<?php
namespace app\models\api;

use Yii;
use yii\base\Model;
use yii\base\ErrorException;

use app\models\Products;
use app\models\ProductsModels;
use app\models\ProductsFamily;
use app\models\ProductCategory;
use app\models\CategoriesDictionary;

require_once Yii::getAlias('@vendor') . '/autoload.php'; // call google client
/**
 * class that will be in charge of synchronizing the products with google drive of the document called Drive Listening Dictionary
 */
class DriveProductsApi extends Model
{
    private $_data;

    private $_productsFamily = ['HA', 'HE', 'MC', 'Monitores y proyectores'];

    /**
     * [SaveToDatabase calls distins function to save in database]
     * @param [array] $values [all products from dictionaries]
     */
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
    /**
     * [getContentDocument calls services google document and read values]
     * @return [function or ErrorException] [ call function to save in database otherwise raise errorException]
     */
    public function getContentDocument()
    {
        $service = $this->_getServices();

        $spreadsheetId = Yii::$app->params['drive']['Drive Diccionario Listening'];

        $sheetNames = $this->_productsFamily;

        $values = [];

        foreach ($sheetNames as $id => $sheetName) {
            try {
                $response = $service->spreadsheets_values->get($spreadsheetId, $sheetName);
            }catch(\Google_Service_Exception $e){
                throw new \yii\web\NotFoundHttpException(Yii::t('app','houston we have a problem, drive api has problems .·´¯`(>▂<)´¯`·.'));
            }
            $values[] = $response->getValues();
        }

        try {
            $this->SaveToDatabase($values);
        }catch (ErrorException $e){
            throw new \yii\web\NotFoundHttpException(Yii::t('app','houston we have a problem, problem in the drive document ლ(ಠ_ಠლ)   '));
        }

    }
    /**
     * [getContentDictionaryByTitle get the content dictionary by title]
     * @param  array  $sheetNames [array with names dictionaries] dictionaries
     * @return [array]             [content of the dictionary]
     */
    public function getContentDictionaryByTitle($sheetNames = [])
    {
        $service = $this->_getServices();

        $spreadsheetId = Yii::$app->params['drive']['Drive Diccionario Listening'];
        try {
          $response = $service->spreadsheets->get($spreadsheetId);
        } catch (\Google_Service_Exception $e) {
          throw new \yii\web\NotFoundHttpException(Yii::t('app','houston we have a problem, drive api has problems .·´¯`(>▂<)´¯`·.'));
        }

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
    /**
     * [getTitleDictionary get the title dictionaries in drive]
     * @return [array] [titles dictionaries]
     */
    public function getTitleDictionary()
    {
        // Get the API client and construct the service object.
        $service = $this->_getServices();

        $spreadsheetId = Yii::$app->params['drive']['Drive Diccionario Listening'];
        
        try {
          $response = $service->spreadsheets->get($spreadsheetId);
          
        } catch (\Google_Service_Exception $e) {

          throw new \yii\web\NotFoundHttpException(Yii::t('app','houston we have a problem, drive api has problems .·´¯`(>▂<)´¯`·.'));
        }
        $sheetNames    = [];
        for ($i = 0; $i < sizeof($response->sheets); $i++) {
            if (!in_array($response->sheets[$i]->properties->title, $this->_productsFamily)) {
                $title = $response->sheets[$i]->properties->title;
                $sheetName[$title] = $title;
            }
        }
        if (count($sheetName)) {
            $this->saveCategoriesDictionary($sheetName);
        }
        return (count($sheetName)) ? $sheetName : null;

    }
    /**
     * [saveFamily save in database family of products]
     * @param  [string] $value [family name]
     * @return [int]           [family id]
     */
    public function saveFamily($value)
    {
        $value = $this->delete_quotation_marks($value);
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
    /**
     * [saveParent save parent in the family]
     * @param  [int] $familyId [id of the family records]
     * @param  [string] $value [string to save]
     * @return [int]           [id of new recors]
     */
    public function saveParent($familyId, $value)
    {
        $value = $this->delete_quotation_marks($value);

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
    /**
     * [saveProductCategory save in category table]
     * @param  [int] $familyId  [familyId of the record]
     * @param  [string] $value  [string to save]
     * @return [int]            [id of new record category]
     */
    public function saveProductCategory($familyId, $value)
    {
        $value = $this->delete_quotation_marks($value);

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
    /**
     * [saveProduct save the product in to respective category]
     * @param  [int] $categoryId [id the category]
     * @param  [string] $value   [value to save]
     * @return [int]             [id of new record product]
     */
    public function saveProduct($categoryId, $value)
    {   
        $value = $this->delete_quotation_marks($value);

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
    /**
     * [saveProductsModel save the model in to respective product]
     * @param  [int] $productId [id the product]
     * @param  [string] $value  [string to save]
     * @return [int]            [id of the new record]
     */
    public function saveProductsModel($productId, $value)
    {   
        $value = $this->delete_quotation_marks($value);

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
    /**
     * [saveCategoriesDictionary save new category if not exist]
     * @param  [string] $categoryTitles [ name of the new category dictionary]
     * @return [null]                 [description]
     */
    public function saveCategoriesDictionary($categoryTitles)
    {
        foreach ($categoryTitles as $category) {
            $model = CategoriesDictionary::findOne(['name' => $category]);
            if (is_null($model)) {
               $model = new CategoriesDictionary();
               $model->name = $category;
               $model->save();
            }
        }
    }
    /**
     * [_getClient get google client]
     * @return [object] [client to call to api google drive]
     */
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
    /**
     * [_getServices get google service sheets]
     * @return [obj] [service to call function style sheets]
     */
    private function _getServices()
    {
        // Get the API client and construct the service object.
        $client  = $this->_getClient();
        $service = new \Google_Service_Sheets($client);
        return $service;
    }
    /**
     * [delete_quotation_marks delete the quotations marks in the string]
     * @param  [string] $string [string to sanitaze]
     * @return [string]         [sanitaze string]
     */
    public function delete_quotation_marks($string) { 
        $result = str_replace(array('\'', '"'), '', $string);
        return $result; 
    } 

}
