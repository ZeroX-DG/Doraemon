<?php

use Model\Shifts;
class ShiftController{
	public function all(){
		header('Content-type: application/json');
		$allShifts = Shifts::all();
		echo $allShifts->toJson();
	}
	public function find($id){
		header('Content-type: application/json');
		$id = addslashes($id);
		$foundShift = Shifts::find($id);
		if($foundShift != null)
			echo $foundShift->toJson();
		else
			echo json_encode(["Error" => "No shift found !"]);
	}
}
?>