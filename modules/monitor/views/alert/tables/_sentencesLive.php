<?php 
use yii\data\ArrayDataProvider;

use kartik\export\ExportMenu;
$gridColumns = [
    'product',
    'title',
    'source',
    'sentence',
    'created_at',
    'author_name',
    'entity',
    'status',
    'url_retail',
];

$provider = new ArrayDataProvider([
    'allModels' => $sentences,
    
]);

// Renders a export dropdown menu
echo ExportMenu::widget([
    'dataProvider' => $provider,
    'columns' => $gridColumns
]);

 ?>

 <?= \nullref\datatable\DataTable::widget([
    'data' => $sentences,
    'scrollY' => '400px',
    'scrollCollapse' => true,
    'tableOptions' => [
        'class' => 'table table-striped',
    ],
    'columns' => $gridColumns,
]) ?>