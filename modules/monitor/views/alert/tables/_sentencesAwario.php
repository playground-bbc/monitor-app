<?php 
use yii\data\ArrayDataProvider;

$data = [];
foreach ($sentences as $product => $resources) {
    foreach ($resources as $resource => $categories) {
        foreach ($categories as $category => $words) {
            $data[] = $words;
        }
    }
}
$providerAwario = new ArrayDataProvider([
  'allModels' => $data,
 // 'keys' => $data,
  'pagination' => [
          'pageSize' => 10,
      ],
    'totalCount' => count($data),
  ]);
/*
echo "<pre>";
var_dump($providerAwario->getModels());*/
 ?>

 <?= \nullref\datatable\DataTable::widget([
    'data' => $data,
    'tableOptions' => [
        'class' => 'table table-striped',
    ],
    'columns' => [
        //'product',
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