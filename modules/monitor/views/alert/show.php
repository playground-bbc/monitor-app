<?php 
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\data\ArrayDataProvider;


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
 	<!-- detail alert </!-->
 	<!-- count categories tweet </!-->
	<?php if ($chartCategories->getCategories('countByCategoryInTweet')): ?>
		<?=$this->render('charts/_countByCategoryInTweet',['chartCategories' => $chartCategories]); ?>
 	<?php endif ?>
 	<!-- count words tweet </!-->
 	<?php if ($chartWords->getSeries('countWords')): ?>
		<?=$this->render('charts/_countWordsTweet',['chartWords' => $chartWords]); ?>
 	<?php endif ?>
 	<!-- count sentences tweet </!-->
 	<?php if (isset($model['tweets'])): ?>
 		<?php if (isset($model['tweets']['sentences'])): ?>
			<?=$this->render('tables/_sentencesTweet',['sentences' => $model['tweets']['sentences'],'alertId' => $alert->id]); ?>
 		<?php endif ?>
 	<?php endif ?>

 	<!-- Live chat countByCategory </!-->
 	<?php if ($chartCategories->getCategories('countByCategoryInLiveChat')): ?>
		<?=$this->render('charts/_countByCategoryInLive',['chartCategories' => $chartCategories]); ?>
 	<?php endif ?>
 	<!-- count words live </!-->
 	<?php if ($chartWords->getSeries('countWords_live')): ?>
		<?=$this->render('charts/_countWordsLive',['chartWords' => $chartWords]); ?>
 	<?php endif ?>
 	<!-- count sentences Live </!-->
 	<?php if (isset($model['liveChat'])): ?>
 		<?php if (isset($model['liveChat']['sentences_live'])): ?>
			<?=$this->render('tables/_sentencesLive',['sentences' => $model['liveChat']['sentences_live'],'alertId' => $alert->id]); ?>
 		<?php endif ?>
 	<?php endif ?>
 	<!-- total ticket Live </!-->
 	<?php if (isset($model['liveChat'])): ?>
 		<?php if (isset($model['liveChat']['total'])): ?>
			<?=$this->render('charts/_totalTicektLive',['chartLive' => $chartLive]) ?>
 		<?php endif ?>
 	<?php endif ?>

 	
 	<!-- total awario categories </!-->
 	<?php if (isset($model['awario'])): ?>
 		<?php if (isset($model['awario']['countByCategoryInAwario'])): ?>
			<?=$this->render('charts/_countByCategoryInAwario',['chartAwario' => $chartCategories]) ?>
 		<?php endif ?>
 	<?php endif ?>
 	<!-- sentences awario </!-->
 	<?php if (isset($model['awario'])): ?>
 		<?php if (isset($model['awario']['sentence_awario'])): ?>
			<?=$this->render('tables/_sentencesAwario',['sentences' => $model['awario']['sentence_awario']]) ?>
 		<?php endif ?>
 	<?php endif ?>

	<!-- count categories web </!-->
	<?php if ($chartCategories->getCategories('countByCategoryInWeb')): ?>
		<?=$this->render('charts/_countByCategoryInWeb',['chartCategories' => $chartCategories]); ?>
 	<?php endif ?>
 	<!-- count words web </!-->
 	<?php if ($chartWords->getSeries('countWords_web')): ?>
		<?=$this->render('charts/_countWordsWeb',['chartWords' => $chartWords]); ?>
 	<?php endif ?>
 	<!-- sentences web </!-->
 	<?php if (isset($model['web'])): ?>
 		<?php if (isset($model['web']['sentences_web'])): ?>
			<?=$this->render('tables/_sentencesWeb',['sentences' => $model['web']['sentences_web']]) ?>
 		<?php endif ?>
 	<?php endif ?>

 	
 </div>
 

