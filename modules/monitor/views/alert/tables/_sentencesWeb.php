<?php 
use yii\data\ArrayDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
 ?>
<?= Html::a('Export Excel', [Url::to('excel/excel-web'),'alertId' => $alertId,'resource_name' => 'web'], ['class' => 'btn btn-success','target' => '_blank']) ?>
<hr>
 <?= \nullref\datatable\DataTable::widget([
    'id' => 'web',
    'data' => $sentences,
    'scrollY' => '400px',
    'scrollCollapse' => true,
    'tableOptions' => [
        'id' => 'web',
        'class' => 'table table-striped',
    ],
    'columns' => [
        'product',
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
        'tag',
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
]) ?>