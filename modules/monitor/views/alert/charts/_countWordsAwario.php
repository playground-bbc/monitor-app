<?php 
use miloschuman\highcharts\Highcharts;
use yii\helpers\Html;

 ?>
<div class="well">
	<div class="row">
		<div class="col-md-12">
		<?php echo Html::img('@web/img/logo-awario.png', ['class' => 'img-responsive']); ?>
		<?=  Highcharts::widget([
			'scripts' => ['modules/drilldown'],	
			  'options' => [
			  	'chart' => ['type' => 'pie'],
			  	'title' => ['text' => Yii::t('app',"total por palabras en Awario ID: # {$info_head['alertId']}")],
				'subtitle' => ['text' => Yii::t('app',"desde la fecha: {$info_head['start_date']} - hasta la fecha: {$info_head['end_date']}")],
				'plotOptions' =>  [
					'series' => [
						'cursor' => 'pointer',
						'point' => [
							'events' =>[
								'click' => new \yii\web\JsExpression('function(e){
									var point_name = e.point.name;
									if(point_name !== null){
										var name = point_name.split(":");
										var table = $("#awario").DataTable();
										table.search(name[1]).draw();
									}
								}
								
								'),
							],
						],
						'dataLabels' => [
							'enabled' => true,
			                'format' => "{point.name}: {point.y} <br/> {point.percentage:.1f}%"
						],
					],

				],
				'tooltip' =>[
					'headerFormat' => '<span style="font-size:11px">{series.name}</span><br>',
			        'pointFormat' => '<span style="color:{point.color}">{point.name}</span> = <b>{point.y}</b> of total<br/> {point.percentage:.1f}%'
				],
			    'series' => [
			      		[
			      			'name' => "Browsers",
			        		'colorByPoint' => true,
			        		'data' => $chartWords->getSeries('countWords_awario') ,
			      		],
			    ],
		      	'drilldown' => [
		      		'series' => $chartWords->getDrilldownSeries('countWords_awario'),

		      	],
		      	
				'credits' => ['enabled' => false],

			  ], 
			]);
		  ?>

		</div>
	</div>
</div>