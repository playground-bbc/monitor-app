<?php

/* @var $this yii\web\View */

$this->title = 'My Yii Application';
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

use yii\data\ArrayDataProvider;


/*
echo "<pre>";
var_dump($reply);
die();
echo "</pre>";
*/
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Monitor Social Media!</h1>

        <p class="lead">Get started Social Media.</p>

        <p><?= Html::a('Logout', ['logout'], ['class' => 'btn btn-lg btn-success']) ?></p>
    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-12">
                <?= Html::beginForm([Url::to('index')],'post',['class' => 'form-group']); ?>
                <?= Html::input('search', 'search_twitter','',['class' => 'form-control']); ?>
                <div class="form-group" style="padding-top: 10px ">
                    <?= Html::submitButton('Buscar', ['class' => 'btn btn-primary']); ?>
                </div>
                <?= Html::endForm(); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <h2>Twitter</h2>

                <?php 
                    echo GridView::widget([
                    'dataProvider' => new \yii\data\ArrayDataProvider([
                        'allModels' => (!empty($reply)) ? $reply : [] ,
                        'sort' => [
                            'attributes' => ['id','user.name'],
                        ],
                    ]),
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        // Simple columns defined by the data contained in $dataProvider.
                        // Data from the model's column will be used.
                        'id',
                        [
                            'format' => 'image',
                            'value'=>function($data) { return $data->user->profile_image_url; },
                        ],
                        'created_at',
                        'user.name',
                        'text'
                    ],
                ]);
                 ?>
            </div>
        </div>

    </div>
</div>
