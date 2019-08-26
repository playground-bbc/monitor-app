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
			'scripts' => [
			      'modules/exporting',
			      'themes/sand-signika',
			  ],  				
			'options' => [
			'scripts' => [
			      'themes/unica',
			  ],	
		   	'chart' => ['type' => 'column'],
			      'title' => ['text' => Yii::t('app',"numero de palabras por diccionario en LiveChat Tickets ID: # {$info_head['alertId']}")],
		      	  'subtitle' => ['text' => Yii::t('app',"desde la fecha: {$info_head['start_date']} - hasta la fecha: {$info_head['end_date']}")],
			      'xAxis' => [
			         'categories' => $chartCategories->getCategories('countByCategoryInLiveChat'),
			      ],
			      'yAxis' => [
			         'title' => ['text' => 'Live Chat Data']
			      ],
			      'series' => $chartCategories->getSeries('countByCategoryInLiveChat'),
			'credits' => ['enabled' => false],     
		   		]
			]);
		  ?>

		</div>
	</div>
</div>
