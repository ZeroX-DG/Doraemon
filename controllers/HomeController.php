<?php
class HomeController{
	public function Index(){
		if($_SESSION['Role'] == ADMIN_ROLE){
			View("AdminIndex",["isAdmin"=>true]);
		}
		else{
			View("EmployeeIndex");
		}
	}
}
?>