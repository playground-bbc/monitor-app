<?php 
use yii\helpers\Html;
use yii\helpers\Url;

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

 ?>
<?= Html::a('Export Excel', [Url::to('excel/excel'),'alertId' => $alertId,'resource_name' => 'Live Chat'], ['class' => 'btn btn-success','target' => '_blank']) ?>
<hr>
 <?= \nullref\datatable\DataTable::widget([
    'id' => 'live',
    'data' => $sentences,
    'scrollY' => '400px',
    'scrollCollapse' => true,
    'tableOptions' => [
        'id' => 'live',
        'class' => 'table table-striped',
    ],
    'columns' => [
        'product',
        'title',
        'source',
        'sentence',
        'created_at',
        'author_name',
        'entity',
        'status',
        'url_retail'
    ],
]) ?>