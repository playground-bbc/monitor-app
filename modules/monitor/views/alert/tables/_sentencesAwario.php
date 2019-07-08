<?php 
use yii\data\ArrayDataProvider;

$data = [];
foreach ($sentences as $product => $resources) {
    foreach ($resources as $resource => $categories) {
        foreach ($categories as $category => $words) {
            $data[] = array_shift($words);
            /*var_dump($words);*/
        }
    }
}

 ?>

 <?= \nullref\datatable\DataTable::widget([
    'data' => $data,
    'tableOptions' => [
        'class' => 'table table-striped',
    ],
    'columns' => [
        //'product',
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
                return row.post_from;
        }'),
        ],
        'created_at',
        'author_name',
        'url'
    ],
]) ?>