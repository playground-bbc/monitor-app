<?php 
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\data\ArrayDataProvider;
// format dates 
$start_date = \Yii::$app->formatter->asDatetime($alert->start_date, "php:d-m-Y");
$end_date = \Yii::$app->formatter->asDatetime($alert->end_date, "php:d-m-Y");

$info_head = [
	'alertId' => $alert->id,
	'start_date' => $start_date,
	'end_date' => $end_date
];


 ?>
 <div class="container">
 	<div class="row">
 		<div class="col-md-12" style="padding-bottom: 20px ">
 			<?= Html::a('New Alert', ['alert/create'], ['class' => 'btn btn-info pull-right']) ?>
 		</div>
 	</div>
 	
 	
	<!-- detail alert </!-->
	<?php if ($alert): ?>
		<?=$this->render('tables/_detailAlert',['alert' => $alert]); ?>
	<?php endif ?>
	<!-- tweets !-->
	<?php if(isset($model['tweets'])): ?>
	 	<!-- detail alert </!-->
	 	<div class = "row">
	 		<div class="col-md-6">
	 			<!-- count categories tweet </!-->
				<?php if ($chartCategories->getCategories('countByCategoryInTweet')): ?>
					<?=$this->render('charts/_countByCategoryInTweet',['chartCategories' => $chartCategories,'info_head' => $info_head]); ?>
			 	<?php endif ?>
	 		</div>
	 		<div class="col-md-6">
				<!-- count words tweet </!-->
			 	<?=$this->render('charts/_countWordsTweet',['chartWords' => $chartWords,'info_head' => $info_head]); ?>			
	 		</div>
	 	</div>
	 	<div class="row">
	 		<div class="col-md-12">
	 			<!-- count sentences tweet </!-->
			 	<?php if (isset($model['tweets'])): ?>
			 		<?php if (isset($model['tweets']['sentences'])): ?>
						<?=$this->render('tables/_sentencesTweet',['sentences' => $model['tweets']['sentences'],'alertId' => $alert->id]); ?>
			 		<?php endif ?>
			 	<?php endif ?>
	 		</div>
	 	</div>
		
		<hr>
	<?php endif ?>
	<!-- Live Chat !-->
	<?php if(isset($model['liveChat'])): ?>
	 	<div class="row">
	 		<div class="col-md-12">
				<!-- Live chat countByCategory </!-->
			 	<?php if ($chartCategories->getCategories('countByCategoryInLiveChat')): ?>
					<?=$this->render('charts/_countByCategoryInLive',['chartCategories' => $chartCategories,'info_head' => $info_head]); ?>
			 	<?php endif ?>
	 		</div>
	 	</div>
		<div class="row">
			<div class="col-md-6">
				<!-- count words live </!-->
			 	<?=$this->render('charts/_countWordsLive',['chartWords' => $chartWords,'info_head' => $info_head]); ?>
			</div>
			<div class="col-md-6">
				<!-- total ticket Live </!-->
			 	<?php if (isset($model['liveChat'])): ?>
			 		<?php if (isset($model['liveChat']['total'])): ?>
						<?=$this->render('charts/_totalTicektLive',['chartLive' => $chartLive,'info_head' => $info_head]) ?>
			 		<?php endif ?>
			 	<?php endif ?>
			</div>
		</div>
	 	<div class="row">
	 		<div class="col-md-12">
	 			<!-- count sentences Live </!-->
			 	<?php if (isset($model['liveChat'])): ?>
			 		<?php if (isset($model['liveChat']['sentences'])): ?>
						<?=$this->render('tables/_sentencesLive',['sentences' => $model['liveChat']['sentences'],'alertId' => $alert->id]); ?>
			 		<?php endif ?>
			 	<?php endif ?>
	 		</div>
	 	</div>
	 	
	 	<hr>
	<?php endif ?>
	<!-- live_conversations !-->
	<?php if(isset($model['live_conversations'])): ?>
		<div class="row">
			<div class="col-md-6">
				<!-- Live chat countByCategory </!-->
			 	<?php if ($chartCategories->getCategories('count_category_conversations')): ?>
					<?=$this->render('charts/_countByCategoryInConversationsLive',['chartCategories' => $chartCategories,'info_head' => $info_head]); ?>
			 	<?php endif ?>
			</div>
			<div class="col-md-6">
				<!-- count words live conversations </!-->
				<?=$this->render('charts/_countWordsLiveConversations',['chartWords' => $chartWords,'info_head' => $info_head]); ?>
			</div>
		</div>
		<div class="row">
	 		<div class="col-md-12">
	 			<!-- count sentences Live </!-->
			 	<?php if (isset($model['live_conversations'])): ?>
			 		<?php if (isset($model['live_conversations']['sentences'])): ?>
						<?=$this->render('tables/_sentencesLiveConversations',['conversations' => $model['live_conversations']['sentences'],'alertId' => $alert->id]); ?>
			 		<?php endif ?>
			 	<?php endif ?>
	 		</div>
	 	</div>
	 	<hr>
 	<?php endif ?>
	<!-- awario !-->
 	<?php if (isset($model['awario'])): ?>
		<div class="row">
			<div class="col-md-6">
				<!-- total awario categories </!-->
		 		<?php if (isset($model['awario']['countByCategoryInAwario'])): ?>
					<?=$this->render('charts/_countByCategoryInAwario',['chartAwario' => $chartCategories,'info_head' => $info_head]) ?>
		 		<?php endif ?>
			</div>
			<div class="col-md-6">
				<!-- count words awario </!-->
			 	<?php if (isset($model['awario']['countWords_awario'])): ?>
					<?=$this->render('charts/_countWordsAwario',['chartWords' => $chartWords,'info_head' => $info_head]); ?>
			 	<?php endif ?>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<!-- sentences awario </!-->
			 	<?php if (isset($model['awario'])): ?>
			 		<?php if (isset($model['awario']['sentences'])): ?>
						<?=$this->render('tables/_sentencesAwario',['sentences' => $model['awario']['sentences'],'alertId' => $alert->id]) ?>
			 		<?php endif ?>
			 	<?php endif ?>
			</div>
		</div>
	 	
	 	<hr>
 	<?php endif ?>
 	<!-- web !-->
	<?php if (ArrayHelper::keyExists('sentences_web',$model['web'], false)): ?>
	 	<div class="row">
	 		<div class="col-md-6">
	 			<?php 

	 				$url = [];

					foreach ($alert->alertResources as $alert => $resources) {
					    for ($i=0; $i <sizeof($resources->resources) ; $i++) { 
					        if($resources->resources[$i]->typeResourceId == 1){
					        	$url [] = "{$resources->resources[$i]->url}";
					        }
					    }
					}

					$url = implode( "<br>", $url );

	 			?>
	 			<!-- count categories web </!-->
	 			<?=$this->render('charts/_countByCategoryInWeb',['chartCategories' => $chartCategories,'info_head' => $info_head,'url' => $url]); ?>
	 		</div>
	 		<div class="col-md-6">
	 			<!-- count words web </!-->
			 	<?=$this->render('charts/_countWordsWeb',['chartWords' => $chartWords,'info_head' => $info_head,'url' => $url]); ?>
	 		</div>
	 	</div>
	 	<div class="row">
	 		<div class="col-md-12">
	 			<!-- sentences web </!-->
			 	
			 		<?php if (isset($model['web']['sentences_web'])): ?>
						<?=$this->render('tables/_sentencesWeb',['sentences' => $model['web']['sentences_web'],'alertId' => $info_head['alertId']]) ?>
			 		<?php endif ?>
			 	
	 		</div>
	 	</div>
 	<?php endif ?>
 </div>
 

