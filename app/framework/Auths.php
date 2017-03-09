<?php 
/**
* 
*/
class Auths
{
    public function __construct(){
        
    }
    
    private static function getKey($public_key){
        if (!$public_key) {
            return false;
        }
        $data=(new Model)->first("SELECT * FROM auth WHERE public_key=? AND is_active=1",[$public_key]);
        return $data?:false;
    }

    private static function generateAuthorization(){
        $public_key=Input::any('public_key');
        $nonce=Input::any('nonce');
        $keys=self::getKey($public_key);
        if (!$keys) {
            return false;
        }
        $data=$public_key.$nonce.$keys->private_key;
        return self::hash($data,$keys->private_key);
    }

    private static function hash($data,$key){
        return hash_hmac('sha256', $data, $key);
    }

    public static function isAuthorized(){
        $shash=self::generateAuthorization();
        $chash=self::getAuthorization();
        if (!$shash || !$chash) {
            return false;
        }
        return $shash===$chash;
    }

    private static function getAuthorization(){
        return Input::any('authorization')?:false;
    }
    public static function getReqestToken($token){
        if (!$token) {
            return false;
        }
        $data= (new Model)->first("SELECT * FROM request_token WHERE token=?",[$token]);
        if(!$data){
            return false;
        }
        $data->auth=(new Model)->first("SELECT * FROM auth WHERE id=?",[$data->auth_id]);
        return $data;
    }
    public static function setRequestToken(){
        $public_key=Input::any('public_key');
        $key=self::getKey($public_key);
        $token=hash_hmac('sha256', $public_key.time().uniqid(), $key->private_key);
        $model=new Model;
        if($model->insert(['token'=>$token,'auth_id'=>$key->id],'request_token')){
            return $token;
        }
        return false;
    }
    public static function isAuthenticated(){
        $request_token=self::getReqestToken(Input::any('request_token'));
        if (!$request_token) {
            return false;
        }
        if ($request_token->is_active==1 && $request_token->auth->is_active==1) {
            return $request_token;
        }
        return false;
    }
    public function deniedResponse(){
        Json::make('0','Un-authorized Access')->withError(403)->response();
        die;
    }
    public function isValidRequestToken(){
        $request_token=Auths::getReqestToken(Input::any('request_token'));
        if ($request_token->id && !(new Model)->first("SELECT * FROM access_token WHERE request_token_id=?",[$request_token->id])) {
            return true;
        }
        return false;
    }
}
?>