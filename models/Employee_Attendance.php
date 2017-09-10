<?php
namespace Model;
use Illuminate\Database\Eloquent\Model as Eloquent;
class Employee_Attendance extends Eloquent {
	protected $table = "employee_attendances";
	protected $fillable = ["UserId", "Date", "Time", "ShiftId"];
	public $timestamps = false;
}
?>