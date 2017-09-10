<?php
namespace Model;
use Illuminate\Database\Eloquent\Model as Eloquent;
class Schedule_details extends Eloquent {
	public function users(){
		return $this->belongTo('Model\Users', 'UserId');
	}
}
?>