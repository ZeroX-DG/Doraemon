<?php
function assets($path){
	return VIEW_FOLDER.$path;
}

function View($viewName, $data){
	echo $GLOBALS['blade']->make($viewName, $data);
}
?>