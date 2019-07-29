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
</div>
