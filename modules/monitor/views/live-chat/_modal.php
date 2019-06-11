<?php 
use app\models\CategoriesDictionary;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use kartik\file\FileInput;
use yii\helpers\Url;

$categories_dictionary = ArrayHelper::map(CategoriesDictionary::find()->all(),'id','name');


$form1 = ActiveForm::begin([
    'options'=>['enctype'=>'multipart/form-data'] // important
]);

echo $form1->field($form_model, 'categories_dictionary')->label(false)->widget(Select2::className(), [
    'data' => $categories_dictionary,
    'options' => [
        'placeholder' => 'Choose tag ...',
        'multiple'=> false
    ],
    
]); 
echo FileInput::widget([
	'name'=>'kartiks_file',
	'pluginOptions' => [
    'uploadUrl' => Url::to(['/monitor/live-chat/upload']),
    'uploadExtraData' => [
            'album_id' => 20,
            'cat_id' => 'Nature'
        ],
    'maxFileCount' => 10
   ]

]);
ActiveForm::end();
?>