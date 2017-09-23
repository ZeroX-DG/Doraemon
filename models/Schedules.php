<?php
namespace Model;
use Illuminate\Database\Eloquent\Model as Eloquent;
class Schedules extends Eloquent {
	protected $primaryKey = 'Id';
	public function details(){
		return $this->hasMany('Model\Schedule_details', 'Schedule_id');
	}
	protected $fillable = ["Date_start", "Date_end", "Name"];
	public $timestamps = false;
}
?>