<?php
namespace Model;
use Illuminate\Database\Eloquent\Model as Eloquent;
class Schedule_details extends Eloquent {
	protected $fillable = ["Schedule_id", "DayOfWeek", "UserId", "ShiftId", "Date"];
	public $timestamps = false;
}
?>