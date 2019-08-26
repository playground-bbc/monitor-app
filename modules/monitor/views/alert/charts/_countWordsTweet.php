<?php 
use miloschuman\highcharts\Highcharts;
use kartik\icons\Icon;
Icon::map($this, Icon::FAB);
$twitter_icon = Icon::show('twitter', ['class'=>'fa-2x','style' =>'color:#00ACEE', 'framework' => Icon::FAB]);
 ?>
<div class="well">
	<div class="row">
		<div class="col-md-12">
			<?php echo  $twitter_icon; ?>	
		<?=  Highcharts::widget([
			'scripts' => ['modules/drilldown'],	
			  'options' => [
			  	'chart' => ['type' => 'pie'],
				'title' => ['text' => Yii::t('app',"total por palabras en twitter ID: # {$info_head['alertId']}")],
				'subtitle' => ['text' => Yii::t('app',"desde la fecha: {$info_head['start_date']} - hasta la fecha: {$info_head['end_date']}")],
				'plotOptions' =>  [
					'series' => [
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
			        		'data' => $chartWords->getSeries('countWords') ,
			      		],
			    ],
		      	'drilldown' => [
		      		'series' => $chartWords->getDrilldownSeries('countWords'),

		      	],
		      	
				'credits' => ['enabled' => false],

			  ], 
			]);
		  ?>

		</div>
	</div>
</div>