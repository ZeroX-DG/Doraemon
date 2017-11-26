<?php
use Model\Users;
use Model\user_permissions;
class AccountController{
	public function Index(){
		$employees = Users::where("Role", "=", EMPLOYEE_ROLE)->get();
		return View("AccountManagement", ["isAdmin" => true, "employee" => $employees]);
	}

	public function Add(){
    if(havePermission(1)){
      return View("AddNewAccount", ["isAdmin" => true]);
    }
    else{
      echo "bạn không có quyền vào trang này";
    }
	}

	public function Edit($id){
    if(havePermission(2)){
  		$employee = Users::find($id);
  		return View("EditAccount", ["isAdmin" => true, "employee" => $employee]);
    }
    else{
      echo "bạn không có quyền vào trang này";
    }
	}
	
	public function AddNewEmployee(){
    if(havePermission(1)){
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
  		return View("AddNewAccount", ["isAdmin"=> true,"hasMes" => true, "mess"=>"Thêm thành công"]);
    }
    else{
      echo "bạn không có quyền vào trang này";
    }
	}
	public function EditAccountmanagement($id){
    if(havePermission(2)){
  		$username = $_POST['UserName'];
  		$DisplayName = $_POST['DisplayName'];
  		$cemployee = Users::find($id);
  		$cemployee->UserName = $username;
  		$cemployee->DisplayName = $DisplayName;
  		$cemployee->save();
  		redirect("/account");
    }
    else{
      echo "bạn không có quyền vào trang này";
    }
	}
	public function DeleteAccount(){
    if(havePermission(3)){
  		$id = (int)$_POST['Id'];
  		if($_SESSION['Role'] == ADMIN_ROLE){
  			$isDelete = Users::where('Id', '=', $id)->delete();
  			if($isDelete){
  				echo "done";
  			}
  			else{
  				echo $isDelete;
  			}
  		}
    }
    else{
      echo "bạn không có quyền vào trang này";
    }
	}
}