<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;

use app\models\ProductsFamily;
use app\models\ProductCategory;
use app\models\ProductsModels;
use app\models\api\TwitterApi;

use kartik\select2\Select2;
use kartik\date\DatePicker;
use kartik\file\FileInput;
use kartik\switchinput\SwitchInput;
use faryshta\widgets\JqueryTagsInput;

/* @var $this yii\web\View */
/* @var $model app\models\LiveChat */
/* @var $form ActiveForm */

$moduleName = $this->context->action->id;
$this->title = 'Crear Busqueda';
$this->params['breadcrumbs'][] = ['label' => $moduleName, 'Alerta', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;





?>

<?php $form = ActiveForm::begin(['id' => 'search-form','options' => ['enctype' => 'multipart/form-data']]); ?>
	
	<div class="container">
		<div class="row">
			<div class="col-md-6">
				<?= $form->field($form_alert,'name')  ?>
			</div>
			<div class="col-md-6">
				<?= $form->field($form_alert, 'social_resources[]')->widget(Select2::classname(), [
					   'data' => $form_alert->socialResources,
					    'options' => [
					    	'id' => 'social_resources',
					    	'placeholder' => 'Select a state ...',
					    	'multiple' => true
						],
					    'pluginOptions' => [
					        'allowClear' => true,
					    ],
					    'pluginEvents' => [
					       "select2:select" => "function(e) { populateClientCode(e.params.data.id); }",
					    ]
					]);
				?>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6">
				<?= $form->field($form_alert, 'products[]')->widget(Select2::classname(), [
					   'data' => $form_alert->Products,
					    'options' => [
					    	'placeholder' => 'Select a product! ...',
					    	'multiple' => true
						],
					    'pluginOptions' => [
					        'allowClear' => true,
					    ],
					]);
				?>
			</div>
			<div class="col-md-6">
				<?= $form->field($form_alert, 'awario_file')->widget(FileInput::classname(), [
					    'options' => ['accept' => 'text/csv'],
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
		<div class="row">
			<div id="checkbox" class="col-md-3">
				 <?= $form->field($form_alert, 'is_dictionary')->checkbox(array(
					'id'=>'dicto',
					//'labelOptions'=>array('style'=>'padding:5px;'),
					//'disabled'=>true
					)); ?>
			</div>
		</div>
		<div id="dictionary" class="row">
			<div class="col-md-3">
				<?= $form->field($form_alert, 'drive_dictionary[]')->widget(Select2::classname(), [
					   'data' => $form_alert->dictionaryNameOnDrive,
					    'options' => [
					    	'placeholder' => 'Select a state ...',
					    	'multiple' => true
						],
					    'pluginOptions' => [
					        'allowClear' => true,
					    ],
					]);
				?>
			</div>
			<div class="col-md-4" style="margin-left: 40px">
				<?= $form->field($form_alert, 'positive_words')->widget(JqueryTagsInput::className(), [
					    'clientOptions'=>[
					    	'height'=>'35px',
   							'width'=>'300px',
					    ]
					]);

				?>
			</div>
			<div class="col-md-4">
				<?= $form->field($form_alert, 'negative_words')->widget(JqueryTagsInput::className(), [
					    'clientOptions'=>[
					    	'height'=>'35px',
   							'width'=>'300px',
					    ]
					]);

				?>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6">
					<?=  $form->field($form_alert, 'start_date')->widget(DatePicker::classname(), [
						    'options' => ['placeholder' => 'Enter start date! ...'],
						    'pluginOptions' => [
						        'autoclose'=>true,
						        'format' => 'mm/dd/yyyy',
						        //'startDate' => date(Yii::$app->formatter->dateFormat, strtotime('today')),
		                    	'todayHighlight' => true
						    ]
						]);
					    
					 ?>
				</div>
				<div class="col-md-6">
					<?=  $form->field($form_alert, 'end_date')->widget(DatePicker::classname(), [
						    'options' => ['placeholder' => 'Enter end date ...'],
						    'pluginOptions' => [
						        'autoclose'=>true,
						        'format' => 'mm/dd/yyyy',
						        'startDate' => date(Yii::$app->formatter->dateFormat, strtotime('today')),
		                    	'todayHighlight' => true
						    ]
						]);
					    
					 ?>
				</div>
			</div>
		    <div class="form-group">
		        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
		    </div>
		</div>


<?php ActiveForm::end(); ?>

<?php

use yii\web\View; 
$this->registerJs('

$( document ).ready(function() {
    $("#dictionary").hide();  
	    $("#dicto").click(function(){
	        $("#checkbox").hide();
	        $("#dictionary").show();

	});
});	


',
    View::POS_READY
);


if (!\Yii::$app->session->has('oauth_token_twitter')) {
	$url = Url::to('twitter');

	$this->registerJs('
		function populateClientCode(params){
			if(params == 4){
			window.location.replace("'.$url.'");
			}
		}

	');

}




 ?>