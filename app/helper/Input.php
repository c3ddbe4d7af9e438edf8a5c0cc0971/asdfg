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
		return htmlspecialchars((trim($var)));
	}
}
?>