<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\WebPage */
/* @var $form ActiveForm */
?>
<div class="site-web">

    <?php $form = ActiveForm::begin([
            'action' => '/create',
            'options' => [
             ]
        ]); ?>

		<?= $form->field($model, 'category_id') ?>
        <?= $form->field($model, 'name') ?>
        <?= $form->field($model, 'url_web_page') ?>
        
    
        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div><!-- site-web -->