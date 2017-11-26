<?php
namespace Model;
use Illuminate\Database\Eloquent\Model as Eloquent;
class user_permissions extends Eloquent {
  protected $fillable = ["userId", "permissionId"];
  public $timestamps = false;
}
?>