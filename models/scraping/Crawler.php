<?php

namespace app\models\scraping;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;



use Goutte\Client;
use Stringizer\Stringizer;
use GuzzleHttp\Client as GuzzleClient;
use app\models\filebase\Filebase;

use app\models\Resource;
use app\models\api\BaseApi;


/**
 * Crawler is the model behind the logic from web scrapping.
 *
 */
class Crawler extends Model
{
    

    public $data = [];

    public $alertId;
    public $words;
    public $products_models;
    public $resources;
    public $start_date;
    public $end_date;

    private $_client;
    private $_filebase;

    // own Crawler search
    const WEB = 'WEB';

    /**
     * [countAndSearchWords call methods to count data for graphics]
     * @return [array] [return array with the data of the different methods]
     */
    public function countAndSearchWords()
    {
        $filebase = new Filebase();
        $filebase->alertId = $this->alertId;
        $db = $filebase->getFilebase();

        // we go through the json 
        $data = $db->field('data');
        // get WEB del Array
        $data = ArrayHelper::getValue($data,'WEB');
        
        $model = [];

        if ($data) {
            $countByCategoryInWeb['countByCategoryInWeb'] = $this->countWordsInWebByCategory($data);
            if (!is_null($countByCategoryInWeb['countByCategoryInWeb'])) {
                $sentences['sentences_web'] = $this->addTagsSentenceFoundInWeb($data);
                $countWords['countWords_web'] = $this->addWordsInWeb($data);
                //join webs
                $model['web'] = ArrayHelper::merge($sentences,$countWords);
                $model['web'] = ArrayHelper::merge($countByCategoryInWeb,$model['web']);
            }
        }
        return $model;
    }

    /**
     * [rules for scrapping a webpage]
     * @return [array] [title => rule xpath]
     */
    public function rules()
    {
        return [
            '//title'       => Yii::t('app','document_title'),
            '//h1'          => Yii::t('app','cabezera_1'),
            '//h2'          => Yii::t('app','cabezera_2'),
            '//h3'          => Yii::t('app','cabezera_3'),
            '//h4'          => Yii::t('app','cabezera_4'),
            '//h5'          => Yii::t('app','cabezera_5'),
            '//strong'      => Yii::t('app','negrita'),
            '//a'           => Yii::t('app','link'),
            '//span'        => Yii::t('app','contenedor'),
            '//li'          => Yii::t('app','ítem'),
            '//address'     => Yii::t('app','address'),
            '//div/article' => Yii::t('app','cabezera_2'),
            '//aside'       => Yii::t('app','aside'),
            '//hgroup'      => Yii::t('app','hgroup'),
            '//p'           => Yii::t('app','paragraph'),
            '//footer/div'  => Yii::t('app','footer'),
        ];
    }
    /**
     * [sendRequest return client request]
     * @param  [string] $url [url webpage]
     * @return [object]  
     */
    public function sendRequest($url)
    {
      $client = $this->_client->request('GET',$url);
      
      return $client;
    }

