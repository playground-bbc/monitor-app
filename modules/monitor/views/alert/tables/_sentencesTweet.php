<?php 
use yii\helpers\Html;
use yii\helpers\Url;

 ?>

<?= Html::a('Export Excel', [['excel/excel-tweets'],'alertId' => $alertId,'resource_name' => 'tweets'], ['class' => 'btn btn-success','target' => '_blank']) ?>
<hr>
 <?= \nullref\datatable\DataTable::widget([
    'data' => $sentences,
    'scrollY' => '400px',
    'scrollCollapse' => true,
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

