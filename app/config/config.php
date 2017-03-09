<?php
date_default_timezone_set('Asia/Kolkata');
$server_ip=$_SERVER['SERVER_ADDR'];
switch ($server_ip) {
	case '172.31.23.161':
		define('env', 'prod');
		break;
	case '127.0.0.1':
		define('env', 'local');
		break;
	default:
		define('env', 'local');
		break;
}

$config=[
	'prod'=>[
		'DB_NAME'	       =>'db',
		'DB_USER'	       =>'root',
		'DB_PASSWORD'	   =>'',
		'DB_HOST'	       =>'yahavi.cn26zz0crrtl.ap-south-1.rds.amazonaws.com',
		'DRIVER'	       =>'mysql',
		'API_URL'	       =>'https://api.knowlarity.com',
	],
	'local'=>[
		'DB_NAME'	       =>'db',
		'DB_USER'	       =>'root',
		'DB_PASSWORD'	   =>'',
		'DB_HOST'	       =>'127.0.0.1',
		'DRIVER'	       =>'mysql',
		
		'API_URL'	       =>'https://api.local.knowlarity.com'
	],
];

if (!defined('env')) {
	define('env', 'prod');
}
foreach ($config[env] as $key => $value) {
	define($key, $value);
}
?>