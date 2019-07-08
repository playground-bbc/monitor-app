<?php 
use yii\data\ArrayDataProvider;

 ?>

 <?= \nullref\datatable\DataTable::widget([
    'data' => $sentences,
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
        'post_from',
        'created_at',
        'author_name',
        'author_username',
        'url'
    ],
]) ?>