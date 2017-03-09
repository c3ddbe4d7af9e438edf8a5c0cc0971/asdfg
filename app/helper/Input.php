<?php
/**
*			 
*/
class Input 		
{
	public static function get($key=null){
		if(is_array($key)){
			$get=array();
			foreach ($key as $key) {
				$get[$key]=self::clean(self::get($key));
			}
			return $get;
		}else{
			if(!isset($key)){
				$get=array();
				foreach ($_GET as $key => $value) {
					$get[$key]=self::clean($value);
				}
				return $get;
			}
		}
		return isset($_GET[$key]) ?self::clean($_GET[$key]):'';
	}

	public static function post($key=null){
		if(is_array($key)){
			$post=array();
			foreach ($key as $key) {
				$post[$key]=self::clean(self::post($key));
			}
			return $post;
		}else{
			if(!isset($key)){
				$post=array();
				foreach ($_POST as $key => $value) {
					$post[$key]=self::clean($value);
				}
				return $post;
			}
		}
		return isset($_POST[$key]) ?self::clean($_POST[$key]):'';
	}
	public static function put($key=null){
		parse_str(file_get_contents("php://input"),$_PUT);
		if(is_array($key)){
			$put=array();
			foreach ($key as $key) {
				$put[$key]=self::clean(self::put($key));
			}
			return $put;
		}else{
			if(!isset($key)){
				$put=array();
				foreach ($_PUT as $key => $value) {
					$put[$key]=self::clean($value);
				}
				return $put;
			}
		}
		return isset($_PUT[$key]) ?self::clean($_PUT[$key]):'';
	}
	public static function any($key=null){
		if(is_array($key)){
			$any=array();
			foreach ($key as $key) {
				$any[$key]=self::clean(self::any($key));
			}
			return $any;
		}else{
			if(!isset($key)){
				$any=array();
				foreach ($_REQUEST as $key => $value) {
					$any[$key]=self::clean($value);
				}
				return $any;
			}
		}
		return isset($_REQUEST[$key]) ?self::clean($_REQUEST[$key]):'';
	}

	private static function clean($var=''){
		if (isset($var)) {
			if (is_array($var)) {
				foreach ($var as $key => $value) {
					$var[$key]=trim($value);
				}
			}else{
				$var=trim($var);		
			}
		}else{
			$var='';
		}
		return $var;
	}
}
?>