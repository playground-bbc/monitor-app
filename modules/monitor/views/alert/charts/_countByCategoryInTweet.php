<?php 
use miloschuman\highcharts\Highcharts;

 ?>

<div class="well">
	<div class="row">
		<div class="col-md-12">
		<?=  Highcharts::widget([
		 	'scripts' => [
			      'modules/exporting',
			      'themes/sand-signika',
			  ],   
			'options' => [
		   	'chart' => ['type' => 'column'],
		      'title' => ['text' => Yii::t('app','word numbers by products / dictionaries in twitter')],
		     // 'subtitle' => ['text' => Yii::t('app','Subtitle ....')],
		      'xAxis' => [
		         'categories' => $chartCategories->getCategories('countByCategoryInTweet'),
		         'crosshair' => true,
		      ],
		      'plotOptions' => [
		      	'series' => [
		      		'dataLabels' => [
		      			'enabled' => true
		      		]
		      	],
		      ],
		      'yAxis' => [
		         'title' => ['text' => 'Twitter Data'],
		         'labels' => ['overflow' => 'justify']
		      ],
			'series' => $chartCategories->getSeries('countByCategoryInTweet'),
			'credits' => ['enabled' => false],     
		   		]
			]);
		  ?>

		</div>
	</div>
</div>
