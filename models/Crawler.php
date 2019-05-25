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
    public $keywords = [];
    public $web_resource = [];
    

    public $data = [];

    private $_client;


    /*
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // text_search  are required
            [['text_search','keywords','web_resource'], 'required'],
         //   [['keywords','web_resource'], 'save'],
            
        ];
    }

    public function scraping()
    {
        foreach ($this->web_resource as $key => $value) {
            $crawler = $this->_client->request('GET', $value);
            $this->data[] = $value;
        }
        return $this->data;
       /*
      $resource = Resource::findOne($id);
      $client = new Client();
      $crawler = $client->request('GET', 'https://www.yiiframework.com/doc/guide/2.0/en/db-migrations');
       //var_dump($crawler);
       //die();
    
      $name = $resource->name;
      $searchword = "Tim";
      //$out = [];
      //$out = $crawler->evaluate("//p[text()[contains(.,".$searchword .")]]")->each(function ($node) use($searchword)
      $out = $crawler->evaluate("//p[text()[contains(.,".$searchword .")]]")->each(function ($node) use($searchword)
      {
        if (preg_match("/".$searchword."/i",$node->text())) {
          return $node->text();
        }
        
      });
     // $stripped = preg_replace('/\s+/', ' ', $out[0]);
      echo "<pre>";
      var_dump(array_filter($out));
      echo "</pre>";
      die();
      */
    }

    public function get_content($crawler, $patterns = [''])
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
