<?php 
use yii\helpers\Html;

 ?>

<div class="monitor-default-index">
    <h1><?= $this->context->action->uniqueId ?></h1>
    <p>
        <?= Html::a('<i class="fa fa-link"></i> New Url',['scraping/create'], ['class' => 'btn btn-info', 'title' => 'New Url']) ?>
    </p>
    
    <p>
        You may customize this page by editing the following file:<br>
        <code><?= __FILE__ ?></code>
    </p>
</div>
