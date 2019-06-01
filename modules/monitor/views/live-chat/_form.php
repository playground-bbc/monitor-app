<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use faryshta\widgets\JqueryTagsInput;



use app\models\Resource;


/* @var $this yii\web\View */
/* @var $model app\models\WebPage */
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


    <?= $form->field($form_model, 'keywords[]')->widget(JqueryTagsInput::className(), [
    // extra configuration
	]); ?>



	<?= $form->field($form_model, 'text_search')->textarea(['rows' => 2]); ?>

	

    <div class="form-group">
        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
    </div>

<?php ActiveForm::end(); ?>