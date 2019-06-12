<?php 
use app\models\CategoriesDictionary;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use kartik\file\FileInput;
use yii\helpers\Url;
use yii\web\View;

$categories_dictionary = ArrayHelper::map(CategoriesDictionary::find()->all(),'id','name');

?>

	<div class="row">
		<div class="col-md-6">
			<?php 

			echo $form->field($form_model, 'negative_words')->widget(FileInput::classname(), [
				    'options' => ['accept' => 'txt/*'],
				    'pluginOptions' => [
				        'showPreview' => false,
				        'showCaption' => true,
				        'showRemove' => true,
				        'showUpload' => false
    				]

				]);

			?>
		</div>
		<div class="col-md-6">
			<?php 
				echo $form->field($form_model, 'positive_words')->widget(FileInput::classname(), [
				    'options' => ['accept' => 'txt/*'],
				    'pluginOptions' => [
				        'showPreview' => false,
				        'showCaption' => true,
				        'showRemove' => true,
				        'showUpload' => false
    				]
				]);
				?>
		</div>
	</div>


