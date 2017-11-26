<?php
use Model\Users;
use Model\user_permissions;
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
function get_client_ip() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}
function number_with_comma($number){
    return preg_replace("/\B(?=(\d{3})+(?!\d))/i", ",", $number);
}
function havePermission($permissionId){
  $permission = user_permissions::where('userId', '=', $_SESSION['Uid'])
                                ->where('permissionId', '=', $permissionId)
                                ->get()
                                ->toArray();
  return count($permission) > 0;
}
?>