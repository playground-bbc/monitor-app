<?php 
namespace app\models\api;

use Yii;
use yii\base\Model;
use Codebird\Codebird;
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
        //return $this->redirect_to_auth_website_to_view();
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
       /* // send to same URL, without oauth GET parameters
        header('Location: ' . basename(__FILE__));
        die();*/

    }


    public function search_tweets($params)
    {

        $this->twitter->setReturnFormat(CODEBIRD_RETURNFORMAT_ARRAY);
        set_time_limit(500); // 
        return $this->twitter->search_tweets($params,true);
         
    }

    public function search_tweets_by_date($params)
    {
        $data =[];
        $index = 0;

        do {
                $temp = $this->search_tweets($params);
                if (!empty($temp['statuses'])) {
                    sleep(1);
                    $data[$index]  = $this->search_tweets($params);
                }
                
                
                if (isset($data[$index]['search_metadata']['next_results'])) {
                    parse_str($data[$index]['search_metadata']['next_results'], $output);
                    $params['max_id'] = $output['?max_id'];


                }


                     
                $index ++;

        } while ($params['max_id'] != $temp['search_metadata']['max_id']);

        

        return $data;

    }


    public function __construct() {
        Codebird::setConsumerKey(Yii::$app->params['twitter']['api_key'], Yii::$app->params['twitter']['api_secret_key']);
        $this->twitter = Codebird::getInstance();
        
        parent::__construct();
    }

       
}



 ?>