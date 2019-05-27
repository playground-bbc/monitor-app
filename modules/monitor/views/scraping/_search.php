<?php 
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use faryshta\widgets\JqueryTagsInput;


use app\models\Resource;

/* @var $this yii\web\View */
/* @var $form_model app\models\SearchForm */
/* @var $form ActiveForm */

 ?>

<?php $form = ActiveForm::begin(['id' => 'search-form']); ?>


    <?= $form->field($form_model, 'keywords[]')->widget(JqueryTagsInput::className(), [
    // extra configuration
	]); ?>

	<?= $form->field($form_model, 'web_resource[]')->label(false)->widget(Select2::className(), [
	        'data' => ArrayHelper::map(Resource::find()->all(), 'url', 'name'),
	        'options' => [
	            'multiple' => true,
	            'placeholder' => 'Choose tag ...',
	        ],
	        'pluginOptions' => [
	            'tags' => true
	        ]
	    ]);  
    ?>


	<?= $form->field($form_model, 'social_resources[]')->listBox($form_model->social_resources,['multiple' => 'true']); ?>

	<?php $form->field($form_model, 'query_search')->textarea(['rows'=>2,'cols'=>5]); ?>

    <div class="form-group">
        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
    </div>

<?php ActiveForm::end(); ?>