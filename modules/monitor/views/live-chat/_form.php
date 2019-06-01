<?php
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;

use app\models\Resource;

use kartik\select2\Select2;
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

    <?= $form->field($form_model, 'keywords[]')->widget(JqueryTagsInput::className(), [
    // extra configuration
	]); ?>

	<?= $form->field($form_model, 'text_search')->textarea(['rows' => 2]); ?>

    <div class="form-group">
        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
    </div>

<?php ActiveForm::end(); ?>