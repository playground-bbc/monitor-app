<?php 
use yii\helpers\Html;
use yii\helpers\Url;


$target = '"_blank"';
 ?>

<?= Html::a('Export Excel', [Url::to('excel/excel'),'alertId' => $alertId,'resource_name' => 'Twitter'], ['class' => 'btn btn-success','target' => '_blank']) ?>
<hr>
 <?= \nullref\datatable\DataTable::widget([
    'id' => 'twitter',
    'data' => $sentences,
    'scrollY' => '400px',
    'scrollCollapse' => true,
    'tableOptions' => [
        'id' => 'twitter',
        'class' => 'table table-striped',
    ],
    'columns' => [
        'product',
        'source',
        'location',
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
        'followers_count',
        //'url'
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
]) ?>

<?php 
use yii\web\View; 
$this->registerJs('
    $("#twitter_wrapper").on( "init.dt", function ( e, settings ) {
    var api = new $.fn.dataTable.Api( settings );
 
    console.log( "New DataTable created:", api.table().node() );
} );
    ',
    View::POS_READY);

?>

