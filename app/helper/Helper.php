<?php
class Helper
{
	
	function pre($arr){
		echo "<pre>";
		print_r($arr);
		echo "</pre>";
	}

	public static function isAjax(){
		$header=apache_request_headers();
		if (@$header['X-Requested-With']=='XMLHttpRequest') {
			return true;
		}
		return false;
	}
	
	


}
?>