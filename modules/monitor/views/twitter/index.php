<?php 
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;

$moduleName = $this->context->action->id;
$word = Yii::$app->session->get('key');

if (\Yii::$app->request->get('page')) {
    $reply = \Yii::$app->cache->get(Yii::$app->session->get('key'));
    $reply = $reply['statuses'];  
}
 ?>

<div class="monitor-default-index">
    <div class="jumbotron">
        <h1>Monitor Social Media!</h1>

        <p class="lead">Get started Social Media.</p>

        <p><?= Html::a('Logout', Url::to(['twitter/logout']), ['class' => 'btn btn-lg btn-success']) ?></p>
    </div>
    <h1><?= $this->context->action->uniqueId ?></h1>
    <p>
        This is the view content for action "<?= $this->context->action->id ?>".
        The action belongs to the controller "<?= get_class($this->context) ?>"
        in the "<?= $this->context->module->id ?>" module.
    </p>
    <div class="body-content">

        <div class="row">
            <div class="col-lg-12">
                <?= Html::beginForm(Url::to(['index']),'post',['class' => 'form-group']); ?>
                <?= Html::input('search', 'search_twitter',$word,['class' => 'form-control']); ?>
                <div class="form-group" style="padding-top: 10px ">
                    <?= Html::submitButton('Buscar', ['class' => 'btn btn-primary']); ?>
                </div>
                <?= Html::endForm(); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <h2>Twitter</h2>

                <?= GridView::widget([
                    'dataProvider' => new \yii\data\ArrayDataProvider([
                        'allModels' => (!empty($reply)) ? $reply : [] ,
                        //'allModels' => $reply,
                        //'key' => 'id',
                        'sort' => [
                            'attributes' => ['id','user.screen_name'],
                        ],
                        /*
                        'pagination' => [
                                'pageSize' => 10
                            
                        ],
                        */
                    ]),
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        // Simple columns defined by the data contained in $dataProvider.
                        // Data from the model's column will be used.
                        'id',
                        [
                            'format' => 'image',
                            'value'=>function($data) { return $data['user']['profile_image_url']; },
                        ],
                       // 'entities.media.urls.url',
                        'created_at',
                        [
                            'header'=> Yii::t('app', 'Username'),
                            'value' => 'user.screen_name'
                        ],
                        
                        'text'
                    ],
                ]);
                 ?>
            </div>
        </div>
    </div>
</div>
