<?php 
use miloschuman\highcharts\Highcharts;
use kartik\icons\Icon;
Icon::map($this, Icon::WHHG);

 ?>

<div class="well">
	<div class="row">
		<div class="col-md-12">
		<?php echo Icon::show('websitealt', ['style' =>'color:black; font-size: 30px', 'framework' => Icon::WHHG]); ?>
		<?=  Highcharts::widget([
		 	'scripts' => [
			      'modules/exporting',
			      'themes/sand-signika',
			  ],   
			'options' => [
		   	'chart' => ['type' => 'column'],
		      'title' => ['text' => Yii::t('app',"number of words by type of dictionary in Web Page Tickets ID: # {$info_head['alertId']}")],
		      	'subtitle' => ['text' => $url],
		      'xAxis' => [
		         'categories' => $chartCategories->getCategories('countByCategoryInWeb'),
		         'crosshair' => true,
		      ],
		      'yAxis' => [
		         'title' => ['text' => 'Web Data'],
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
