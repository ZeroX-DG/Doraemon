<?php
namespace Model;
use Illuminate\Database\Eloquent\Model as Eloquent;
class Permissions extends Eloquent {
  protected $table = "permissions";
  protected $fillable = ["department_id", "name"];
  public $timestamps = false;
}
?>