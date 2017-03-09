<?php
require_once '../app/init.php';

$route=new Route;

$route->post('/signup','account@signup');

$route->post('/login','account@login');

$route->post('/user/edit','Detail@update_details');

$route->post('/password/change','Detail@change_password');

$route->post('/password/forget','account@send_temp_password');

$route->post('/password/reset','account@set_new_password');

$route->get('/details','Detail@user_details');
$route->get('/student','Detail@getUser');

$route->get('/test','Test@home');
$route->run();

?>
