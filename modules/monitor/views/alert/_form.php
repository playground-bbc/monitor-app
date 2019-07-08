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

/*
var_dump(\Yii::$app->session->has('oauth_token_twitter'));
die();*/


?>

<?php $form = ActiveForm::begin(['id' => 'search-form','options' => ['enctype' => 'multipart/form-data']]); ?>
	
	<div class="container">
		<div class="row">
			<?= $form->field($form_alert,'name')->hiddenInput()->label(false);  ?>
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
						        //'startDate' => date(Yii::$app->formatter->dateFormat, strtotime('today')),
		                    	'todayHighlight' => true
						    ]
						]);
					    
					 ?>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6">
				<?= $form->field($form_alert, 'awario_file')->widget(FileInput::classname(), [
					    'options' => ['accept' => 'text/csv'],
					    'pluginOptions' => [
					    	/*'uploadUrl' => Url::to(['awario']),
					    	'uploadExtraData' => [
					            'album_id' => 20,
					            'cat_id' => 'Nature'
					        ],*/
					        'showPreview' => false,
					        'showCaption' => true,
					        'showRemove' => true,
					        'showUpload' => false
	    				]

					]);
				?>
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
			
		</div>
		<div class="row">
			<div class="col-md-6">
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
					    'pluginEvents' => [
					       "select2:select" => "function(e) { sendProducts(e.params.data.id) }",
					       "select2:unselect" => "function(e) { removeProducts(e.params.data) }",
					    ]
					]);
				?>
			</div>
		</div>
		<div class="row">
			
		</div>
		
	    <div class="form-group">
	        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
	    </div>
	</div>


<?php ActiveForm::end(); ?>

<?php

use yii\web\View; 

$urlModelsAlert = Url::to(['models']);
$uuid = '';


$this->registerJs('

$( document ).ready(function() {
    $("#dictionary").hide();  
	    $("#dicto").click(function(){
	        $("#checkbox").hide();
	        $("#dictionary").show();

	});
});	

function removeProducts(id){
	console.log(id);
}

function sendProducts(name){
	var product_name = name;
	var  alert_name = $("#searchform-name").val()
	var  resource = $("#social_resources").val()
	var  start_date = $("#searchform-start_date").val()
	var  start_end = $("#searchform-end_date").val()

	$.ajax({
        url:"'.$urlModelsAlert.'",
        type:"post",
        dataType: "json",
        data: {
            product_name: product_name,
            alert_name: alert_name,
            resource: resource,
            start_date: start_date,
            start_end: start_end,
           
        }

    })
    .done(function(response) {
                if (response.data.success == true) {
                    console.log(response.data);
                }
            })
    .fail(function() {
        console.log("error");
    });	


	 
}

',
    View::POS_READY
);


if (!\Yii::$app->session->has('oauth_token_twitter')) {
	$url = Url::to('twitter');

	$this->registerJs('
		function populateClientCode(params){
			if(params == 2){
			window.location.replace("'.$url.'");
			}
		}

	');

}




 ?>