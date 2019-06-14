<?php 
use yii\web\View;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
 ?>

<div class="monitor-default-index">
    <h1><?= $this->context->action->uniqueId ?></h1>
    <p>
        This is the view content for action "<?= $this->context->action->id ?>".
        The action belongs to the controller "<?= get_class($this->context) ?>"
        in the "<?= $this->context->module->id ?>" module.
    </p>
    <p>
        You may customize this page by editing the following file:<br>
        <code><?= __FILE__ ?></code>
    </p>
    <div class="container">
    <div class="row" style="padding-top: 15px">
        <div class="col-md-12">
            <?php $form = ActiveForm::begin([
                'id' => 'form-family',
                'layout' => 'horizontal']); ?>

                <?= $form->field($model, 'parentId')->widget(Select2::classname(), [
                        'data' => $parents ,
                        'hashVarLoadPosition' => View::POS_READY,
                        'language' => 'es',
                        'options' => ['placeholder' => 'Select a parentId ...'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]); ?>
                <?= $form->field($model, 'name') ?>
         
                <div class="form-group text-center">
                    <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary']) ?>
                </div>
                <div class="form-group text-center">
                    <?= Html::submitButton(Yii::t('app', 'Submit & Create'),['class' => 'btn btn-warning'],['return' =>'true' ]) ?>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
            
        </div>
    </div>
</div>