    /**
     * [getResouceUri difference if it is a web resource and sorts it by domain and url]
     * @return  
     */
    private function getResouceUri()
    {
        $resources = [];
        

        for ($r=0; $r <sizeof($this->resources) ; $r++) { 
            $is_web = Resource::isWebResource($this->resources[$r]);
            if ($is_web) {
                $models =  Resource::find()->where(['name' => $this->resources[$r]])->select('url')->all();
                $urls = [];
                foreach ($models as $resource => $model) {
                    if (!in_array($model->url, $urls)) {
                        $urls[] = $model->url;
                    }
                }
                $resources[$this->resources[$r]] = $urls;
            }
        }
        
        return $resources;
    }
    /**
     * [getRequest send request to urls and if status is 200 return object request by domain and url]
     * @param [array] $resources [array conform by exanple: google => www.google.com]
     */
    private function getRequest($resources)
    {
        $crawler = [];

        foreach ($resources as $domain => $urls) {
            for ($u=0; $u <sizeof($urls) ; $u++) { 
                $client = $this->sendRequest($urls[$u]);
                $status_code = $this->_client->getResponse()->getStatus();
                if ($status_code == 200) {
                    $content_type = $this->_client->getResponse()->getHeader('Content-Type');
                    if (strpos($content_type, 'text/html') !== false) {
                        $crawler[$domain][$urls[$u]] = $client;
                    }
                }
            }
        }

        return $crawler;
        
    }
    /**
     * [getContent filters by defined rules xpath in rules]
     * @param  [array] $crawler [defined in getRequest function]
     * @return [array] $contents [array with the content of the web pages defined by the labels]
     */
    private function getContent($crawler)
    {
        $contents = [];
        $attributes = ['_name', '_text', 'id'];
        

        foreach ($crawler as $domain => $urls) {
            foreach ($urls as $url => $craws) {
                for ($c=0; $c <sizeof($craws) ; $c++) { 
                    foreach ($this->rules() as $rule => $title) {
                        $contents[$domain][$url][$title][] = $craws->filterXpath($rule)->each(function ($node,$i)
                        {
                            $sentences = new Stringizer($node->text());

                            if (!$sentences->isBlank()) {
                                return [
                                    'id' => $node->extract(['id']),
                                    '_text' =>  $sentences->trim(),
                                ];
                            }
                            return null;

                        });
                    }
                }
            }
        }
        return $contents;
    }
    /**
     * [setContent set the content take values different to null, order by id and text]
     * @param [array] $contents [contents the web pages]
     */
    private function setContent($contents)
    {
        $model = [];

        foreach ($contents as $domain => $webpages) {
            foreach ($webpages as $webpage => $labels) {
                foreach ($labels as $label => $data) {
                    if (!empty($data)) {
                        for ($l=0; $l <sizeof($data) ; $l++) { 
                            if (!ArrayHelper::isAssociative($data[$l])) {
                                if (count($data[$l])) {
                                    for ($i=0; $i <sizeof($data[$l]) ; $i++) { 
                                        
                                        if (!empty($data[$l][$i]['_text'])) {
                                            $section['id'] = $data[$l][$i]['id']; 
                                            $section['_text'] = trim($data[$l][$i]['_text']); 
                                            $model[$domain][$webpage][$label][] = $section; 
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

        }

        return $model;
    }
    
    /**
     * [searchProductsInContent search in the content the web pages any products from the alert]
     * @param  [array] $model [the content the web page order]
     * @return [array]        [content filter by products]
     */
    private function searchProductsInContent($model)
    {
        $searchs = [];
     
        // searchs by procuts
        foreach ($model as $domain => $webpages) {
            foreach ($webpages as $webpage => $labels) {
                foreach ($labels as $label => $data) {
                    for ($d=0; $d <sizeof($data) ; $d++) { 
                        $sentence = new Stringizer($data[$d]['_text']);
                        foreach ($this->setProducts() as $products => $product) {
                            if ($sentence->containsCountIncaseSensitive($product)) {
                                $data[$d]['product'] = $product;
                                $searchs[$domain][$webpage][$label][] = $data[$d];
                            }
                        }
                    }
                }
            }
        }
       
        return $searchs;
    }

    /**
     * [searchWordsInContent search by words in the alert dictionaries]
     * @param  [array] $searchs [content filter by products]
     * @return [array]          [content filter by words]
     */
    private function searchWordsInContent($searchs)
    {
        $contains = [];
      
        // search by words
        foreach ($searchs as $domain => $webpages) {
            foreach ($webpages as $webpage => $labels) {
                foreach ($labels as $label => $data) {
                    for ($d=0; $d <sizeof($data) ; $d++) { 
                        $sentence_in_word = new Stringizer($data[$d]['_text']);
                        foreach ($this->words as $dictionary => $word) {
                            for ($w=0; $w <sizeof($word) ; $w++) { 
                                if ($sentence_in_word->containsCountIncaseSensitive($word[$w])) {
                                    $data[$d]['word'] = $word[$w];
                                    $contains[$domain][$webpage][$label][] = $data[$d];
                                }
                            }
                        }
                    }
                }
            }
        }
        
        return $contains;
    }
    /**
     * [setSearchDataWebPage standardizes the data to save in the json]
     * @param [array] $contains [all data filter]
     */
    private function setSearchDataWebPage($contains)
    {
        $this->data = [];

        foreach ($contains as $domains => $uris) {
            foreach ($uris as $uri => $labels) {
                foreach ($labels as $label => $value) {
                    for ($v=0; $v <sizeof($value) ; $v++) { 
                        if (!in_array($value[$v]['product'], $this->data)) {
                           // $this->data[] = $value[$v]['product'];
                            $temp['source'] = 'WEB';
                            $temp['tag'] = $label;
                            $temp['url'] = $uri;
                            $temp['created_at'] = "-";
                            $temp['author_name'] = "-";
                            $temp['author_username'] = "-";
                            $temp['post_from'] = $value[$v]['_text'];
                            //$this->data[$value[$v]['product']][] = $temp;
                            $this->data['WEB'][$value[$v]['product']][] = $temp;
                        }
                    }
                }
            }
        }
        
        return $this->data;
    }

    /**
     * [getData return all data filter]
     * @return [array]
     */
    private function getData()
    {
        return $this->data;
    }

    /**
     * [setProducts order the products in array more iterable]
     */
    private function setProducts()
    {
        $products = [];
        foreach ($this->products_models as $key => $value) {
            $products[] = $key;
            foreach ($value as $model => $serial_model) {
                $products[] = $model;
                foreach ($serial_model as $serial => $model_value) {
                    $products[] = $model_value;
                }
            }
        }
        return $products;
    }

    /**
     * [saveJsonFile save content in a json file]
     */
    private function saveJsonFile()
    {
        $data = $this->getData();
        $this->_filebase->save($data);
    }

    /**
     * [countWordsInWebByCategory add word in to category]
     * @param  [array] $data [data in json file section WEB]
     * @return [array]       ['products or models' => 'dictionary_category_1' => int 1,'dictionary_category_2' => int 0]
     */
    private function countWordsInWebByCategory($data)
    {
        // set array analisys Model => dictionary_title => word
        $products = array_keys($data);
        $countByCategory = [];
        for ($i=0; $i <sizeof($products) ; $i++) { 
            foreach ($this->words as $categories => $words) {
                $countByCategory[$products[$i]][$categories] = 0;
            }
        }
       
        
        foreach ($data as $model => $value) {
            for ($i=0; $i <sizeof($value) ; $i++) { 
                if ($value[$i]['source'] == self::WEB) {
                    $stringizer = new Stringizer($value[$i]['post_from']);
                    foreach ($this->words as $categories => $words) {
                        for ($j=0; $j <sizeof($words) ; $j++) { 
                            if ($stringizer->containsCountIncaseSensitive($words[$j])) {
                               $countByCategory[$model][$categories] += $stringizer->containsCountIncaseSensitive($words[$j]) ;
                            }
                        }
                    }
                }
            }
        }


       return (count($countByCategory)) ? $countByCategory : null;
    }
    /**
     * [addTagsSentenceFoundInWeb add tags html in post_from]
     * @param [array] $data 
     * @return [array] $sentences
     */
    private function addTagsSentenceFoundInWeb($data)
    {
        $sentences = [];
        foreach ($data as $model => $value) {
            
            for ($i=0; $i <sizeof($value) ; $i++) { 
                if ($value[$i]['source'] == self::WEB) {
                    $stringizer = new Stringizer($value[$i]['post_from']);
                    $tmp = [];
                    foreach ($this->words as $categories => $words) {
                        for ($w=0; $w <sizeof($words) ; $w++) { 
                            if ($stringizer->containsIncaseSensitive($words[$w])) {
                                $background = BaseApi::COLOR[$categories];
                                $sentence = (array) $stringizer->replaceIncaseSensitive($words[$w], "<span style='background: {$background}'>{$words[$w]}</span>");
                                $value[$i]['post_from'] = array_values($sentence);
                                $value[$i]['product'] = $model;
                                $tmp[] = $value[$i];
                            }
                        }
                    }
                 if (!empty($tmp)) {
                        $sentences[] = end($tmp);
                    }   
                }
            }
        }
        
        return $sentences;
    }

    /**
     * [addWordsInWeb add words found]
     * @param [array] $data 
     * @return [array] $model ['products or models' => 'dictionary_category_1' => word_name => 1,'dictionary_category_2' => word_name => 4]
     */
    private function addWordsInWeb($data)
    {
        // set array analisys Model => dictionary_title => word
        $products = array_keys($data);
        $countByWords = [];
        for ($i=0; $i <sizeof($products) ; $i++) { 
            foreach ($this->words as $categories => $words) {
                for ($j=0; $j <sizeof($words) ; $j++) { 
                    $countByWords[$products[$i]][$categories][$words[$j]] = 0;
                }
            }
        }

        foreach ($data as $products => $value) {
            for ($i=0; $i <sizeof($value) ; $i++) { 
                if ($value[$i]['source'] == self::WEB) {
                    $stringizer = new Stringizer($value[$i]['post_from']);
                    foreach ($this->words as $categories => $words) {
                        for ($j=0; $j <sizeof($words) ; $j++) { 
                            if ($stringizer->containsCountIncaseSensitive($words[$j]) && isset($countByWords[$products][$categories][$words[$j]])) {
                                $countByWords[$products][$categories][$words[$j]] += $stringizer->containsCountIncaseSensitive($words[$j]) ;
                            }
                        }
                    }
                }
            }
        }

        
        $model = [];
        foreach ($countByWords as $products => $categories_words) {
            foreach ($categories_words as $words => $word) {
                foreach ($word as $key => $value) {
                    if ($value) {
                        $model[$products][$words][$key] = $value;
                    }
                }
            }
        }
        unset($countByWords);

        return $model;
    }



    public function __construct($params) {
        

        $this->_client = new Client();
        $this->_filebase = new Filebase();

        foreach ($params as $key => $value) {
            $this->$key = $value;
        }

        $this->_filebase->alertId = $this->alertId;

        $resources = $this->getResouceUri();
        
        if ($resources) {

            $crawler  = $this->getRequest($resources);
            $contents = $this->getContent($crawler);
            $model    = $this->setContent($contents);
            $searchs  = $this->searchProductsInContent($model);
            $contains = $this->searchWordsInContent($searchs);
            $data = $this->setSearchDataWebPage($contains);
            if ($data) {
                $this->saveJsonFile();
            }
        }


        
        parent::__construct();
    }



}
