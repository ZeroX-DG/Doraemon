<?php
namespace Model;
use Illuminate\Database\Eloquent\Model as Eloquent;
class salary_history extends Eloquent {
  protected $table = "salary_history";
  protected $fillable = ["Name", "createAt"];
  public $timestamps = false;
}
?>