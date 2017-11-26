<?php
use Model\Users;
use Model\Permissions;
use Model\Departments;
use Model\user_permissions;
class PermissionsController{
  public function Index(){
    if(havePermission(7)){
      $users = Users::all();
      $departments = Departments::all();
      $permissions = [];

      foreach($departments as $department){
        $permission_in_department = 
          Permissions::where('department_id', '=', $department->Id)
                      ->get();
        array_push($permissions,[
          "department" => $department->name,
          "permissions" => $permission_in_department
        ]);
      }
      return View("PermissionManagement", [
        "users" => $users,
        "permissionsList" => $permissions,
        "isAdmin" => true
      ]);
    }
    else{
      echo "bạn không có quyền vào trang này";
    }
  }

  public function getPermissionByUserId($id){
    header('Content-Type: application/json');
    $permissions = user_permissions::where('userId', '=', $id)->get();
    $ids = [];
    foreach($permissions as $permission){
      array_push($ids, $permission->permissionId);
    }
    print_r(json_encode($ids));
  }

  public function addPermission($userId, $permissionId){
    if(havePermission(7)){
      $permission = new user_permissions;
      $permission->userId = $userId;
      $permission->permissionId = $permissionId;
      $permission->save();
    }
    else{
      echo "bạn không có quyền vào trang này";
    }
  }
  public function removePermission($userId, $permissionId){
    if(havePermission(7)){
      $permission = user_permissions::where("userId", "=", $userId)
                                    ->where("permissionId", "=", $permissionId)
                                    ->delete();
    }
    else{
      echo "bạn không có quyền vào trang này";
    }
  }
}