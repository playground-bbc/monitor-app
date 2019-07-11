<?php 
use yii\data\ArrayDataProvider;



 ?>

 <?= \nullref\datatable\DataTable::widget([
    'data' => $sentences,
    'scrollY' => '400px',
    'scrollCollapse' => true,
    'tableOptions' => [
        'class' => 'table table-striped',
    ],
    'columns' => [
        'product',
        'title',
        /*[
            'data' => 'active',
            'title' => \Yii::t('app', 'Is active'),
            'filter' => ['true' => 'Yes', 'false' => 'No'],
        ],*/
        'source',
        'sentence',
        'created_at',
        'author_name',
        'entity',
        'status',
        'url_retail',
       // 'url',
    ],
]) ?>