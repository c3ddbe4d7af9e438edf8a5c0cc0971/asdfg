<?php
class Detail{
	function __construct(){
		if(!Users::auth()){
			 Json::make('0','Access_denied')->withError(403)->response();
			 exit;
		}
	}
	public function update_details(){
		$details=Input::post(array('name','dob','mobile_number','country','gender','kroo_pic','profile_setting'));
		if(!$valdate=Validator::array_empty($details)){
			return Json::make('0',Validator::error())->withError(400)->response();
		}
		//var_dump($details['dob']);
		if($details['dob']){
		$details['dob']=date('Y-m-d',strtotime($details['dob']));	
		}
		//var_dump($details);
		$rules=array(
			'mobile_number'=>'mobile',
			);
		if(!$valdate=Validator::validate($details,$rules)){
			return Json::make('0',Validator::error())->withError(400)->response();
		}
		if(!empty($_FILES['kroo_pic'])){
			$base=$_SERVER['DOCUMENT_ROOT'].'/images';
			$ext=strrchr($_FILES['kroo_pic']['name'],".");
			$image_name=md5(uniqid().microtime()).$ext;
			$image_dest=$base."/".$image_name;
			$image_type=strtolower($_FILES['kroo_pic']['type']);
			$image_size=$_FILES['kroo_pic']['size'];
			$allow_type=array('image/gif','image/png','image/jpg','image/jpeg');
			if(in_array($image_type, $allow_type) and $image_size<30000000){
				if(!move_uploaded_file($_FILES['kroo_pic']['tmp_name'],$image_dest)){
					return Json::make('0','image not uploaded',array())->Witherror(304)->response();
				}
				$details['kroo_pic']=$image_name;
			}
		}
		$var=Details::update_details($details);
		$details['id']=Users::auth()->id;
		$var1=Details::u_details($details);	
		if(false!==$var and false!==$var1){
			return Json::make('1','details updated',$var1[0])->response();
		}
		return Json::make('0','server error',array())->Witherror(503)->response();
	}
	public function change_password(){
		$details=Input::post(array('email','password','new_password','repeat_password'));
		$rules=array(
			'email'=>'required|email',
			'password'=>'required',
			'new_password'=>'required',
			'repeat_password'=>'required',
			);
		if(!$validate=Validator::validate($details,$rules)){
			return Json::make('0',Validator::error())->Witherror(400)->response();
		}
		if($details['new_password']!==$details['repeat_password']){
			return Json::make('0','new_password and repeat_password should be same')->response();
		}
		$user=Users::auth();
		if($user->email===$details['email'] and password_verify($details['password'],$user->password)){
			$details['access_token']=md5(uniqid().microtime().$details['email']);
			$details['access_token_time']=date('Y-m-d H:i:s');
			$details['new_password']=password_hash($details['new_password'],PASSWORD_BCRYPT,['cost'=>10]);
			$var=Details::change_password($details);
			if(false!==$var){
				return Json::make('1','Password changed successfully please login with new password')->response();
			}
			return Json::make('0','server error',array())->Witherror(503)->response();
		}
		return Json::make('1','Email or password incorrect')->response();
	}
	public function user_details(){
		$user=Users::auth();
		if($user->type=='1'){
			$data=Details::stu_details($user->id);
		}else if($user->type=='2'){
			$data=Details::tea_details($user->id);
		}
		if(false!==$data){
			return Json::make('1','User_detail is ',$data)->response();
		}
		return Json::make('0','Server Error')->withError(503)->response();
	}

	public function getUser(){
		$details=Input::get(array('mobile','pendrive_num'));
		$rules=array(
			'mobile'=>'required|mobile',
			'pendrive_num'=>'required',
			);
		if(!$validate=Validator::validate($details,$rules)){
			return Json::make('0',Validator::error())->Witherror(400)->response();
		}
		$user=Details::getUser($details)[0]->id;
		$data=Details::stu_details($user);
		if(false!==$data){
			return Json::make('1','User_detail is ',$data)->response();
		}
		return Json::make('0','Server Error')->withError(503)->response();
	}








}