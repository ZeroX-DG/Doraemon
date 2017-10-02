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
	$router->get('/all', 'AttendanceController@listAll');
	$router->get('/listSchedules', 'AttendanceController@viewAttendanceSchedules');
	$router->get('/scheduleDetails/(\d+)/(\d+)', 'AttendanceController@viewScheduleDetails');
	$router->post('/scheduleDetails/(\d+)/(\d+)', 'AttendanceController@adminTakeAttendance');
});

$router->mount('/shifts', function() use ($router){
	$router->get('/all', 'ShiftController@Index');
	$router->get('/', 'ShiftController@Index');
	$router->post('/delete', 'ShiftController@deleteShift');
	$router->get('/edit/(\d+)', 'ShiftController@viewEditShift');
	$router->get('/(\d+)', 'ShiftController@find');
	$router->post('/edit/(\d+)', 'ShiftController@EditShift');
	$router->get('/add', 'ShiftController@viewAddShift');
	$router->post('/add', 'ShiftController@AddShift');
});

$router->mount('/schedules', function() use ($router){
	$router->get('/all', 'ScheduleController@all');
	$router->post('/delete', 'ScheduleController@deleteSchedule');
	$router->post('/edit', 'ScheduleController@editScheduleName');
	$router->get('/', 'ScheduleController@details');
	$router->post('/', 'ScheduleController@addSchedule');
	$router->get('/(\d+)', 'ScheduleController@details');
	$router->post('/(\d+)', 'ScheduleController@addSchedule');
	$router->post('/shift/delete', 'ShiftController@deleteShiftFromSchedule');
	$router->post('/shift/add', 'ShiftController@addShiftToSchedule');
	$router->get('/shift/find/(\d+)/(\d+)/(\d+)', 'ShiftController@find');
});

$router->mount('/account', function() use ($router){
	$router->get('/', 'AccountController@Index');
	$router->get('/add', 'AccountController@Add');
	$router->post('/add', 'AccountController@AddNewEmployee');
	$router->get('/edit/(\d+)', 'AccountController@Edit');
	$router->post('/edit/(\d+)', 'AccountController@EditAccountmanagement');
	$router->post('/delete', 'AccountController@DeleteAccount');
});

$router->mount('/storage', function() use ($router){
	$router->get('/', 'StorageController@Index');
	$router->get('/(\d+)', 'StorageController@ShowContent');
	$router->post('/(\d+)/delete', 'StorageController@deleteProduct');
	$router->get('/(\d+)/add', 'StorageController@viewAddProduct');
	$router->post('/(\d+)/add', 'StorageController@addProduct');
	$router->get('/(\d+)/edit/(\d+)', 'StorageController@viewEditProduct');
	$router->post('/(\d+)/edit/(\d+)', 'StorageController@EditProduct');
	$router->post('/delete', 'StorageController@deleteStorage');
	$router->get('/add', 'StorageController@viewAddStorage');
	$router->post('/add', 'StorageController@AddStorage');
	$router->get('/(\d+)/edit', 'StorageController@ViewEditStorage');
	$router->post('/(\d+)/edit', 'StorageController@EditStorage');
	$router->post('/(\d+)', 'StorageController@importAndExportProduct');
});

$router->get('/storageHistory', 'StorageController@history');

$router->mount("/salary", function() use ($router){
	$router->get("/", 'SalaryController@Index');
});

$router->before('GET', '/.*', function(){
	if(!isset($_SESSION['UserName']) &&
		!isset($_SESSION['Role']) &&
		strpos($_SERVER['REQUEST_URI'], "login") === false){
		redirect('/login');
	}
})
?>