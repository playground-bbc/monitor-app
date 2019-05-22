<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * SearchForm is the model behind the search form.
 */
class SearchForm extends Model
{
    
    public $keywords = [];
    public $web_resource = [];
    public $negative_words = [
        'malo',
        'terrible'
    ];

    public $social_resources = [
        'Facebook',
        'Twitter',
        'web Page'
    ];

    public $query_search;
    /*
    public $query_search = [
        'and' => '&',
        'or' => '<>'
    ];

    */
    
   
    public $start_date;
    public $end_date;

    public $text_search;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // text_search  are required
            [['text_search'], 'required'],
            // text_search has to be a valid string
            [['text_search'], 'string'],
            // start date needs to be entered correctly
            [['start_date','end_date'], 'date'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'text_search' => 'texto de Busqueda',
            'start_date' => 'Fecha de Inicio',
            'start_end' => 'Fecha Final',
            'keywords' => 'Palabras Claves',
        ];
    }

    /**
     * Sends an email to the specified email address using the information collected by this model.
     * @param string $email the target email address
     * @return bool whether the model passes validation
    
    public function contact($email)
    {
        if ($this->validate()) {
            Yii::$app->mailer->compose()
                ->setTo($email)
                ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
                ->setReplyTo([$this->email => $this->name])
                ->setSubject($this->subject)
                ->setTextBody($this->body)
                ->send();

            return true;
        }
        return false;
    }
     */
}
