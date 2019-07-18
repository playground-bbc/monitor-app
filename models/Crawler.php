<?php

namespace app\models;

use Yii;
use yii\base\Model;

use Goutte\Client;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class Crawler extends Model
{
    

    public $data = [];

    private $_client;


    /*
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
        ];
    }

    public function scraping()
    {
      $crawler = $this->_client->request('GET','https://www.yiiframework.com/doc/guide/2.0/en/db-migrations');
      
      return $this->data;
    }

    public function get_content($crawler, $value)
    {
        $out = [];


    }

    public function get_rules($type_rule,$word)
    {
        $rule = [
            'text_paragraph' => "//p[text()[contains(.,".$word .")]]",
            'text_title_h1' => "//h1[text()[contains(.,".$word .")]]",
        ];

        return (isset($rule[$type_rule])) ? $rule[$type_rule] : null ;
    }



    public function __construct() {
        $this->_client = new Client();
        parent::__construct();
    }



}
