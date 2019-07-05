<?php 
use yii\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;
 ?>


 

 <div class="container">
 	<!-- count categories tweet </!-->
	<?php if ($chartCategories->getCategories('countByCategoryInTweet')): ?>
		<?=$this->render('charts/_countByCategoryInTweet',['chartCategories' => $chartCategories]); ?>
 	<?php endif ?>
 	<!-- count words tweet </!-->
 	<?php if ($chartWords->getSeries('countWords')): ?>
		<?=$this->render('charts/_countWordsTweet',['chartWords' => $chartWords]); ?>
 	<?php endif ?>

 	<!-- Live chat countByCategory </!-->
 	<?php if ($chartCategories->getCategories('countByCategoryInLiveChat')): ?>
		<?=$this->render('charts/_countByCategoryInLive',['chartCategories' => $chartCategories]); ?>
 	<?php endif ?>
 	<!-- count words live </!-->
 	<?php if ($chartWords->getSeries('countWords_live')): ?>
		<?=$this->render('charts/_countWordsLive',['chartWords' => $chartWords]); ?>
 	<?php endif ?>
 </div>
 

