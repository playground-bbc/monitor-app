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
		      'title' => ['text' => Yii::t('app',"numero de palabras por diccionario en Web Page Tickets ID: # {$info_head['alertId']}")],
		      	'subtitle' => ['text' => $url],
		      'xAxis' => [
		         'categories' => $chartCategories->getCategories('countByCategoryInWeb'),
		         'crosshair' => true,
		      ],
		      'plotOptions' => [
		      	'series' => [
		      		'cursor' => 'pointer',
		      		'point' => [
							'events' =>[
								'click' => new \yii\web\JsExpression('function(e){
									var table = $("#web").DataTable();
									table.search(this.category).draw();
								}
								
								'),
							],
						],
		      		'dataLabels' => [
		      			'enabled' => true
		      		]
		      	],
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
