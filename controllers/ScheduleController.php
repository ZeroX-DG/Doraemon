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

	public function details($scheduleId){
		// data to render
		$data = [];
		// schedule info
		$scheduleInfo = Schedules::find($scheduleId)->toArray();
		// get employee list
		$employees = Users::where('Role', '>', ADMIN_ROLE)->get();
		// call getEmployeeSchedule with employee id
		foreach($employees as $employee){
			$schedules = $this->getEmployeeSchedule($scheduleId, $employee->Id);
			// get shift details
			$shifts_details = []; 
			for($i = 2; $i < 9; $i++){
				$shifts_details["t" . $i] = [];
			}
			foreach($schedules as $schedule){
				// key for array
				// 8 is sunday
				$dayOfWeek = "t".$schedule["DayOfWeek"];
				
				$shift = Shifts::find($schedule["ShiftId"])->toArray();
				$dateParts = date_parse($shift["Time_start"]);
				$timeAt = "";
				if($dateParts["hour"] < 12){
					$timeAt = "morning";
				}
				else if($dateParts["hour"] < 17){
					$timeAt = "afternoon";
				}
				else{
					$timeAt = "night";
				}
				$shift["timeAt"] = $timeAt;
				array_push($shifts_details[$dayOfWeek], $shift);
			}
			array_push($data, [
				"DisplayName" => $employee->DisplayName, 
				"Shifts" => $shifts_details,
				]);
		}
		// print_r($data);
		// render
		View("ScheduleDetails", ["schedules" => $data, "scheduleInfo" => $scheduleInfo]);
	}
}
?>