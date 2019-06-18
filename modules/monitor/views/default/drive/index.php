<?php 
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;


 ?>
<div class="monitor-default-index">
    <h1><?= $this->context->action->uniqueId ?></h1>
    <p>
        This is the view content for action "<?= $this->context->action->id ?>".
        The action belongs to the controller "<?= get_class($this->context) ?>"
        in the "<?= $this->context->module->id ?>" module.
    </p>
    
    <div class="row">
        <div class="col-md-12">
            <div class="well well-large">
                <div class="text-center">
                    <?= Html::a(Html::tag('i', 'Sync Drive', ['class' => 'btn btn-info']) . ' ', ['sync'], ['class' => 'btn btn-black', 'title' => 'Sync']) ?>
                </div>
            </div>
        </div>
    </div>
</div>
