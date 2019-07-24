<?php 
use miloschuman\highcharts\Highcharts;



 ?>

<div class="well">
	<div class="row">
		<div class="col-md-12">
		<h1>Web Data</h1>
		<h2> Total por categoria de palabras</h2>	
		<?=  Highcharts::widget([
		 	'scripts' => [
			      'modules/exporting',
			      'themes/sand-signika',
			  ],   
			'options' => [
		   	'chart' => ['type' => 'column'],
		      'title' => ['text' => Yii::t('app','word numbers by products / dictionaries')],
		      'subtitle' => ['text' => Yii::t('app','Subtitle ....')],
		      'xAxis' => [
		         'categories' => $chartCategories->getCategories('countByCategoryInWeb'),
		         'crosshair' => true,
		      ],
		      'yAxis' => [
		         'title' => ['text' => 'Twitter Data'],
		         'labels' => ['overflow' => 'justify']
		      ],
			'series' => $chartCategories->getSeries('countByCategoryInWeb'),
			'credits' => ['enabled' => false],     
		   		]
			]);
		  ?>

		</div>
	</div>
</div>
