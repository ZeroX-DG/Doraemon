<?php
use Model\Users;
class HomeController{
	public function Index(){
		$users = Users::all();
		foreach ($users as $user) {
			print_r($user->UserName);
		}
	}
}
?>