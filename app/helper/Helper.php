<?php
class Helper
{
	
	function pre($arr){
		echo "<pre>";
		print_r($arr);
		echo "</pre>";
	}

	function curl_get_contents($url,$xml='0'){
  		$ch =	 curl_init();
			 	 curl_setopt($ch, CURLOPT_URL, $url); 
		 	 	 curl_setopt($ch, CURLOPT_HEADER, 0);
			   	 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    $output= curl_exec($ch); 
	             curl_close($ch);
	             if($xml){
	             	return json_decode(json_encode(simplexml_load_string($output)));
	             }
	   return $output;
	} 
	function file_get_contents_with_cdata($url,$xml='0'){ 
  		$output =file_get_contents($url);
  			             if($xml){
	             	return json_decode(json_encode(simplexml_load_string($output,null,LIBXML_NOCDATA)));
	             }
	   return $output;
	}
	function file_get_contents($url,$xml='0'){ 
  		$output =file_get_contents($url);
  			             if($xml){
	             	return json_decode(json_encode(simplexml_load_string($output,null,LIBXML_NOCDATA)));
	             }
	   return $output;
	}
	function placeholder($string,$count=0,$seperator=','){
			$resutl=array();
			for ($i=0; $i <$count ; $i++) { 
				$resutl[]=$string;
			}
			return implode($seperator, $resutl);
	}

	function push($push_data){

		$path=dirname(__FILE__)."/ck.pem";
		$passphrase = '12345';
		$ctx = stream_context_create();
		stream_context_set_option($ctx, 'ssl', 'local_cert', $path);
		stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
		$fp = stream_socket_client(
			'ssl://gateway.sandbox.push.apple.com:2195', $err,
			$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
		if (!$fp)
			exit("Failed to connect: $err $errstr" . PHP_EOL);
		//echo 'Connected to APNS' . PHP_EOL;
		$body['aps'] = array(
			'alert' => $push_data[0]->title,
			'sound' => 'default',
			'data'  => $push_data
			);
		$payload = json_encode($body);
		///loop start
		foreach ($push_data as $key => $value) {
			$deviceToken=$value->device_id;
			$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
			$result = fwrite($fp, $msg, strlen($msg));
			//if (!$result)
				//echo 'Message not delivered' . PHP_EOL;
			//else
				//echo 'Message successfully delivered' . PHP_EOL;
		}
		
		//loop end
		fclose($fp);
	}
	
	


}
?>