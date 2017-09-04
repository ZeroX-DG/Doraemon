<?php
$router->get('/', function() {
	(new HomeController())->Index();
});
?>