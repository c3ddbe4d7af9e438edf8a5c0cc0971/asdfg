<?php
/**
* 
*/
class Validator
{
	protected static $error=array();
	protected static $unique=null;
	public static function validate(Array $input, Array $rules){
		self::$error=false;
		foreach ($rules as $key => $value) {
			$rule=explode('|', $value);
			foreach ($rule as $rule) {
				if (strpos($rule, ':')) {
					$rule=explode(':', $rule);
					$value=isset($input[$key])?$input[$key]:'';
					if($value || $rule[0]=='required')
					self::$rule[0]($value,$rule[1],$key);
				}else{
					$value=isset($input[$key])?$input[$key]:'';
					if($value || $rule=='required')
					self::$rule($value,$key);
				}
			}
		}
		return !self::$error;
	}
	public static function error(){
		return self::$error;
	}
	public static function uniqueError(){
		return self::$unique;
	}
	private static function required($value,$key){
		if (!isset($value) || $value=='') {
			self::setError($key, $key.' is required');
			return false;
		}
		return true;
	}
	private static function min($value,$param,$key){
		if($value>=(int)$param){
			return true;
		}
		self::setError($key, $key.' must be at least '.$param);
		return false;
	}
	private static function max($value,$param,$key){
		if($value<=(int)$param){
			return true;
		}
		self::setError($key, $key.' must be less than '.$param);
		return false;
	}
	private static function email($value,$key){
		if(filter_var($value, FILTER_VALIDATE_EMAIL)){
			return true;
		}
		self::setError($key, $key.' must be valid email');
		return false;
	}
	private static function minl($value,$param,$key){
		if(strlen($value)>=$param){
			return true;
		}
		self::setError($key, $key.' shold be at least '.$param.' charecter long');
		return false;
	}
	private static function maxl($value,$param,$key){
		if(strlen($value)<=$param){
			return true;
		}
		self::setError($key, $key.' should not be more than '.$param.' charecter long');
		return false;
	}
	private static function unique($value,$param,$key){
		$db=DB::getInstance();
		$sth=$db->prepare("SELECT * FROM $param WHERE $key=:value");
		$sth->execute(array('value'=>$value));
		if (!$sth->rowCount()) {
			return true;
		}
		self::$unique=$key=='email'?100:101;
		self::setError($key, $key.' should be unique',$key);
		return false;
	}
	private static function date($value,$key){
		if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$value)){
        	return true;
    	}
    	self::setError($key, $key.' Must be in YYYY-MM-DD format');
		return false;	
	}
	private static function mind($value,$param,$key){
		if (strtotime($value)>=strtotime($param)){
        	return true;
    	}
    	self::setError($key, $key.' should  be at least '.$param);
		return false;	
	}
	private static function maxd($value,$param,$key){
		if (strtotime($value)>=strtotime($param)){
        	return true;
    	}
    	self::setError($key, $key.' should not be more than '.$param);
		return false;	
	}
	private static function setError($key,$value){
		self::$error[$key][]=$value;
	}
	private static function rangel($value,$param,$key){
		$param=explode('-', $param);
		self::minl($value,$param[0],$key);
		self::maxl($value,$param[1],$key);
	}
	private static function range($value,$param,$key){
		$param=explode('-', $param);
		self::min($value,$param[0],$key);
		self::max($value,$param[1],$key);
	}
	private static function mobile($value,$key){
		if(preg_match('/^\d{10}$/', $value)){
			return true;
		}
		self::setError($key,$key.' Should be a valid mobile number');
		return false;
	}
	public static function numeric($value,$key){
		if (is_numeric($value)){
        	return true;
    	}
    	self::setError($key, $key.' Must be numeric');
		return false;	
	}
}
?>