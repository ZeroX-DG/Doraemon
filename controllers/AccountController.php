<?php
use Model\Users;
class AccountController{
	public function Index(){
		$employees = Users::where("Role", "=", EMPLOYEE_ROLE)->get();
		return View("AccountManagement", ["isAdmin" => true, "employee" => $employees]);
	}

	public function Add(){
		return View("AddNewAccount", ["isAdmin" => true]);
	}
	public function Edit($id){
		$employee = Users::find($id);
		return View("EditAccount", ["isAdmin" => true, "employee" => $employee]);
	}
	
	public function AddNewEmployee(){
		$name = ($_POST['name']);
		$username = ($_POST['username']);
		$pass = ($_POST['pass']);
		$repass= ($_POST['repass']);

		if(empty($name)){
			return View("AddNewAccount",  ["error" => "Tên không được để trống", "hasError" => true, "isAdmin" => true]);
		}
		if(empty($username)){
			return View("AddNewAccount",  ["error" => "Tên hiển thị không được để trống ", "hasError" => true, "isAdmin" => true]);
		}
		if(empty($pass)){
			return View("AddNewAccount",  ["error" => "Mật khẩu không được để trống", "hasError" => true, "isAdmin" => true]);
		}
		if(empty($repass)){
			return View("AddNewAccount",  ["error" => "Mật khẩu không được để trống", "hasError" => true, "isAdmin" => true]);
		}
		if($repass != $pass ){
			return View("AddNewAccount",  ["error" => "Mật khẩu nhập lại không đúng", "hasError" => true, "isAdmin" => true]);
		}
		$foundUser = Users::where("UserName", "=", $name)->get();
		if(count($foundUser) != 0){
			return View("AddNewAccount",  ["error" => "Tên đăng nhập đã tồn tại ", "hasError" => true, "isAdmin" => true]);
		}
		$user = new Users;
		$user->UserName = $name;
		$user->PassWord = md5($pass);
		$user->DisplayName = $username;
		$user->Role = 2;
		$user->save();
		return View("AddNewAccount", ["hasMes" => true, "mess"=>"Thêm thành công"]);
	}
	public function EditAccountmanagement($id){
		$id = (int)$id;
		$username = $_POST['UserName'];
		$DisplayName = $_POST['DisplayName'];
		$employee = Users::find($id);
		$employee->UserName = $username;
		$employee->DisplayName = $DisplayName;
		$employee->save();
		return View("EditAccount", ["isAdmin" => true, "employee" => $employee,"hasMes" => true, "mess"=>"Chỉnh sửa thành công "]);
	}
	public function DeleteAccount($id){
		$id = (int)$id;
		$username = $_POST['UserName'];
		$DisplayName = $_POST['DisplayName'];
		$employee = Users::find($id);
		if ($employee != null) {
			$employee->delete();
		}
		else{
			redirect("/account");
		}
	}
}