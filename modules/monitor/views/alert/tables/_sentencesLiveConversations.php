<?php 
use yii\helpers\Html;
use yii\helpers\Url;

$gridColumns = [
    'product',
    'id',
    'sentence',
    'created_at',
    'author_name',
    'entity',
];

 ?>
<?= Html::a('Export Excel', ['excel-live-conversations','alertId' => $alertId,'resource_name' => 'livechat-conversations'], ['class' => 'btn btn-success','target' => '_blank']) ?>
<hr>
 <?= \nullref\datatable\DataTable::widget([
    'data' => $conversations,
    'scrollY' => '400px',
    'scrollCollapse' => true,
    'tableOptions' => [
        'class' => 'table table-striped',
    ],
    'columns' => $gridColumns,
]) ?>