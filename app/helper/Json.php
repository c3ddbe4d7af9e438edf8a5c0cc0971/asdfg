<?php
/**
* 
*/
class Json
{
	public $data;
	protected static $status=array(
				'200'=>'OK', 
				'201'=>'Created',	
				'202'=>'Accepted',	
				'203'=>'Non-Authoritative Information',	
				'204'=>'No Content', 
				'205'=>'Reset Content',	
				'206'=>'Partial Content', 
				'300'=>'Multiple Choices', 
				'301'=>'Moved Permanently', 
				'302'=>'Found',	
				'303'=>'See Other', 
				'304'=>'Not Modified', 
				'306'=>'Switch Proxy', 
				'307'=>'Temporary Redirect', 
				'308'=>'Resume Incomplete',	
				'400'=>'Bad Request',	
				'401'=>'Unauthorized',	
				'402'=>'Payment Required', 
				'403'=>'Forbidden',	
				'404'=>'Not Found', 
				'405'=>'Method Not Allowed', 
				'406'=>'Not Acceptable',	
				'407'=>'Proxy Authentication Required',	
				'408'=>'Request Timeout',	
				'409'=>'Conflict',	
				'410'=>'Gone',	
				'411'=>'Length Required',	
				'412'=>'Precondition Failed',	
				'413'=>'Request Entity Too Large',	
				'414'=>'Request-URI Too Long',	
				'415'=>'Unsupported Media Type',	
				'416'=>'Requested Range Not Satisfiable',	
				'417'=>'Expectation Failed',	
				'500'=>'Internal Server Error',	
				'501'=>'Not Implemented',	
				'502'=>'Bad Gateway',	
				'503'=>'Service Unavailable',	
				'504'=>'Gateway Timeout',	
				'505'=>'HTTP Version Not Supported',	
				'511'=>'Network Authentication Required'
		);
	protected static $error=0;
	private function __construct($data) {
		$this->data=$data;
	}
	function response($status='200'){
		header('HTTP/1.1 '.$status.' '.self::$status[$status]);
		header('Content-Type: Application/Json; Charset=UTF-8');
		header('X-Powered-By: Mayank Kumar');
		//$this->data['count']=sizeof($this->data['data']);
		echo json_encode($this->data,JSON_PRETTY_PRINT);
	}

	function make($success,$message,$data=array()){
		$d['success']=$success;
		$d['message']=$message;
		$d['data']=$data;
		$d['error']=self::$error;
		return new self($d);
	}
	function withError($error){
		$this->data['error']=$error;
		return $this;
	}
}
?>