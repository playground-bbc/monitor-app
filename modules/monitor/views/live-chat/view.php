<?php 
use yii\grid\GridView;
use yii\helpers\Html;   
use yii\helpers\ArrayHelper;
use app\models\Products;
use app\models\ProductsCategories;
use app\models\ProductsModels;

use yii\widgets\Pjax;


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
        <?php Pjax::begin(); ?>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                'id',

              //  'alertId',
                [   
                    'filter' => true,
                    'header'=> Yii::t('app', 'modelos'),
                    'value' => function ($model)
                    {
                        return $model->productModel->serial_model;
                    }
                ],
                'createdAt',
                'updatedAt',
                //'createdBy',
                //'updatedBy',

                ['class' => 'yii\grid\ActionColumn'],
            ],
        ]); ?>

        <?php Pjax::end(); ?>
    </div>
</div>
