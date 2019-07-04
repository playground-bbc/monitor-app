<?php 

use miloschuman\highcharts\Highcharts;


 ?>


<div class="row">
	<div class="col-md-12">
		 <?php 
 	echo Highcharts::widget([
	 	'scripts' => [
		      'modules/exporting',
		      'themes/sand-signika',
		  ],   
		'options' => [
	   	'chart' => ['type' => 'column'],
	      'title' => ['text' => Yii::t('app','word numbers by products / dictionaries')],
	      'subtitle' => ['text' => Yii::t('app','Subtitle ....')],
	      'xAxis' => [
	         'categories' => $chartCategories->getCategories('countByCategoryInTweet'),
	         'crosshair' => true,
	      ],
	      'yAxis' => [
	         'title' => ['text' => 'Twitter Data'],
	         'labels' => ['overflow' => 'justify']
	      ],
	     'tooltip' => [
	     	'valueSuffix' => ' und',
	     	'headerFormat'=> '<span style="font-size:10px">{point.key}</span><table>',
	        
	        'footerFormat'=> '</table>',
	        'footerFormat'=> true,
	        'useHTML'=> true
	     ],
		'series' => $chartCategories->getSeries('countByCategoryInTweet'),
		'credits' => ['enabled' => false],     
	   		]
		]);

	  ?>

	</div>
</div>
<br>
<div class="row">
	<div class="col-md-12">
		 <?php 
		  	echo Highcharts::widget([
			'options' => [
			'scripts' => [
			      'themes/unica',
			  ],	
		   	'chart' => ['type' => 'column'],
			      'title' => ['text' => Yii::t('app','word numbers by products / dictionaries')],
			      'xAxis' => [
			         'categories' => $chartCategories->getCategories('countByCategoryInLiveChat'),
			      ],
			      'yAxis' => [
			         'title' => ['text' => 'Live Chat Data']
			      ],
			      'series' => $chartCategories->getSeries('countByCategoryInLiveChat'),
			'credits' => ['enabled' => false],     
		   		]
			]);

		   ?>
	</div>
</div>