<?php
class Auth 
{   
    private static $table='auth';
    private static $db;
    public static function authorized(){
        $header=apache_request_headers();
        $authorization=isset($header['Authorization'])?$header['Authorization']:'';
        $public_key=Input::any('public_key');
        $time=Input::any('time');
        if($private_key=self::get_private_key($public_key)){
            $data=$public_key.$time.$private_key;
            $hash=self::hash($data,$private_key);
            return $hash===$authorization?true:false;    
        }
        return false;    
    }
    private static function get_private_key($public_key){
        self::$db=DB::getInstance();
        $table=self::$table;
        $sth=self::$db->prepare("SELECT private_key FROM $table WHERE public_key=?");
        $sth->execute(array($public_key));
        if($sth->rowCount()){
            $result=$sth->fetch(PDO::FETCH_OBJ);
            return $result->private_key;
        }
        return false;
    }
    protected static function hash($data,$key){
        return hash_hmac(HASH_ALGO, $data, $key);
    }
}
?>