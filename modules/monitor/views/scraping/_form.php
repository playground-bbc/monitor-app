<?php
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;

use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\models\WebPage */
/* @var $form ActiveForm */



$moduleName = $this->context->action->id;
$this->title = 'Crear categoría';
$this->params['breadcrumbs'][] = ['label' => $moduleName, 'Categorias', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$data = ArrayHelper::map($typeResource, 'id', 'name');
?>
<div class="site-web">

    <?php $form = ActiveForm::begin([
        'id' => 'form-webpage',
        'layout' => 'horizontal']); 
    ?>
    
    <?= $form->field($resource, 'typeResourceId')->widget(Select2::classname(), [
		   'data' => $data,
		    'options' => ['placeholder' => 'Select a state ...'],
		    'pluginOptions' => [
		        'allowClear' => true
		    ],
		]);
	?>
    <?= $form->field($resource, 'name') ?>
    <?= $form->field($resource, 'url') ?>
        
    
    <div class="form-group">
        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div><!-- site-web -->