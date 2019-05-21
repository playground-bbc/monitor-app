<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;

/* @var $this yii\web\View */
/* @var $model app\models\WebPage */
/* @var $form ActiveForm */



$moduleName = $this->context->action->id;
$this->title = 'Crear categoría';
$this->params['breadcrumbs'][] = ['label' => $moduleName, 'Categorias', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$data = ArrayHelper::map($categories, 'id', 'name');
?>
<div class="site-web">

    <?php $form = ActiveForm::begin([
        'id' => 'form-webpage',
     
        'layout' => 'horizontal']); ?>
    <?php 
			echo $form->field($model, 'category_id')->widget(Select2::classname(), [
		   'data' => $data,
		    'options' => ['placeholder' => 'Select a state ...'],
		    'pluginOptions' => [
		        'allowClear' => true
		    ],
		]);

		 ?>
        <?= $form->field($model, 'name') ?>
        <?= $form->field($model, 'url_web_page') ?>
        
    
        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div><!-- site-web -->