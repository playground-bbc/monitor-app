<?php 
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use faryshta\widgets\JqueryTagsInput;

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
</div>

<?php $form = ActiveForm::begin(['id' => 'search-form']); ?>
    
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($form_model,'name')->hiddenInput()->label(false);  ?>
                <?= 
                    // with ActiveForm
                    $form->field($form_model, 'web_resource')->widget(JqueryTagsInput::className(), [
                        // extra configuration
                    ]);
                 ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($form_model, 'drive_dictionary[]')->widget(Select2::classname(), [
                       'data' => $form_model->dictionaryNameOnDrive,
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
            <div class="col-md-6">
                <?= $form->field($form_model, 'products[]')->widget(Select2::classname(), [
                       'data' => $form_model->Products,
                        'options' => [
                            'placeholder' => 'Select a product! ...',
                            'multiple' => true
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                        'pluginEvents' => [
                           "select2:select" => "function(e) { sendProducts(e.params.data.id) }",
                           "select2:unselect" => "function(e) { removeProducts(e.params.data) }",
                        ]
                    ]);
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <?= 
                    // with ActiveForm
                    $form->field($form_model, 'positive_words')->widget(JqueryTagsInput::className(), [
                        // extra configuration
                    ]);
                 ?>
            </div>
        </div>
        
        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
        </div>
    </div>


<?php ActiveForm::end(); ?>