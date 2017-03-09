<?php 
/**
* 
*/
class Before
{
	private static function parseUri($uri){
		if(!empty($uri)){
			$url=parse_url($uri)['path'];
			$url=urldecode($url);
			$url=rtrim(trim($url),'/');
			$url=strtolower($url);
			return $url?$url:'/';
		}
		return '/';
	}

	public static function logRequest(){
		$uri=self::parseUri($_SERVER['REQUEST_URI']);
		$token=Input::any('access_token')?:Input::any('request_token');
		$method=$_SERVER['REQUEST_METHOD'];
		$time=time();
		$model=new Model;
		$model->insert([
			'uri'=>$uri,
			'token'=>$token,
			'method'=>$method,
			'created_at'=>$time,
			'ip'=>Helper::getClientIp(),
			'ua'=>$_SERVER ['HTTP_USER_AGENT'],
		],'request_log');
	}

	public static function getApiLimit(){
		$pass=['POST /auth/request-token','GET /test','POST /logout'];
		$uri=self::parseUri($_SERVER['REQUEST_URI']);
		$method=$_SERVER['REQUEST_METHOD'];
		if (in_array($method.' '.$uri, $pass)) {
			return true;
		}
		$token=Input::any('access_token')?:Input::any('request_token');
		$time=time();
		$model=new Model;
		$rt= $model->first("SELECT * FROM request_token a LEFT JOIN auth b ON a.auth_id=b.id WHERE token=?",[$token]);
		if (!$rt) {
			$rt=$model->first("SELECT * FROM access_token a LEFT JOIN request_token b ON a.request_token_id=b.id LEFT JOIN auth c ON b.auth_id=c.id WHERE access_token=?",[$token]);
		}
	    $limit=$rt->rate;
		if ($method!='GET') {
			$limit=(int)($limit/4);
		}
		$slot=60;
	    $window=$time%$slot;
		$start=$time-$window;
		$count=$model->first("SELECT count(*) AS count FROM request_log WHERE created_at>? AND token=? AND method=?",[$start,$token,$method])->count;
		return [
			'X-RateLimit-Cost'=>1,
			'X-RateLimit-Limit'=>$limit,
			'X-RateLimit-Remaining'=>$limit-$count-1>0?$limit-$count-1:0,
			'X-RateLimit-Reset'=>$time+($slot-$window),
			'X-RateLimit-Reset-Ttl'=>$slot-$window
		];
	}
	public function makeLimit(){
		$limit=self::getApiLimit();
		foreach ($limit as $key => $value) {
		header("$key: $value");
		}
		if (isset($limit['X-RateLimit-Remaining']) && $limit['X-RateLimit-Remaining']<=0) {
		Json::make('0','To many request')->withError(429)->response();
		die;
		}
		self::logRequest();
	}
	public static function uriLimit($limit,$slot){
		$token=Input::any('access_token');
		$user=Auth::user();
		$time=time();
		$window=$time%$slot;
		$start=$time-$window;
		$uri=self::parseUri($_SERVER['REQUEST_URI']);
		$method=$_SERVER['REQUEST_METHOD'];
		switch ($user->user_type) {
			case '1':
				$user_id='artist_id';
				break;
			case '2':
				$user_id='business_id';
				break;
			case '3':
				$user_id='user_id';
				break;
			default:
				return false;
				break;
		}
		$model=new Model;
		$count=$model->first("SELECT count(*) AS count FROM `request_log` WHERE created_at>? AND uri=? AND method=? AND token in (SELECT access_token FROM access_token WHERE $user_id=?)",[$start,$uri,$method,$user->id])->count;
		if (!$count) {
			return false;
		}
		return [
			'X-RateLimit-Cost'=>1,
			'X-RateLimit-Limit'=>$limit,
			'X-RateLimit-Remaining'=>$limit-$count>0?$limit-$count:0,
			'X-RateLimit-Reset'=>$time+($slot-$window),
			'X-RateLimit-Reset-Ttl'=>$slot-$window
		];
	}
	public static function makeUriLimit($limit,$slot=86400){
		$limit=self::uriLimit($limit,$slot);
		foreach ($limit as $key => $value) {
		header("$key: $value");
		}
		if (isset($limit['X-RateLimit-Remaining']) && $limit['X-RateLimit-Remaining']<=0) {
		Json::make('0','To many request')->withError(429)->response();
		die;
		}
	}
}
?>