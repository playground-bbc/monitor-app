<?php 
use yii\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;
 ?>


 <div class="container">
 	<?=$this->render('charts/_countByCategoryInTweet',['chartCategories' => $chartCategories]); ?>
 </div>
 <div class="container">
 	
 	<?php 

 		$providerTwitter = new ArrayDataProvider([
	      'allModels' => $model['tweets']['sentences'],
	      'keys' => array_keys($model['tweets']['sentences']),
	      'pagination' => [
	              'pageSize' => 10,
	          ],
	        'totalCount' => count($model['tweets']['sentences']),
	      ]);


 	 ?>

 	 <?= \nullref\datatable\DataTable::widget([
	    'data' => $providerTwitter->getModels(),
	    'columns' => [
	        'product',
	        'source',
	        'post_from',
	        'created_at',
	        'author_name',
	        'author_username',
	        'url',
	    ],
	]) ?>
 </div>

