<?php 
use yii\widgets\DetailView;
use yii\helpers\ArrayHelper;
use app\models\Products;
use app\models\ProductsCategories;
use app\models\ProductsModels;



/*foreach ($modelMC as $key => $value) {
  echo($value->products);
}*/

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
        
    </div>
</div>
