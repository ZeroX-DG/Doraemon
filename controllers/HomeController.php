<?php
use Model\Users;
class HomeController{
	public function Index(){
		$users = Users::all();
		$hello = "dit me";
		View("index", [
			"ditme" => $hello,
			"hello" => "TUng Tom"
			]);
	}
	public function chao($so){
		echo "Hello " . $so;
	}
}
?>