<?php
use Model\Users;
class LoginController{
	public function Index(){
		return View("login");
	}

	public function doLogin(){
		$username = addslashes($_POST['userName']);
		$password = addslashes($_POST['password']);
		// find user
		$foundUser = Users::where('UserName','=',$username)
							->where('PassWord', '=', $password)
							->get();
		// if only 1 user found !
		if(count($foundUser) == 1){
			// correct 
			$userRole = $foundUser[0]->Role;
			// set session
			$_SESSION['UserName'] = $userName;
			$_SESSION['Role'] = $userRole;
			// redirect to index page
			redirect('/');
		}
		else{
			return View("login", ["error" => "Wrong username or password"]);
		}
	}
}