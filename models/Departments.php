<?php
namespace Model;
use Illuminate\Database\Eloquent\Model as Eloquent;
class Departments extends Eloquent {
  protected $table = "departments";
  protected $fillable = ["name"];
  public $timestamps = false;
}
?>