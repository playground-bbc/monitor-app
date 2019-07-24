<?php 
use yii\widgets\DetailView;
use app\models\ProductsModelsAlerts;
use app\models\Dictionary;

var_dump($alert->start_date);
var_dump(Yii::$app->formatter->asDate($alert->end_date,'yyyy-MM-dd')
);

echo DetailView::widget([
    'model' => $alert,
    'attributes' => [
       'start_date:datetime', // creation date formatted as datetime
        'end_date:datetime', // end date formatted as datetime           
       // 'description:html',    // description attribute in HTML
        [     
            'format' => 'html',                 // the owner name of the model
            'label' => 'requested resources',
            'value' => function ($model)
            {
                $html = '';
                foreach ($model->alertResources as $alert => $resources) {
                    for ($i=0; $i <sizeof($resources->resources) ; $i++) { 
                        $html .= " <span class='label label-info'>{$resources->resources[$i]->name}</span>";
                    }
                }
                return $html;
            },
        ],
        [     
            'format' => 'html',                 // the owner name of the model
            'label' => 'products consulted',
            'value' => function ($model)
            {
                
                $productModels = ProductsModelsAlerts::find()->where(['alertId' => $model->id])->all();
                $html = '';
                $products = [];
                for ($i=0; $i <sizeof($productModels) ; $i++) { 
                    $products[] = $productModels[$i]->productModel->product->name;
                }
                $products = array_unique($products);
                foreach ($products as $key => $value) {
                    $html .= " <span class='label label-success'>{$value}</span>";
                }
                
                return $html;
            },
        ],

        [
            'format' => 'html',                 // the owner name of the model
            'label' => 'Palabras Libres',
            'value' => function ($model)
            {
                $words = Dictionary::find()->where(['alertId' => $model->id,'category_dictionaryId' => 1])->select('word')->all();
                $html = '';
                for ($i=0; $i <sizeof($words) ; $i++) { 
                   $html .= " <span class='label label-warning'>{$words[$i]->word}</span>";
                }
                return $html;
            }

        ],
        
    ],
]);
 ?>