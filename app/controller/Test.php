<?php
/**
* 
*/
class Test
{
	function home(){
		View::make('home');
	}
	function version(){
		phpinfo();
	}
	
	function header(){
		$hash=hash_hmac('sha256', '611c9f811b80684c4173e4fc52958b86db5c64431234eb61ffce723d858fde96d014d6ca32cff8aaba0', '4eb61ffce723d858fde96d014d6ca32cff8aaba0');
		$headers=array(
			'Authorization: '.$hash
			);
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL, 'http://127.0.0.1/user/login');
		curl_setopt($ch,CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POSTFIELDS, 'public_key=611c9f811b80684c4173e4fc52958b86db5c6443&time=123');
		$output=curl_exec($ch);
		var_dump($output);
	}
}
?>



