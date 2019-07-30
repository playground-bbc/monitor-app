<?php 
use miloschuman\highcharts\Highcharts;
use kartik\icons\Icon;
Icon::map($this, Icon::WHHG);
 ?>
<div class="well">
	<div class="row">
		<div class="col-md-12">
			<?php echo Icon::show('chat', ['style' =>'color:#d36126; font-size: 30px', 'framework' => Icon::WHHG]); ?>
		<?=  Highcharts::widget([
			'scripts' => ['modules/drilldown'],	
			  'options' => [
			  	'chart' => ['type' => 'pie'],
				'title' => ['text' => Yii::t('app',"total per words in LiveChat Conversations ID: # {$info_head['alertId']}")],
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
			        		'data' => $chartWords->getSeries('count_words_conversations') ,
			      		],
			    ],
		      	'drilldown' => [
		      		'series' => $chartWords->getDrilldownSeries('count_words_conversations'),

		      	],
		      	
				'credits' => ['enabled' => false],

			  ], 
			]);
		  ?>

		</div>
	</div>
</div>