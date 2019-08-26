<?php 
namespace app\models\api;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

use Codebird\Codebird;

set_time_limit(500); // 
/**
 * TwitterApi is the model behind the login API.
 *
 */
class TwitterApi extends Model
{
    private $twitter;
    public $username;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
        ];
    }



    public function authenticate()
    {
        $reply = $this->twitter->oauth_requestToken([
          'oauth_callback' => 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']
        ]);
        // store the token
        $this->twitter->setToken($reply->oauth_token, $reply->oauth_token_secret);
        Yii::$app->session->set('oauth_token_twitter',$reply->oauth_token);
        Yii::$app->session->set('oauth_token_secret_twitter',$reply->oauth_token_secret);
        Yii::$app->session->set('oauth_verify_twitter',true);

        $this->redirect_to_auth_website();
    }

    public function logout()
    {
        $this->twitter->logout();
        Yii::$app->session->remove('oauth_token_twitter');
        Yii::$app->session->remove('oauth_token_secret_twitter');
        Yii::$app->session->remove('oauth_verify_twitter');
        Yii::$app->session->remove('oauth_verify');
    }


    public function redirect_to_auth_website()
    {
        $auth_url = $this->twitter->oauth_authorize();
        header('Location: ' . $auth_url);
        die();
    }


    
    public function redirect_to_monitor()
    {
        $cb->setToken(Yii::$app->session->get('oauth_token_twitter'), Yii::$app->session->get('oauth_token_secret_twitter'));
        Yii::$app->session->remove('oauth_verify');

          // get the access token
        $reply = $cb->oauth_accessToken([
            'oauth_verifier' => $_GET['oauth_verifier']
        ]);

        // store the token (which is different from the request token!)
        Yii::$app->session->set('oauth_token_twitter',$reply->oauth_token);
        Yii::$app->session->set('oauth_token_secret_twitter',$reply->oauth_token_secret);

    }


    public function search_tweets($params)
    {

        $this->twitter->setReturnFormat(CODEBIRD_RETURNFORMAT_ARRAY);
        return $this->twitter->search_tweets($params,true);
         
    }

    public function search_tweets_by_date($params)
    {
        $data =[];
        $index = 0;

        do {
                 
                $data[$index]  = $this->search_tweets($params);

                if(!ArrayHelper::keyExists('search_metadata', $data[$index], false)){
                    $params['max_id'] =  0;
                    $next_results = 0;
                }

                if (ArrayHelper::keyExists('next_results', $data[$index]['search_metadata'], false)) {
                    parse_str($data[$index]['search_metadata']['next_results'], $output);
                    $params['max_id'] = $output['?max_id'];
                    $next_results = $data[$index]['search_metadata']['max_id_str'];
                }else{
                    $params['max_id'] =  $data[$index]['search_metadata']['max_id_str'];
                    $next_results = $data[$index]['search_metadata']['max_id_str'];
                }

                $index ++;


        } while ($params['max_id'] != $next_results && $index <= 10);

        
        return $data;

    }

    public function getBearerToken(){
        $reply = $this->twitter->oauth2_token();
        $bearer_token = $reply->access_token;
        return $bearer_token;
    }

    public function oauth2_invalidateToken(){
        $this->twitter->oauth2_invalidateToken();
    }

    public function __construct() {
        //Codebird::setConsumerKey(Yii::$app->params['twitter']['api_key'], Yii::$app->params['twitter']['api_secret_key']);
        Codebird::setBearerToken(Yii::$app->params['twitter']['bearer_token']);
        $this->twitter = Codebird::getInstance();
        
        parent::__construct();
    }

       
}



 ?>