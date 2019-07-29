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
 	
 	<div class="container">
 		<!-- detail alert </!-->
		<?php if ($alert): ?>
			<?=$this->render('tables/_detailAlert',['alert' => $alert]); ?>
	 	<?php endif ?>
	 	<!-- detail alert </!-->
	 	<div class = "row">
	 		<div class="col-md-6">
	 			<!-- count categories tweet </!-->
				<?php if ($chartCategories->getCategories('countByCategoryInTweet')): ?>
					<?=$this->render('charts/_countByCategoryInTweet',['chartCategories' => $chartCategories]); ?>
			 	<?php endif ?>
	 		</div>
	 		<div class="col-md-6">
				<!-- count words tweet </!-->
			 	<?php if ($chartWords->getSeries('countWords')): ?>
					<?=$this->render('charts/_countWordsTweet',['chartWords' => $chartWords]); ?>
			 	<?php endif ?> 			
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
 	</div>
 	<hr>
 	<div class="row">
 		<div class="col-md-12">
			<!-- Live chat countByCategory </!-->
		 	<?php if ($chartCategories->getCategories('countByCategoryInLiveChat')): ?>
				<?=$this->render('charts/_countByCategoryInLive',['chartCategories' => $chartCategories]); ?>
		 	<?php endif ?>
 		</div>
 	</div>
	<div class="row">
		<div class="col-md-6">
			<!-- count words live </!-->
		 	<?php if ($chartWords->getSeries('countWords_live')): ?>
				<?=$this->render('charts/_countWordsLive',['chartWords' => $chartWords]); ?>
		 	<?php endif ?>
		</div>
		<div class="col-md-6">
			<!-- total ticket Live </!-->
		 	<?php if (isset($model['liveChat'])): ?>
		 		<?php if (isset($model['liveChat']['total'])): ?>
					<?=$this->render('charts/_totalTicektLive',['chartLive' => $chartLive]) ?>
		 		<?php endif ?>
		 	<?php endif ?>
		</div>
	</div>
 	<div class="row">
 		<div class="col-md-12">
 			<!-- count sentences Live </!-->
		 	<?php if (isset($model['liveChat'])): ?>
		 		<?php if (isset($model['liveChat']['sentences_live'])): ?>
					<?=$this->render('tables/_sentencesLive',['sentences' => $model['liveChat']['sentences_live'],'alertId' => $alert->id]); ?>
		 		<?php endif ?>
		 	<?php endif ?>
 		</div>
 	</div>
 	
 	<hr>
	<?php if(isset($model['live_conversations'])): ?>
	<div class="row">
		<div class="col-md-6">
			<!-- Live chat countByCategory </!-->
		 	<?php if ($chartCategories->getCategories('count_category_conversations')): ?>
				<?=$this->render('charts/_countByCategoryInConversationsLive',['chartCategories' => $chartCategories]); ?>
		 	<?php endif ?>
		</div>
		<div class="col-md-6">
			<!-- count words live conversations </!-->
			<?=$this->render('charts/_countWordsLiveConversations',['chartWords' => $chartWords]); ?>
		</div>
	</div>
	<div class="row">
 		<div class="col-md-12">
 			<!-- count sentences Live </!-->
		 	<?php if (isset($model['live_conversations'])): ?>
		 		<?php if (isset($model['live_conversations']['sentences_live_conversations'])): ?>
					<?=$this->render('tables/_sentencesLiveConversations',['conversations' => $model['live_conversations']['sentences_live_conversations'],'alertId' => $alert->id]); ?>
		 		<?php endif ?>
		 	<?php endif ?>
 		</div>
 	</div>
 	<?php endif ?>
 	<hr>

 	
	<div class="row">
		<div class="col-md-6">
			<!-- total awario categories </!-->
		 	<?php if (isset($model['awario'])): ?>
		 		<?php if (isset($model['awario']['countByCategoryInAwario'])): ?>
					<?=$this->render('charts/_countByCategoryInAwario',['chartAwario' => $chartCategories]) ?>
		 		<?php endif ?>
		 	<?php endif ?>
		</div>
		<div class="col-md-6">
			<!-- count words awario </!-->
		 	<?php if (isset($model['awario']['countWords_awario'])): ?>
				<?=$this->render('charts/_countWordsAwario',['chartWords' => $chartWords]); ?>
		 	<?php endif ?>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<!-- sentences awario </!-->
		 	<?php if (isset($model['awario'])): ?>
		 		<?php if (isset($model['awario']['sentence_awario'])): ?>
					<?=$this->render('tables/_sentencesAwario',['sentences' => $model['awario']['sentence_awario'],'alertId' => $alert->id]) ?>
		 		<?php endif ?>
		 	<?php endif ?>
		</div>
	</div>
 	
 	
 	
 	<div class="row">
 		<div class="col-md-6">
 			<!-- count categories web </!-->
			<?php if ($chartCategories->getCategories('countByCategoryInWeb')): ?>
				<?=$this->render('charts/_countByCategoryInWeb',['chartCategories' => $chartCategories]); ?>
		 	<?php endif ?>
 		</div>
 		<div class="col-md-6">
 			<!-- count words web </!-->
		 	<?php if ($chartWords->getSeries('countWords_web')): ?>
				<?=$this->render('charts/_countWordsWeb',['chartWords' => $chartWords]); ?>
		 	<?php endif ?>
 		</div>
 	</div>
 	<div class="row">
 		<div class="col-md-12">
 			<!-- sentences web </!-->
		 	<?php if (isset($model['web'])): ?>
		 		<?php if (isset($model['web']['sentences_web'])): ?>
					<?=$this->render('tables/_sentencesWeb',['sentences' => $model['web']['sentences_web']]) ?>
		 		<?php endif ?>
		 	<?php endif ?>
 		</div>
 	</div>

	
 	
 	

 	
 </div>
 

