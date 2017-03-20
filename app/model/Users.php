<?php

class Users extends Model{
	protected $table='users';
	protected static $auth=false;
	public static function auth(){
		if(!self::$auth){
			$details=Input::any('access_token');
			$sql="SELECT * from users where access_token=:access_token AND is_admin=1 AND DATEDIFF(curdate(),access_token_time)<".ACCESS_TOKEN_EXPIRY;
			self::$auth=(new self)->first($sql,array('access_token'=>$details));
		}
		return self::$auth;
	}
	public function update_token($details){
		$sql="UPDATE users set access_token_time=:access_token_time,access_token=:access_token where  email=:email or mobile=:email";
		return(new self)->update($sql,array('access_token_time'=>$details['access_token_time'],'email'=>$details['email'],'access_token'=>$details['access_token']));
	}
	public static function email_exist($details){
		$sql="SELECT email from users where email=:email";
		return (new self)->select($sql,array('email'=>$details['email']));
	}
	public static function signup($details){
		return (new self)->insert($details);

	}
	public static function login($details){
		$sql="SELECT *,DATEDIFF(curdate(),access_token_time) as `at` from users where (email=:email or mobile=:email)";
		return(new self)->select($sql,array('email'=>$details['email']));
	}

	public static function update_password($details){
		$sql="UPDATE users set password=:password where email=:email)";
		return (new self)->update($sql,array('password'=>$details['password'],'email'=>$details['email']));
	}
	
	


	
}
?>
