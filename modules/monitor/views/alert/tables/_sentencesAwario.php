<?php 
use yii\helpers\Html;
use yii\helpers\Url;


$target = '"_blank"';
 ?>
<?= Html::a('Export Excel', [Url::to('excel/excel'),'alertId' => $alertId,'resource_name' => 'awario'], ['class' => 'btn btn-success','target' => '_blank']) ?>
 <hr>
 <?= \nullref\datatable\DataTable::widget([
    'id' => 'awario',
    'data' => $sentences,
    'scrollY' => '400px',
    'scrollCollapse' => true,
    'tableOptions' => [
        'id' => 'awario',
        'class' => 'table table-striped',
    ],
    'columns' => [
        //'product',
        'source',
        'title',
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
        [
            'data' => 'url',
            'render' => new \yii\web\JsExpression('function(data, type, row, meta) { 
                var link = "-";
                if(row.url.length > 1){
                    var href = "<a href=";
                    var url = row.url;
                    var target =   " target=" + '.$target.'
                    var text = ">link</a>";
                    link = href.concat(url,target ,text);
                    return link;
                }
                return link;
        }'),
        ]
    ],
   // 'withColumnFilter' => false,
]) ?>