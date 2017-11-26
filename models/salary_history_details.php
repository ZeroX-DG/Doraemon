<?php
namespace Model;
use Illuminate\Database\Eloquent\Model as Eloquent;
class salary_history_details extends Eloquent {
  protected $table = "salary_history_details";
  protected $fillable = [
    "userId", 
    "historyId", 
    "work_hour", 
    "salary_by_hour",
    "bonus",
    "cash_advance",
    "total_salary"
  ];
  public $timestamps = false;
}
?>