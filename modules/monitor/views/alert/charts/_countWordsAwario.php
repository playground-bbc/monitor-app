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
			  	'title' => ['text' => Yii::t('app',"total per words in Awario file ID: # {$info_head['alertId']}")],
				'subtitle' => ['text' => Yii::t('app',"From date: {$info_head['start_date']} - To date: {$info_head['end_date']}")],
				'plotOptions' =>  [
					'series' => [
						'dataLabels' => [
							'enabled' => true,
			                'format' => '{point.name}: {point.y}'
						],
					],

				],
				'tooltip' =>[
					'headerFormat' => '<span style="font-size:11px">{series.name}</span><br>',
			        'pointFormat' => '<span style="color:{point.color}">{point.name}</span> = <b>{point.y}</b> of total<br/>'
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