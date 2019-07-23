<?php 
use yii\data\ArrayDataProvider;
use yii2tech\spreadsheet\Spreadsheet;





/*$exporter = new Spreadsheet([
    'dataProvider' => new ArrayDataProvider([
        'allModels' => $data
    ]),
    'columns' => [
        [
            'attribute' => 'source',
        ],
        [
            'attribute' => 'url',

        ],
        [
            'attribute' => 'created_at',

        ],
        [
            'attribute' => 'author_name',

        ],
        [
            'attribute' => 'author_username',

        ],
        [
            'attribute' => 'post_from',

        ],
        [
            'attribute' => 'product',
            'contentOptions' => [
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                ],
            ],
        ],
        
        
    ],
]);

$exporter->save('C:\wamp64\www\playground\monitor-app\monitor-app-data\live-chat/file.xls');
*/


 ?>

 <?= \nullref\datatable\DataTable::widget([
    'data' => $sentences,
    /*'scrollY' => '400px',
    'scrollCollapse' => true,*/
    'tableOptions' => [
        'class' => 'table table-striped',
    ],
    'columns' => [
        'product',
        /*[
            'data' => 'active',
            'title' => \Yii::t('app', 'Is active'),
            'filter' => ['true' => 'Yes', 'false' => 'No'],
        ],*/
        'source',
        [
            'data' => 'post_from',
            'title' => \Yii::t('app', 'post_from'),
            'render' => new \yii\web\JsExpression('function(data, type, row, meta) { 
                return row.post_from[0];
        }'),
        ],
        'created_at',
        'author_name',
        'author_username',
        'url'
    ],
]) ?>