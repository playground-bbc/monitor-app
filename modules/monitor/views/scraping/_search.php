<?php 
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;

/* @var $this yii\web\View */
/* @var $model app\models\WebPage */
/* @var $form ActiveForm */

 ?>

<?php $form = ActiveForm::begin(['id' => 'search-form']); ?>

    <?= $form->field($model, 'text_search')->textInput(['autofocus' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
    </div>

<?php ActiveForm::end(); ?>