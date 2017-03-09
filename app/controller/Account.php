<?php
class Account
{
	function __construct() {
		/*if(!Auth::authorized()){
			Json::make('0','not authorized')->withError(array(401))->response();
			exit;
		}*/
	}
	


	public function signup(){
		$details=Input::post(array('email','password','name'));
		$rules=array(
			'email'		 =>"required|email",
			'password'   =>'required',
			'name'=>'required'
			);
		if(!$validate=Validator::validate($details,$rules)){
			return Json::make('0',Validator::error())->withError(400)->response();
		}
		$var=Users::email_exist($details);
		if($var!==false and !empty($var)){
			return Json::make('0','Email Id already exist please login')->withError(400)->response();
		}
		$details['password']=password_hash($details['password'],PASSWORD_BCRYPT,['cost'=>10]);
		$details['access_token']=md5(uniqid().microtime().uniqid().$details['email']);
		$details['access_token_time']=date('Y-m-d H:i:s');
		$var=Users::signup($details);
		if(false!==$var){
			return Json::make('1','Registration successful',array('access_token'=>$details['access_token'],'id'=>$var))->response();
		}
		return Json::make('0','Server Error')->withError(503)->response();
	}
	public function login(){
		$details=Input::post(array('email','password'));
		$rules=array(
			'email'=>'required|mobile_email',
			'password'=>'required'
			);
		if(!$validate=Validator::validate($details,$rules)){
			return Json::make('0',Validator::error())->withError(400)->response();
		}
		$id=Users::login($details);
		if(false!==$id){
			if(!empty($id) and password_verify($details['password'],$id[0]->password)){
				$details['id']=$id[0]->id;
				if($id[0]->at>=ACCESS_TOKEN_EXPIRY){
					$details['access_token']=md5(uniqid().microtime().uniqid().$details['email']);
					$details['access_token_time']=date('Y-m-d H:i:s');
					$var=Users::update_token($details);
					if(false!==$var){
						return Json::make('1','Login successful',array('access_token'=>$details['access_token']))->response();
					}
					return Json::make('0','Server Error')->withError(503)->response();
				}
				
				return Json::make('1','Login successful',array('access_token'=>$id[0]->access_token))->response();
			}
			return Json::make('0','email or mobile number or password incorrect')->withError(400)->response();

		}
		return Json::make('0','Server Error')->withError(503)->response();
	}
	public function send_temp_password(){
		$details=Input::post(array('email'));
		$rules=array(
			'email'=>'required|mobile_email',
			);
		if(!$validate=Validator::validate($details,$rules)){
			return json::make('0',Validator::error())->withError(400)->response();
		}
		$email=User_details::mobile_email_exist($details);
		if(false!==$email){
			if(!empty($email)){
				$details['id']=$email[0]->id;
				$details['temp_password']=uniqid();
				$details['temp_password_time']=date('Y-m-d H:i:s');
				$temp_pass=User_details::temp_password($details);//insert/update temp password in database
				//send random password to user mobile or email if get success then send to reset page
				$success=1;
				if($success and false!==$temp_pass){
					return Json::make('1','Temporary password sent successfully please set new password')->response();
				}
				return Json::make('0','Some Error occurred')->withError(503)->response();
			}
			return json::make('0','Please enter valid mobile number or email')->withError(400)->response();
		}
		return Json::make('0','Server Error')->Witherror(503)->response();

	}
	public function set_new_password(){
		$details=Input::post(array('email','temp_password','new_password','repeat_password'));
		$rules=array(
			'email'=>'required|mobile_email',
			'temp_password'=>'required',
			'new_password'=>'required',
			'repeat_password'=>'required',
			);
		if(!$validate=Validator::validate($details,$rules)){
			return Json::make('0',Validator::error())->withError(400)->response();
		}
		if($details['new_password']!=$details['repeat_password']){
			return Json::make('0','new_password and repeat_password should be same')->withError(400)->response();
		}
		if(User_details::get_temp_password($details)[0]->temp_password!==$details['temp_password']){
			return Json::make('0','incorrect Temporary Password')->withError(400)->response();
		}
		$details['new_password']=password_hash($details['new_password'],PASSWORD_BCRYPT,['cost'=>10]);
		$var=User_details::set_new_password($details);
		if(false!==$var){
			if($var['expire']==1){
				return Json::make('0','Temporary Password expired please resend Temporary Password')->response();
			}
			return Json::make('1','Please Login with New password')->response();
		}
		return Json::make('0','Server Error')->withError(503)->response();

	}
}
?>
