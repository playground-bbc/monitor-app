<?php
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;

use app\models\ProductsFamily;
use app\models\ProductCategory;
use app\models\ProductsModels;

use kartik\select2\Select2;
use kartik\date\DatePicker;
use yii\bootstrap\Modal;
use kartik\widgets\FileInput;

/* @var $this yii\web\View */
/* @var $model app\models\LiveChat */
/* @var $form ActiveForm */

$moduleName = $this->context->action->id;
$this->title = 'Crear Busqueda';
$this->params['breadcrumbs'][] = ['label' => $moduleName, 'Alerta', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$family['Products Family'] = ArrayHelper::map(ProductsFamily::find()->andFilterCompare('parentId','null','<>')->all(),'name','name');
$categories['Product Category'] = ArrayHelper::map(ProductCategory::find()->andFilterCompare('familyId','null','<>')->all(),'name','name');
$products_models['Product Models'] = ArrayHelper::map(ProductsModels::find()->andFilterCompare('productId','null','<>')->all(),'serial_model','serial_model');

$data = ArrayHelper::merge($family,$categories);
$data = ArrayHelper::merge($products_models,$data);

?>

<?php $form = ActiveForm::begin(['id' => 'search-form']); ?>
	
	<div class="row">
		<div class="col-md-12">
			<?php 
			    echo DatePicker::widget([
			        'name' => 'from_date',
			        'value' => '01-Feb-1996',
			        'type' => DatePicker::TYPE_RANGE,
			        'name2' => 'to_date',
			        'value2' => '27-Feb-1996',
			        'pluginOptions' => [
			            'autoclose' => true,
			            'format' => 'yyyy-mm-dd'
			        ]
			    ]);
			 ?>
		</div>
		<div class="col-md-6">
			<?= $form->field($form_model, 'products[]')->widget(Select2::classname(), [
				   'data' => $data,
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
		<div class="col-md-12">
			<?php 
				Modal::begin([
				    'header' => 'Hello world',
				    'toggleButton' => ['label' => 'click me'],
				]);
				echo $this->render('_modal',['form_model' => $form_model]);
				Modal::end();

			 ?>
		</div>
	</div>

    <div class="form-group">
        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
    </div>

<?php ActiveForm::end(); ?>