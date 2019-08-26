<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;

use kartik\select2\Select2;
use kartik\date\DatePicker;
use kartik\file\FileInput;
use pudinglabs\tagsinput\TagsinputWidget;

/* @var $this yii\web\View */
/* @var $model app\models\LiveChat */
/* @var $form ActiveForm */

$moduleName = $this->context->action->id;
$this->title = 'Crear Busqueda';
$this->params['breadcrumbs'][] = ['label' => $moduleName, 'Alerta', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;




?>
<hr>
<br>
<br>
<?php $form = ActiveForm::begin(['id' => 'search-form','enableAjaxValidation' => true,'options' => ['enctype' => 'multipart/form-data']]); ?>
	
	<div class="container">
		<div class="row">
			<?= $form->field($form_alert,'name')->hiddenInput()->label(false);  ?>
			<div class="col-md-6">
					<?=  $form->field($form_alert, 'start_date')->widget(DatePicker::classname(), [
						    'options' => [
						    	'placeholder' => 'Enter start date! ...',
						    	'value' => date('m/d/Y'),

						    ],
						    'pluginOptions' => [
						        'autoclose'=>true,
						        'format' => 'mm/dd/yyyy',
						        //'startDate' => date(Yii::$app->formatter->dateFormat, strtotime('today')),
		                    	'todayHighlight' => true
						    ],
						    'pluginEvents' => [
						    	'changeDate' => "function(e) { 
										callSendProducts();
							       }",
						    ]
						]);
					    
					 ?>
			</div>
			<div class="col-md-6">
					<?=  $form->field($form_alert, 'end_date')->widget(DatePicker::classname(), [
						    'options' => ['placeholder' => 'Enter end date ...',
						    'value' => date('m/d/Y')
						],
						    'pluginOptions' => [
						        'autoclose'=>true,
						        'format' => 'mm/dd/yyyy',
						        //'startDate' => date(Yii::$app->formatter->dateFormat, strtotime('today')),
		                    	'todayHighlight' => true
						    ],
						    'pluginEvents' => [
						    	'changeDate' => "function(e) { 
										callSendProducts();
							       }",
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
				<?= $form->field($form_alert, 'social_resources')->widget(Select2::classname(), [
					   'data' => $form_alert->socialResources,
					   	'options' => [
					    	'id' => 'social_resources',
					    	'placeholder' => 'Select a state ...',
					    	'multiple' => true,
					    	'theme' => 'krajee',
					    	'debug' => true
						],
					    'pluginOptions' => [
					        'allowClear' => true,
					    ],
					    'pluginEvents' => [
					       "select2:select" => "function(e) { 
								callSendProducts();
					       		populateClientCode(e.params.data.id); 
					       }",
					    ]
					]);
				?>
			</div>
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
					    	'multiple' => true,
					    	//'disabled' => true
						],
					    'pluginOptions' => [
					        'allowClear' => true,

					    ],
					    'pluginEvents' => [
					       "select2:select" => "function(e) { sendProducts(e.params.data.id) }",
					       "select2:unselect" => "function(e) { removeProducts(e.params.data.id) }",
					    ]
					]);
				?>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6">
				<?= $form->field($form_alert, 'positive_words')->widget(TagsinputWidget::classname(), [
			            'options' => [
			            	'width' => '10px',
			            ],
			            'clientOptions' => [],
			            'clientEvents' => []
			         ]);
				 ?>
				
			</div>
			<div class="col-md-6">
				<?=  $form->field($form_alert, 'web_resource')->widget(TagsinputWidget::classname(), [
			            'options' => [],
			            'clientOptions' => [],
			            'clientEvents' => [
			            	//"itemAdded" => "function(e) { sendUrls(e.item) }",
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
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<?php

use yii\web\View; 

$urlModelsAlert = Url::to(['insert-product']);
$urlModelsAlertDelete = Url::to(['delete-product']);

$urlUrlAlert = Url::to(['web/search-web']);
$uuid = '';


$this->registerJs('


function callSendProducts(){
	var products = $("#searchform-products").val();
	console.log(products);
	if(products != null){
		if(products.length){
			for(p = 0; p < products.length; p ++){
				sendProducts(products[p]);

			}
		}
	}
}



function removeProducts(name){
	var product_name = name;
	
	$.ajax({
        url:"'.$urlModelsAlertDelete.'",
        type:"post",
        dataType: "json",
	        data: {
	            product_name: product_name,  
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

function sendProducts(name){
	
	var flag = false;
	
	var product_name = name;
	
	var  alert_name = $("#searchform-name").val()
	var  resource   = $("#social_resources").val()
	
	var  start_date = $("#searchform-start_date").val()
	var  start_end  = $("#searchform-end_date").val()

	if( start_date.length && start_end.length && resource ){
		flag = true;
	}else{
		
		swal("Upps!", "you need to fill in the fields of resources and dates!", "error");
		$("#searchform-products").val("").trigger("change");
	}

	if(flag){
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
	 
}

',
    View::POS_READY
);

/*if (!\Yii::$app->session->has('oauth_token_twitter')) {
	$url = Url::to('twitter');

	$this->registerJs('
		function populateClientCode(params){
			console.log(params)
			if(params == 1){
			window.location.replace("'.$url.'");
			}
		}

	');

}
*/



 ?>
