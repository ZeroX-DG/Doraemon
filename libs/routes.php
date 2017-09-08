<?php
$router->get('/', 'HomeController@Index');

$router->mount('/login', function() use ($router){
	$router->get('/', 'LoginController@Index');
	$router->post('/', 'LoginController@doLogin');
});

$router->get('/logout', function(){
	session_destroy();
	redirect('/login');
});

$router->post('/checkWork', 'AttendanceController@doCheck');

$router->mount('/shifts', function() use ($router){
	$router->get('/all', 'ShiftController@all');
	$router->get('/', 'ShiftController@all');
	$router->get('/(\d+)', 'ShiftController@find');
});

$router->mount('/schedules', function() use ($router){
	$router->get('/all', 'ScheduleController@all');
	$router->get('/', 'ScheduleController@all');
	$router->get('/(\d+)/(\d+)', 'ScheduleController@findByUserId');
	$router->get('/(\d+)', 'ScheduleController@details');
});

$router->before('GET', '/.*', function(){
	if(!isset($_SESSION['UserName']) &&
		!isset($_SESSION['Role']) &&
		strpos($_SERVER['REQUEST_URI'], "login") === false){
		redirect('/login');
	}
})
?>