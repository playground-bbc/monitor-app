<?php 
use yii\helpers\Html;

 ?>

<div class="monitor-default-create">
    <h1><?= $this->context->action->uniqueId ?></h1>
    <p>
        You may customize this page by editing the following file:<br>
        <code><?= __FILE__ ?></code>
    </p>
    <p>
        <?= $this->render('_form',[
	        'model' => $model,
	        'categories' => $categories
	      ])
	    ?>
    </p>
</div>
