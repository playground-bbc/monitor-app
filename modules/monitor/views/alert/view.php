<?php
/* @var $this yii\web\View */
use miloschuman\highcharts\Highcharts;
use yii\web\JsExpression;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
echo "<pre>";
var_dump($model);
die();
echo "</pre>";

?>

<div class="container">
	<div class="row">
		<div class="col-md-12">
			<h1>Tweets Data</h1>
			<h2> total por categoria de palabras</h2>
			<?php if (ArrayHelper::keyExists('tweets', $model, false)): ?>
				<?php if (!is_null($model['tweets']['countByCategoryInTweet'])): ?>
					<?php $categories = [];
					foreach ($model['tweets']['countByCategoryInTweet'] as $products => $category) {
						$categories[] = array_keys($category);
					}
				 ?>
					<table class="table table-striped">
		              <thead>
		                <tr>
		                  <th>Productos</th>
		                  <?php for ($i=0; $i <sizeof($categories[0]) ; $i++) :?>
		                  	<th><?= $categories[0][$i]  ?></th>
		                  <?php endfor; ?>	
		                </tr>
		              </thead>
		              <tbody>
		                <?php foreach ($model['tweets']['countByCategoryInTweet'] as $model => $categories): ?>
		                	<tr>
			                  <td><?= $model ?></td>
			                  <?php foreach ($categories as $key => $value): ?>
			                  	<td><?= $value  ?></td>
			                  <?php endforeach ?>
			                </tr>
		                	
		                <?php endforeach ?>
		              </tbody>
		            </table>
				<?php else: ?>
					<p class="text-warning">No se encontraron coincidencias en la busqueda</p>
				<?php endif ?>
			<?php endif ?>
		</div>		
	</div>
</div>
