<?php
function assets($path){
	return VIEW_FOLDER.$path;
}

function View($viewName, $data = []){
	$tpl = $GLOBALS['m']->loadTemplate($viewName);
	echo $tpl->render($data);
}

function redirect($url, $statusCode = 303){
   header('Location: ' . MAIN_PATH . $url, true, $statusCode);
   die();
}


?>