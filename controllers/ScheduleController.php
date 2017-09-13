<?php
use Model\Schedules;
use Model\Users;
use Model\Shifts;
use Model\Schedule_details;
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
				$shift["ShiftId"] = $schedule["ShiftId"];
				$shift["Date"] = $schedule["Date"];
				array_push($shifts_details[$dayOfWeek], $shift);
				// sort shifts
				usort($shifts_details[$dayOfWeek], function ( $a, $b ) {
				    return strtotime($a["Time_start"]) - strtotime($b["Time_start"]);
				});
			}

			array_push($data, [
				"Uid" => $employee->Id,
				"DisplayName" => $employee->DisplayName, 
				"Shifts" => $shifts_details,
				]);
		}
		// Other schedule
		$otherSchedule = Schedules::all()->toArray();
		// is admin
		$isAdmin = $_SESSION["Role"] == ADMIN_ROLE;
		// all shift
		$allShifts = Shifts::all();
		// render
		View("ScheduleDetails", [
			"schedules" => $data, 
			"scheduleInfo" => $scheduleInfo,
			"otherSchedule" => $otherSchedule,
			"isAdmin" => $isAdmin,
			"employees" => $employees,
			"allShifts" => $allShifts,
			]);
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