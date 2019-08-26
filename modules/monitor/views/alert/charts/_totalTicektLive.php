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
			'options' => [
			'scripts' => [
			      'themes/unica',
			  ],	
			      'title' => [
			      	'text' => Yii::t('app',"tickets rescatados y el total de tickets en LiveChat ID: # {$info_head['alertId']}"),
			      	'align' => 'center',
			      ],
			      'plotOptions' =>  [
			      		'series' => [
				      		'dataLabels' => [
				      			'enabled' => true
				      		]
				      	],
						'pie' => [
							'dataLabels' => [
								'enabled' => true,
								'distance' => -50,
				                'startAngle'=> -90,
					            'endAngle'=> 90,
					            'center'=> ['50%', '75%'],
					            'size'=> '110%'
							],
						],

					],
			      
			      'series' => [
			      	[
			      		'type'=> 'pie',
				      	'name'=>'Universo de Ticket',
				      	'innerSize' => '50%',
				      	'data'=> $chartLive->getSeries('total'),
			      	],

			      ],
			'credits' => ['enabled' => false],     
		   		]
			]);
		  ?>
		</div>
	</div>
</div>