<?php 
use yii\data\ArrayDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;

$target = '"_blank"';
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
                    var href = "<a href=";
                    var url = row.url;
                    var target =   " target=" + '.$target.'
                    var text = ">link</a>";
                    link = href.concat(url,target ,text);
                    return link;
                }
                return link;
                return link;
        }'),
        ]
    ],
]) ?>