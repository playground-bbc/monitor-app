<?php 
use miloschuman\highcharts\Highcharts;
use yii\helpers\Html;
 ?>

<div class="well">
	<div class="row">
		<div class="col-md-12">
		<?php echo Html::img('@web/img/logo-awario.png', ['class' => 'img-responsive']); ?>
		<?=  Highcharts::widget([
		 	'scripts' => [
			      'modules/exporting',
			      'themes/sand-signika',
			  ],   
			'options' => [
		   	'chart' => ['type' => 'column'],
		      'title' => ['text' => Yii::t('app',"numero de palabras por diccionario en Awario ID: # {$info_head['alertId']}")],
		      	  'subtitle' => ['text' => Yii::t('app',"desde la fecha: {$info_head['start_date']} - hasta la fecha: {$info_head['end_date']}")],
		      'xAxis' => [
		         'categories' => $chartAwario->getCategories('countByCategoryInAwario'),
		         'crosshair' => true,
		      ],
		      'plotOptions' => [
				      	'series' => [
				      		'cursor' => 'pointer',
				      		'point' => [
									'events' =>[
										'click' => new \yii\web\JsExpression('function(e){
											var table = $("#awario").DataTable();
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
		         'title' => ['text' => 'Awario Data'],
		         'labels' => ['overflow' => 'justify']
		      ],
			'series' => $chartAwario->getSeries('countByCategoryInAwario'),
			'credits' => ['enabled' => false],     
		   		]
			]);
		  ?>

		</div>
	</div>
</div>
