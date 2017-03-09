<?php
ini_set('display_errors', 1);
require_once '../app/init.php';
$route=new Route;

$route->post('/account/register','account@register');
$route->post('/account/login','account@login');
$route->post('/account/logout','account@logout');
$route->post('/account/forgetPassword','account@forgetPassword');
$route->post('/account/resetPassword','account@resetPassword');
$route->post('/account/changePassword','account@changePassword');

$route->any('/test','test@index');

$route->run();
?>
