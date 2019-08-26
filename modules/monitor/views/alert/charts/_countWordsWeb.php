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
			'scripts' => ['modules/drilldown'],	
			  'options' => [
			  	'chart' => ['type' => 'pie'],
				'title' => ['text' => Yii::t('app',"total por palabras en Web page ID: # {$info_head['alertId']}")],
				'subtitle' => ['text' => $url],
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
			        		'data' => $chartWords->getSeries('countWords_web') ,
			      		],
			    ],
		      	'drilldown' => [
		      		'series' => $chartWords->getDrilldownSeries('countWords_web'),

		      	],
		      	
				'credits' => ['enabled' => false],

			  ], 
			]);
		  ?>

		</div>
	</div>
</div>