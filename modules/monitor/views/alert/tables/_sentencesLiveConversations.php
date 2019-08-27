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
<?= Html::a('Export Excel', [Url::to('excel/excel'),'alertId' => $alertId,'resource_name' => 'Live Chat Conversations'], ['class' => 'btn btn-success','target' => '_blank']) ?>
<hr>
 <?= \nullref\datatable\DataTable::widget([
    'id' => 'live_conversation',
    'data' => $conversations,
    'scrollY' => '400px',
    'scrollCollapse' => true,
    'tableOptions' => [
        'id' => 'live_conversation',
        'class' => 'table table-striped',
    ],
    'columns' => $gridColumns,
]) ?>