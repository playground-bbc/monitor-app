<?php
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;

use app\models\Resource;

use kartik\select2\Select2;
use kartik\date\DatePicker;
use faryshta\widgets\JqueryTagsInput;

/* @var $this yii\web\View */
/* @var $model app\models\LiveChat */
/* @var $form ActiveForm */

$moduleName = $this->context->action->id;
$this->title = 'Crear Busqueda';
$this->params['breadcrumbs'][] = ['label' => $moduleName, 'Alerta', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$data = [
	'1' => 'some'

];
?>

<?php $form = ActiveForm::begin(['id' => 'search-form']); ?>
	<div class="row">
	    <div class="col-md-6">
			<?= $form->field($form_model, 'keywords[]')->widget(JqueryTagsInput::className(), [
				    // extra configuration
					])->label(true); ?>	    	
	    </div>

		<div class="col-md-6">
			<?= $form->field($form_model, 'products[]')->widget(JqueryTagsInput::className(), [
			    // extra configuration
				]); ?>
		</div>
	</div>
	


	<?php 
		echo DatePicker::widget([
		    'name' => 'from_date',
		    'value' => '2019-02-15',
		    'type' => DatePicker::TYPE_RANGE,
		    'name2' => 'to_date',
		    'value2' => '2019-02-15',
		    'pluginOptions' => [
		        'autoclose' => true,
		        'format' => 'yyyy-mm-dd'
		    ]
		]);

	 ?>

    <div class="form-group">
        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
    </div>

<?php ActiveForm::end(); ?>