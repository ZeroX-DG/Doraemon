<?php
$router->get('/', function() {
	(new HomeController())->Index();
});
$router->get('/chao/([A-Z]+)', function($so) {
	(new HomeController())->chao($so);
});
?>