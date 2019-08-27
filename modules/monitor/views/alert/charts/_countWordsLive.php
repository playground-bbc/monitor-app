<?php 
use miloschuman\highcharts\Highcharts;
use kartik\icons\Icon;
Icon::map($this, Icon::WHHG);


 ?>
<div class="well">
	<div class="row">
		<div class="col-md-12">
			<?php echo Icon::show('headphonesalt', ['style' =>'color:#d36126; font-size: 30px', 'framework' => Icon::WHHG]); ?>
		<?=  Highcharts::widget([
			'scripts' => ['modules/drilldown'],	
			  'options' => [
			  	'chart' => ['type' => 'pie'],
				'title' => ['text' => Yii::t('app',"total por palabras en LiveChat Tickets ID: # {$info_head['alertId']}")],
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
										var table = $("#live").DataTable();
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
			        		'data' => $chartWords->getSeries('countWords_live') ,
			      		],
			    ],
		      	'drilldown' => [
		      		'series' => $chartWords->getDrilldownSeries('countWords_live'),

		      	],
		      	
				'credits' => ['enabled' => false],

			  ], 
			]);
		  ?>

		</div>
	</div>
</div>