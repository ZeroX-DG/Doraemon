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

$router->mount('/attendance', function() use ($router){
	$router->get('/', 'AttendanceController@Index');
	$router->post('/', 'AttendanceController@takeAttendance');
	$router->get('/list', 'AttendanceController@showList');
});

$router->mount('/shifts', function() use ($router){
	$router->get('/all', 'ShiftController@all');
	$router->get('/', 'ShiftController@all');
	$router->get('/(\d+)', 'ShiftController@find');
});

$router->mount('/schedules', function() use ($router){
	$router->get('/all', 'ScheduleController@all');
	$router->post('/delete', 'ScheduleController@deleteSchedule');
	$router->get('/', 'ScheduleController@details');
	$router->post('/', 'ScheduleController@addSchedule');
	$router->get('/(\d+)', 'ScheduleController@details');
	$router->post('/(\d+)', 'ScheduleController@addSchedule');
	$router->post('/shift/delete', 'ScheduleController@deleteShift');
	$router->post('/shift/add', 'ScheduleController@addShift');
});

$router->mount('/account', function() use ($router){
	$router->get('/', 'AccountController@Index');
	$router->get('/add', 'AccountController@Add');
	$router->post('/add', 'AccountController@AddNewEmployee');
	$router->get('/edit/(\d+)', 'AccountController@Edit');
	$router->post('/edit/(\d+)', 'AccountController@EditAccountmanagement');
});

$router->before('GET', '/.*', function(){
	if(!isset($_SESSION['UserName']) &&
		!isset($_SESSION['Role']) &&
		strpos($_SERVER['REQUEST_URI'], "login") === false){
		redirect('/login');
	}
})
?>