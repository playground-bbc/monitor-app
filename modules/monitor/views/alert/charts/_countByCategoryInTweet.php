<?php 
use miloschuman\highcharts\Highcharts;
use kartik\icons\Icon;
Icon::map($this, Icon::FAB);


 ?>

<div class="well">
	<div class="row">
		<div class="col-md-12">
		<?php echo Icon::show('twitter', ['class'=>'fa-2x','style' =>'color:#00ACEE', 'framework' => Icon::FAB]); ?>	
		<?=  Highcharts::widget([
		 	'scripts' => [
			      'modules/exporting',
			      'themes/sand-signika',
			  ],   
			'options' => [
		   	'chart' => ['type' => 'column'],
		      'title' => ['text' => Yii::t('app',"number of words by type of dictionary in twitter ID: # {$info_head['alertId']}")],
		      'subtitle' => ['text' => Yii::t('app',"From date: {$info_head['start_date']} - To date: {$info_head['end_date']}")],
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
