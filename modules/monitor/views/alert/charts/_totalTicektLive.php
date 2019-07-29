<?php 
use miloschuman\highcharts\Highcharts;


 ?>

<div class="well">
	<div class="row">
		<div class="col-md-12">
			<?=  Highcharts::widget([
			'options' => [
			'scripts' => [
			      'themes/unica',
			  ],	
			      'title' => [
			      	'text' => Yii::t('app','Universo de Tickets'),
			      	'align' => 'center',
			      	'y' => 40,
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