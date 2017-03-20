<?php
class Detail{
	function __construct(){
		if(!Users::auth()){
			 Json::make('0','Access_denied')->withError(403)->response();
			 exit;
		}
	}
	public function update_details(){
		$details=Input::post(array('name','dob','mobile','gender','pic'));
		if(!$valdate=Validator::array_empty($details)){
			return Json::make('0',Validator::error())->withError(400)->response();
		}
		if(!empty($details['dob'])){
		$details['dob']=date('Y-m-d',strtotime($details['dob']));	
		}
		$rules=array(
			'mobile'=>'mobile',
			);
		if(!$valdate=Validator::validate($details,$rules)){
			return Json::make('0',Validator::error())->withError(400)->response();
		}
		if(!empty($_FILES['pic'])){
			$base=$_SERVER['DOCUMENT_ROOT'].'/images';
			$ext=strrchr($_FILES['pic']['name'],".");
			$image_name=md5(uniqid().microtime()).$ext;
			$image_dest=$base."/".$image_name;
			$image_type=strtolower($_FILES['pic']['type']);
			$image_size=$_FILES['pic']['size'];
			$allow_type=array('image/gif','image/png','image/jpg','image/jpeg');
			if(in_array($image_type, $allow_type) and $image_size<30000000){
				if(!move_uploaded_file($_FILES['pic']['tmp_name'],$image_dest)){
					return Json::make('0','image not uploaded',array())->Witherror(304)->response();
				}
				$details['pic']=$image_name;
			}
		}
		$var=Details::update_details($details);
		$user=Users::auth();
		if($user->type=='1'){
			$data=Details::stu_details($user->id);
		}else if($user->type=='2'){
			$data=Details::tea_details($user->id);
		}
		if(false!==$var and false!==$data){
			return Json::make('1','details updated',$data[0])->response();
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
		$details=Input::get(array('mobile','email','type','id','pendrive_num'));
		$rules=array(
			'type'=>'required',
			);
		if(!$validate=Validator::validate($details,$rules)){
			return Json::make('0',Validator::error())->Witherror(400)->response();
		}
		$data=Details::user_details($details);
		if(false!==$data){
			return Json::make('1','User_detail is ',$data)->response();
		}
		return Json::make('0','Server Error')->withError(503)->response();
	}

	public function getTeacher(){
		$details=Input::get(array('mobile','pendrive_num','class','subject'));
		$rules=array(
			'class'=>'required',
			'subject'=>'required',
			);
		if(!$validate=Validator::validate($details,$rules)){
			return Json::make('0',Validator::error())->Witherror(400)->response();
		}
		$data=Details::getTeacher($details);
		if(false!==$data){
			return Json::make('1','Available teacher is ',$data)->response();
		}
		return Json::make('0','Server Error')->withError(503)->response();
	}
	public function changeTeacherStatus(){
		$details=Input::post(array('id','is_online'));
		$rules=array(
			'id'=>'required',
			'is_online'=>'required',
			);
		if(!$validate=Validator::validate($details,$rules)){
			return Json::make('0',Validator::error())->Witherror(400)->response();
		}
		if(false!==Details::changeTeacherStatus($details)){
			return Json::make('1',$details['is_online']==1?' teacher is Busy now':' teacher is Free now')->response();
		}
		return Json::make('0','Server Error')->withError(503)->response();

	}








}