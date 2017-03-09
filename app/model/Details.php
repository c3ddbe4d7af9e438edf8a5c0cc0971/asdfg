<?php
class Details extends model{
	protected static $editable=array('name','dob','mobile_number','country','gender','kroo_pic','profile_setting');
	public static function update_details($details){
		$sql="UPDATE users SET ";
		foreach ($details as $key => $value) {
			if(!in_array($key, self::$editable) or $value==""){
				unset($details[$key]);
				continue;
			}
			$sql.="`".$key."`".'=:'.$key.',';
		}
		$sql=rtrim($sql,',');
		$sql.=" WHERE id=:id";
		$details['id']=Users::auth()->id;
		return (new self)->update($sql,$details);
	}
	public static function change_password($details){
		$details['id']=Users::auth()->id;
		$sql="UPDATE users set password=:password , access_token=:access_token, access_token_time=:access_token_time where id=:id";
		return (new self)->update($sql,array('password'=>$details['new_password'],'access_token'=>$details['access_token'],'access_token_time'=>$details['access_token_time'],'id'=>$details['id']));
	}

	public static function mobile_email_exist($details){
		$sql="SELECT id,email,mobile_number from users where email=:email or mobile_number=:email";
		return (new self)->select($sql,array('email'=>$details['email']));
	}
	public static function temp_password($details){
		$sql="UPDATE users set temp_password=:temp_password,temp_password_time=:temp_password_time where id=:id";
		return (new self)->update($sql,array('id'=>$details['id'],'temp_password'=>$details['temp_password'],'temp_password_time'=>$details['temp_password_time']));
	}
	public static function set_new_password($details){
		$sql="SELECT timestampdiff(hour,temp_password_time,now()) as temp_pass_expiry,temp_password from users where email=:email or mobile_number=:email";
		$var=(new self)->select($sql,array('email'=>$details['email']));
		if($var[0]->temp_pass_expiry>PASSWORD_EXPIRY){
			return $var;
		}
		$sql="UPDATE users set password=:password where email=:email or mobile_number=:email and temp_password=:temp_password";
		$var=(new self)->update($sql,array('password'=>$details['new_password'],'email'=>$details['email'],'temp_password'=>$details['temp_password']));
		return $var;
	}
	public static function get_temp_password($details){
		$sql="SELECT temp_password from users where email=:email or mobile_number=:email";
		return (new self)->select($sql,array('email'=>$details['email']));
	}
	public static function stu_details($id){
		$sql="SELECT * FROM `users` a
			  left join user_pendrives b on b.user_id=a.id
			  WHERE a.id=:id";
		return(new self)->select($sql,array('id'=>$id));
	}
	public static function tea_details($id){
		$sql="SELECT a.*,group_concat(distinct c.name) as subject,group_concat(distinct e.name) as class,group_concat(g.name) as student  FROM `users` a
			left join teacher_subjects b on b.teacher_id=a.id
			left join subjects c on c.id=b.subject_id
			left join teacher_classes d on d.teacher_id=a.id
			left join classes e on e.id=d.class_id
			left join user_teachers f on f.teacher_id=a.id
			left join users g on g.id=f.user_id
			where a.id=:id group by a.id";
		return(new self)->select($sql,array('id'=>$id));
	}

	public static function getUser($details){
		return (new self)->select("SELECT a.id from users a left join user_pendrives b on b.user_id=a.id where a.mobile=:mobile and b.pendrive_num=:pendrive_num",array('mobile'=>$details['mobile'],'pendrive_num'=>$details['pendrive_num']));
	}
	



}
