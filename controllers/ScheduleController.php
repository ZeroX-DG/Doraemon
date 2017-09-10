<?php
use Model\Schedules;
use Model\Users;
use Model\Shifts;
class ScheduleController{
	public function all(){
		header('Content-type: application/json');
		$allSchedule = Schedules::all();
		echo $allSchedule->toJson();
	}

	public function getEmployeeSchedule($scheduleId, $userId){
		$allSchedule = Schedules::find($scheduleId)
								->details()
								->where('userId', '=', $userId)
								->get();
		return $allSchedule->toArray();
	}

	public function details($scheduleId = null){
		// data to render
		$data = [];
		// schedule info
		$scheduleInfo = null;
		if($scheduleId == null){
			// if there's no schedule id 
			// then get the latest one !
			$scheduleInfo = Schedules::orderBy('id', 'desc')->first();
		}
		else{
			$scheduleInfo = Schedules::find($scheduleId);
		}	
		
		if($scheduleInfo == null){
			echo "Schedule not found !";
			return;
		}
		$scheduleInfo = $scheduleInfo->toArray();
		// get employee list
		$employees = Users::where('Role', '>', ADMIN_ROLE)->get();
		// call getEmployeeSchedule with employee id
		foreach($employees as $employee){
			$schedules = $this->getEmployeeSchedule($scheduleInfo["Id"], $employee->Id);
			// get shift details
			$shifts_details = []; 
			for($i = 2; $i < 9; $i++){
				$shifts_details["t" . $i] = [];
			}
			foreach($schedules as $schedule){
				// key for array
				$dayOfWeek = "t".$schedule["DayOfWeek"];
				
				$shift = Shifts::find($schedule["ShiftId"])->toArray();
				$timeStartParts = date_parse($shift["Time_start"]);
				$timeEndParts = date_parse($shift["Time_end"]);
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
				$shift["Time_start"] = $timeStartParts["hour"] . 
										":" . 
										$timeStartParts["minute"];
				$shift["Time_end"] = $timeEndParts["hour"] . 
										":" . 
										$timeEndParts["minute"];
				$shift["timeAt"] = $timeAt;
				array_push($shifts_details[$dayOfWeek], $shift);
				// sort shifts
				usort($shifts_details[$dayOfWeek], function ( $a, $b ) {
				    return strtotime($a["Time_start"]) - strtotime($b["Time_start"]);
				});
			}

			array_push($data, [
				"DisplayName" => $employee->DisplayName, 
				"Shifts" => $shifts_details,
				]);
		}
		// Other schedule
		$otherSchedule = Schedules::all()->toArray();
		// is admin
		$isAdmin = $_SESSION["Role"] == ADMIN_ROLE;
		// render
		View("ScheduleDetails", [
			"schedules" => $data, 
			"scheduleInfo" => $scheduleInfo,
			"otherSchedule" => $otherSchedule,
			"isAdmin" => $isAdmin
			]);
	}
}
?>