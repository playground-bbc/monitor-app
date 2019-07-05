<?php 
use miloschuman\highcharts\Highcharts;

 ?>

<div class="well">
	<div class="row">
		<div class="col-md-12">
		<h1>Live Data</h1>
		<h2> Total por categoria de palabras</h2>	
		<?=  Highcharts::widget([
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
