<?php
/* @var $this yii\web\View */
use miloschuman\highcharts\Highcharts;
use yii\web\JsExpression;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;
?>

<div class="container">
	<!-- Tweets -->
	<?php if (ArrayHelper::keyExists('tweets', $model, false)): ?>
	<div class="well">
		<div class="row">
			<div class="col-md-12">
				<h1>Tweets Data</h1>
				<h2> Total por categoria de palabras</h2>
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
			                <?php foreach ($model['tweets']['countByCategoryInTweet'] as $data => $categories): ?>
			                	<tr>
				                  <td><?= $data ?></td>
				                  <?php foreach ($categories as $key => $value): ?>
				                  	<td><?= $value  ?></td>
				                  <?php endforeach ?>
				                </tr>
			                <?php endforeach ?>
			              </tbody>
			            </table>
					<?php endif ?>
			</div>
			<div class="col-md-12">
				<h2> Total por palabras</h2>
				<?php if (isset($model['tweets']['countWords'])): ?>
					<table class="table">
		              <thead>
		                <tr>
		                 <th>Product</th>
		                 <th>Type Dictionary</th>
		                 <th>Word</th>	
		                 <th>total</th>
		                 <!-- <th>Word</th>	
		                 <th>total</th>	 -->
		                </tr>
		              </thead>
		              <tbody>
		                <tr class="">
		                <?php foreach ($model['tweets']['countWords'] as $key => $value): ?>
			                  <td><?= $key  ?></td>
			                  <td>
				                  <?php foreach ($value as $array => $category): ?>
										<li><?= $array ?></li>
				                  <?php endforeach ?>
			                  </td>
			                  <td>
			                  	<?php foreach ($value as $array => $category): ?>
			                  		<?php foreach ($category as $word => $total): ?>
			                  			<li><?= $word  ?></li>
			                  		<?php endforeach ?>
			                  	<?php endforeach ?>
			                  </td>
			                  <td>
			                  	<?php foreach ($value as $array => $category): ?>
			                  		<?php foreach ($category as $word => $total): ?>
			                  			<li><?= $total  ?></li>
			                  		<?php endforeach ?>
			                  	<?php endforeach ?>
			                  </td>
			            </tr>
		                <?php endforeach ?>
		              </tbody>
		            </table>
		        <?php else: ?>
					<p class="text-warning">No se encontraron coincidencias en la busqueda</p>   
				<?php endif ?>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<h2> Coincidencias en oraciones</h2>
					<?php if (ArrayHelper::keyExists('tweets', $model, false)):  ?>
						<?php if (isset($model['tweets']['sentences'])): ?>
							<?php 
								$providerTwitter = new ArrayDataProvider([
						          'allModels' => $model['tweets']['sentences'],
						          'keys' => array_keys($model['tweets']['sentences']),
						          'pagination' => [
						                  'pageSize' => 10,
						              ],
						            'totalCount' => count($model['tweets']['sentences']),
						          ]);

								echo GridView::widget([
								        'dataProvider' => $providerTwitter,
								        'columns' => [
								            ['class' => 'yii\grid\SerialColumn'],

								            [
								            	'label' => 'products',
								            	'value' => function ($model, $key, $index, $grid)
								            	{
								            		
								            		return $model['product'];
								            	}
								            ],
								            
								            [
								            	'label' => 'source',
								            	'value' => function ($model, $key, $index, $grid)
								            	{
								            		
								            		return $model['source'];
								            	}
								            ],
								            [
								            	'label' => 'post_from',
								            	'format' => 'html',
								            	'value' => function ($model, $key, $index, $grid)
								            	{
								            		
								            		return array_shift($model['post_from']);
								            	}
								            ],
								            
								            [
								            	'label' => 'created_at',
								            	'value' => function ($model, $key, $index, $grid)
								            	{
								            		
								            		return $model['created_at'];
								            	}
								            ],
								            [
								            	'label' => 'author_name',
								            	'value' => function ($model, $key, $index, $grid)
								            	{
								            		
								            		return $model['author_name'];
								            	}
								            ],  
											[
								            	'label' => 'author_username',
								            	'value' => function ($model, $key, $index, $grid)
								            	{
								            		
								            		return $model['author_username'];
								            	}
								            ],
								            [
								            	'label' => 'url',
								            	'format' => 'html',
								            	'value' => function ($model, $key, $index, $grid)
								            	{
								            		return Html::a('<span class="glyphicon glyphicon-share"></span>', 
								                        $model['url'], ['target' => '_blank']);
								            		
								            	},
								            	'contentOptions' => ['style' => 'width: 80px;max-width: 80px'],
								            ],


								            ],
								        'tableOptions' =>['class' => 'table table-striped table-bordered'],    
								    ]);

							 ?>
						<?php endif ?>
					<?php else: ?>
					<p class="text-warning">No se encontraron coincidencias en la busqueda</p>	
					<?php endif ?>
			</div>
		</div>
	</div>
	<?php endif ?>	
	<!-- /Tweets -->
	<!-- Livechat -->
	<?php if (ArrayHelper::keyExists('liveChat', $model, false)): ?>
		<div class="well">
			<div class="row">
				<div class="col-md-12">
					<h1>Live Chat Data</h1>
					<h2> Total por categoria de palabras</h2>
						<?php if (!is_null($model['liveChat']['countByCategoryInLiveChat'])): ?>
							<?php $categories = [];
							foreach ($model['liveChat']['countByCategoryInLiveChat'] as $products => $category) {
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
				                <?php foreach ($model['liveChat']['countByCategoryInLiveChat'] as $data => $categories): ?>
				                	<tr>
					                  <td><?= $data ?></td>
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
				</div>
				<div class="col-md-12">
					<h2> Total por palabras</h2>
					<?php if (isset($model['tweets']['countWords'])): ?>
						<table class="table">
			              <thead>
			                <tr>
			                  <th>Product</th>
			                  <th>Type Dictionary</th>
			                  <th>Word</th>	
			                  <th>total</th>
			                </tr>
			              </thead>
			              <tbody>
			               <tr class="">
			                <?php foreach ($model['liveChat']['countWords_live'] as $key => $value): ?>
				                  <td><?= $key  ?></td>
				                  <td>
					                  <?php foreach ($value as $array => $category): ?>
											<li><?= $array ?></li>
					                  <?php endforeach ?>
				                  </td>
				                  <td>
				                  	<?php foreach ($value as $array => $category): ?>
				                  		<?php foreach ($category as $word => $total): ?>
				                  			<li><?= $word  ?></li>
				                  		<?php endforeach ?>
				                  	<?php endforeach ?>
				                  </td>
				                  <td>
				                  	<?php foreach ($value as $array => $category): ?>
				                  		<?php foreach ($category as $word => $total): ?>
				                  			<li><?= $total  ?></li>
				                  		<?php endforeach ?>
				                  	<?php endforeach ?>
				                  </td>
				            </tr>
			                <?php endforeach ?>
			              </tbody>
			            </table>
			         <?php else: ?>
						<p class="text-warning">No se encontraron coincidencias en la busqueda</p>   
					<?php endif ?>
				</div>
				<div class="col-md-12">
					<h2> Coincidencias en oraciones</h2>
						<?php if (ArrayHelper::keyExists('liveChat', $model, false)):  ?>
							<?php if (!is_null($model['liveChat']['sentences_live'])): ?>
								<?php 

									$providerLive = new ArrayDataProvider([
							          'allModels' => $model['liveChat']['sentences_live'],
							          'keys' => array_keys($model['liveChat']['sentences_live']),
							          'pagination' => [
							                  'pageSize' => 10,
							              ],
							            'totalCount' => count($model['liveChat']['sentences_live']),
							          ]);

									echo GridView::widget([
									        'dataProvider' => $providerLive,
									        'columns' => [
									            ['class' => 'yii\grid\SerialColumn'],

									            [
									            	'label' => 'products',
									            	'value' => function ($model, $key, $index, $grid)
									            	{
									            		
									            		return $model['product'];
									            	}
									            ],
									            
									            [
									            	'label' => 'source',
									            	'value' => function ($model, $key, $index, $grid)
									            	{
									            		
									            		return $model['source'];
									            	}
									            ],
									            [
									            	'label' => 'post_from',
									            	'format' => 'html',
									            	'value' => function ($model, $key, $index, $grid)
									            	{
									            		
									            		return array_shift($model['post_from']);
									            	}
									            ],
									            [
									            	'label' => 'created_at',
									            	'value' => function ($model, $key, $index, $grid)
									            	{
									            		
									            		return $model['created_at'];
									            	}
									            ],
									            [
									            	'label' => 'author_name',
									            	'value' => function ($model, $key, $index, $grid)
									            	{
									            		
									            		return $model['author_name'];
									            	}
									            ],
									            [
								            	'label' => 'url',
								            	'format' => 'html',
								            	'value' => function ($model, $key, $index, $grid)
								            	{
								            		return Html::a('<span class="glyphicon glyphicon-share"></span>', 
								                        $model['url'], ['target' => '_blank']);
								            		
								            	},
								            	'contentOptions' => ['style' => 'width: 80px;max-width: 80px'],
								            ],
 



									            ],

									    ]);

								 ?>
							<?php endif ?>
						<?php endif ?>
				</div>
				<div class="col-md-12">
					<h2> Total Tickets</h2>
					<?php if (ArrayHelper::keyExists('total',$model['liveChat'],false)): ?>
						<table class="table table-condensed">
			              <thead>
			                <tr>
			                  <?php foreach ($model['liveChat']['total'] as $key => $value): ?>
			                  		<th><?= $key  ?></th>
			                  <?php endforeach ?>
			              </thead>
			              <tbody>
			                	<tr>
				                <?php foreach ($model['liveChat']['total'] as $key => $value): ?>
					                  <td><?= $value  ?></td>
				                <?php endforeach ?>
				                </tr>
			              </tbody>
			            </table>
					<?php endif ?>
				</div>
			</div>
		</div>
	<?php endif ?>
	<!-- /Livechat -->
	<?php if (ArrayHelper::keyExists('awario',$model)): ?>
		<!-- Awario -->
		<div class="well">
			<div class="row">
				<div class="col-md-12">
					<h1>Awario Data</h1>
					<h2> Total por categoria de palabras en redes sociales</h2>

					<?php 
						$countByCategoryInAwario = ArrayHelper::getValue($model['awario'],'countByCategoryInAwario');
						if (count($countByCategoryInAwario)):
						 	$products = array_shift($countByCategoryInAwario);
						 	$categories = array_shift($products);
							$category = array_keys($categories);
					?>
						 	
						<table class="table table-hover">
			              <thead>
			                <tr>
			                  <th>Productos</th>
			                  <th>Source</th>
				                  <?php for ($i=0; $i <sizeof($category) ; $i++) :?>
				                  	<th><?= $category[$i]  ?></th>
				                  <?php endfor ?>
			                </tr>
			              </thead>
			              <tbody>
			                  <?php foreach ($model['awario']['countByCategoryInAwario'] as $product => $resources): ?>
				                <tr>
				                  	<td><?=  $product ?></td>
					                <td>
					                	<?php foreach ($resources as $resource => $categories): ?>
							                 <li> <?= $resource ?></li>
					                  	<?php endforeach ?>	
					                </td>
				                  		<?php foreach ($resources as $resource => $categories): ?>
				                  			<td>
				                  				<?php foreach ($categories as $category => $words): ?>
				                  					<li style="list-style:none;"><?= count($words) ?></li>
				                  				<?php endforeach ?>
				                  			</td>
				                  		<?php endforeach ?>
				                </tr>
			                  <?php endforeach ?>
			              </tbody>
		                </table>
		                <?php else: ?>
							<p class="text-warning">No se encontraron coincidencias en la busqueda</p>
						<?php endif ?>
					
				</div>
				<div class="col-md-12">
					<h2>Coincidencias en oraciones</h2>
					<?php 
						$data = [];
						foreach ($model['awario']['countByCategoryInAwario'] as $product => $resources) {
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
						echo GridView::widget([
					        'dataProvider' => $providerAwario,
					        'columns' => [
					            ['class' => 'yii\grid\SerialColumn'],

					           /* [
					            	'label' => 'products',
					            	'value' => function ($model, $key, $index, $grid)
					            	{
					            		
					            		return $model[0]['product'];
					            	}
					            ],*/
					            
					            [
					            	'label' => 'source',
					            	'value' => function ($model, $key, $index, $grid)
					            	{
					            		
					            		return $model[0]['source'];
					            	}
					            ],
					            [
					            	'label' => 'post_from',
					            	'format' => 'html',
					            	'value' => function ($model, $key, $index, $grid)
					            	{
					            		
					            		return $model[0]['post_from'];
					            	}
					            ],
					            [
					            	'label' => 'url',
					            	'value' => function ($model, $key, $index, $grid)
					            	{
					            		
					            		return $model[0]['url'];
					            	}
					            ],
					            [
					            	'label' => 'created_at',
					            	'value' => function ($model, $key, $index, $grid)
					            	{
					            		
					            		return $model[0]['created_at'];
					            	}
					            ],
					            [
					            	'label' => 'author_name',
					            	'value' => function ($model, $key, $index, $grid)
					            	{
					            		
					            		return $model[0]['author_name'];
					            	}
					            ],  


					            ],

					    ]);

					 ?>
				</div>
			</div>
		</div>
		<!-- /Awario -->
	<?php endif ?>
</div>
