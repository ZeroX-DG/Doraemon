<?php
$router->get('/', 'HomeController@Index');
$router->get('/login', 'LoginController@Index');
$router->post('/login', 'LoginController@doLogin');
$router->before('GET', '/.*', function(){
	if(!isset($_SESSION['UserName']) &&
		!isset($_SESSION['Role']) &&
		strpos($_SERVER['REQUEST_URI'], "login") === false){
		redirect('/login');
	}
})
?>