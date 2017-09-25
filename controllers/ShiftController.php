<?php
use Model\Schedules;
use Model\Users;
use Model\Shifts;
use Model\Schedule_details;

class ShiftController{
	public function all(){
		header('Content-type: application/json');
		$allShifts = Shifts::all();
		echo $allShifts->toJson();
	}

	public function find($scheduleId, $shiftId){
		$details = Schedule_details::where('Schedule_id', '=', $scheduleId)
								->where('ShiftId', '=', $shiftId)
								->get()
								->toArray();
		$schedule = Schedules::find($scheduleId);
		$shiftDetails = Shifts::find($shiftId);
		$data = [
			"shiftName" => $shiftDetails->Name,
			"ScheduleName" => $schedule->Name,
			"employees" => []
		];
		foreach($details as $detail){
			$userDetails = Users::find($detail["UserId"]);
			$date = null;
			if($detail["DayOfWeek"] == 8){
				$date = "Chủ nhật";
			}
			else{
				$date = "Thứ " . $detail["DayOfWeek"];
			}
			array_push($data["employees"], [
				"empName" => $userDetails->DisplayName,
				"dateOfWeek" => $date
			]);
		}
		return View("employeeInShift", $data);	
	}

	public function deleteShift(){
		$uid     = $_POST['uid'];
		$shiftId = $_POST['shiftId'];
		$date    = $_POST['date'];
		if($_SESSION['Role'] == ADMIN_ROLE){
			Schedule_details::where('Date', '=', $date)
							->where('ShiftId', '=', $shiftId)
							->where('UserId', '=', $uid)
							->delete();
			echo "success";
		}
		else{
			echo "nope";
		}
	}

	public function addShift(){
		$scheduleId = $_POST['scheduleId'];
		$dayOfWeek  = $_POST['dayOfWeek'];
		$shift      = $_POST['shift'];
		$employeeId = $_POST['employeeId'];

		$schedule = Schedules::find($scheduleId);
		if($_SESSION['Role'] == ADMIN_ROLE){
			$details = new Schedule_details;
			$details->Schedule_id = $scheduleId;
			$details->DayOfWeek = $dayOfWeek;
			$details->UserId = $employeeId;
			$details->ShiftId = $shift;
			$date = date('Y-m-d', strtotime($schedule->Date_start.' + ' . $dayOfWeek . ' days'));
			$details->Date = $date;
			$details->save();
			$shiftDetails = Shifts::find($shift)->toArray();
			$timeStartParts = date_parse($shiftDetails["Time_start"]);
			$timeEndParts = date_parse($shiftDetails["Time_end"]);
			$timeAt = "";
			if($timeStartParts["hour"] < 12){
				$timeAt = "morning";
			}
			else if($timeStartParts["hour"] < 17){
				$timeAt = "afternoon";
			}
			else{
				$timeAt = "night";
			}
			
			$html = "<div class='shift-panel ".$timeAt."'>";
			$html .= "<div class='name text-center'>";
			$html .= $shiftDetails["Name"];
			$html .= "<div class='time text-center'>";
			$html .= $shiftDetails["Time_start"] . " - " . $shiftDetails["Time_end"];
			$html .= "</div>";
			$html .= "<div class='text-center' style='font-size: 1.5em'>";
			$html .= "<i class='fa fa-trash' data-toggle='modal' data-target='#deleteModal' style='cursor: pointer' data-uid='".$employeeId."' data-date='". $date. "' data-shiftId='".$shift."'></i>";
			$html .= "</div>";
			echo $html;
		}		
	}
}
?>