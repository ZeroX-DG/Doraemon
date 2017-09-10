<?php
class HomeController{
	public function Index(){
		if($_SESSION['Role'] == ADMIN_ROLE){
			View("AdminIndex");
		}
		else{
			View("EmployeeIndex");
		}
	}
}
?>