<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * SearchForm is the model behind the search form.
 */
class SearchForm extends Model
{
    public $name;
    public $keywords = [];
    public $products = [];
    public $web_resource = [];
    
    public $negative_words;
    public $positive_words;

    public $social_resources = [
        'TwitterApi' =>'Twitter',
    ];

    public $query_search;
    public $categories_dictionary = [];
   
   
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
            [['text_search','keywords','web_resource'], 'required', 'on' => 'scraping'],
            [['name','products','positive_words','negative_words','start_date','end_date'], 'required','message' => 'complete the fields', 'on' => 'live-chat'],
            // text_search has to be a valid string
            [['text_search'], 'string'],
            // start date needs to be entered correctly
            [['start_date','end_date'], 'date'],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['scraping'] = ['text_search','keywords','web_resource'];
        $scenarios['live-chat'] = ['products','positive_words','negative_words','start_date','end_date'];
        return $scenarios;
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'text_search' => 'Search text',
            'start_date' => 'start date',
            'start_end' => 'start end',
            'keywords' => 'keywords',
            'products' => 'Productos - Model - Code',
            'categories_dictionary' => 'categories dictionary',
            'positive_words' => 'positive words',
            'negative_words' => 'negative words',
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
