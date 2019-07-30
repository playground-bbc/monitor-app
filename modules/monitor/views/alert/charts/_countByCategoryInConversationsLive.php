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
			'scripts' => [
			      'modules/exporting',
			      'themes/sand-signika',
			  ],  
			'options' => [
			'scripts' => [
			      'themes/unica',
			  ],	
		   	'chart' => ['type' => 'column'],
			      'title' => ['text' => Yii::t('app',"number of words by type of dictionary in LiveChat conversations ID: # {$info_head['alertId']}")],
		      	  'subtitle' => ['text' => Yii::t('app',"From date: {$info_head['start_date']} - To date: {$info_head['end_date']}")],
			      'xAxis' => [
			         'categories' => $chartCategories->getCategories('count_category_conversations'),
			      ],
			      'yAxis' => [
			         'title' => ['text' => 'Live Chat Data']
			      ],
			      'series' => $chartCategories->getSeries('count_category_conversations'),
			'credits' => ['enabled' => false],     
		   		]
			]);
		  ?>

		</div>
	</div>
</div>
