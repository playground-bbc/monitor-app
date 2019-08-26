<?php 
use yii\helpers\Html;
use yii\helpers\Url;

 ?>
<?= Html::a('Export Excel', ['excel-awario','alertId' => $alertId,'resource_name' => 'awario'], ['class' => 'btn btn-success','target' => '_blank']) ?>
 <hr>
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
                    var url = row.url;
                    var cadena = "<a href=";
                    
                    var cadena2 = ">link</a>";
                    link = cadena.concat(url,cadena2);
                    return link;
                }
                return link;
        }'),
        ]
    ],
   // 'withColumnFilter' => false,
]) ?>