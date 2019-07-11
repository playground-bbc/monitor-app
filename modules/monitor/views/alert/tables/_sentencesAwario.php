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
        //'product',
        'source',
        [
            'class' => 'nullref\datatable\DataTableColumn', // can be omitted
            'data' => 'post_from',
            'title' => \Yii::t('app', 'post_from'),
            'render' => new \yii\web\JsExpression('function(data, type, row, meta) { 
                return row.post_from;
        }'),
            'sClass' => 'active-cell-css-class',
            'filter' => true,
        ],
        'created_at',
        'author_name',
        'url'
    ],
   // 'withColumnFilter' => false,
]) ?>