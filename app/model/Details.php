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
	public static function user_details($details){
		$sql="SELECT a.*,if(group_concat(distinct c.code) is not null, group_concat(distinct c.code), '')  as class, if(group_concat(distinct pendrive_num) is not null, group_concat(distinct pendrive_num), '')  as pendrive_num FROM `users` a 
			  left join user_classes b on b.user_id=a.id
			  left join classes c on c.id=b.class_id
			  left join user_pendrives d on d.user_id=a.id
			  left join user_subjects e on e.user_id=a.id
			  left join subjects f on f.id=e.subject_id WHERE  a.id=:id and a.type=:type group by a.id";
		return(new self)->select($sql,array('id'=>$details['id'],'type'=>$details['type']));
	}

	public static function getTeacher($details){
		$param=array();
		$sql="SELECT * FROM `users` a 
		left join user_classes b on b.user_id=a.id
		left join classes c on c.id=b.class_id
		left join user_subjects d on d.user_id=a.id
		left join subjects e on e.id=d.subject_id WHERE a.type=2 and a.is_online=1 and c.code=:class and e.code=:subject ";
		return (new self)->select($sql,array('class'=>$details['class'],'subject'=>$details['subject']));
	}
	public static function changeTeacherStatus($details){
		return (new self)->update("UPDATE users set is_online=:is_online where id=:id",array('id'=>$details['id'],'is_online'=>$details['is_online']));
	}
	



}
