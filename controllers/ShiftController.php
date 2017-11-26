<?php
use Model\Schedules;
use Model\Users;
use Model\Shifts;
use Model\Schedule_details;

class ShiftController{
	public function Index(){
		$shifts = Shifts::all();
		return View("shiftManagement", ["isAdmin" => true,"shifts" => $shifts]);
	}

	public function deleteShift(){
		header("application/json");
		$id = $_POST['id'];
		if($_SESSION['Role'] == ADMIN_ROLE){
			$s = Shifts::find($id)->delete();
			if($s)
				echo trim("done");
			else
				echo "bug";
		}
	}

	public function viewEditShift($id){
		$shift = Shifts::find($id);
		return View("editShift", ["shift" => $shift]);
	}

	public function EditShift($id){
		$name = $_POST["name"];
		$start = date("H:i", strtotime($_POST["time_start"]));
		$end = date("H:i", strtotime($_POST["time_end"]));
		$shift = Shifts::find($id);
		$shift->Name = $name;
		$shift->Time_start = $start;
		$shift->Time_end = $end;
		$shift->save();
		return redirect("/shifts");
	}

	public function viewAddShift(){
		return View("addShift", ["isAdmin" => true]);
	}

	public function AddShift(){
		$name = $_POST["name"];
		$start = date("H:i", strtotime($_POST["time_start"]));
		$end = date("H:i", strtotime($_POST["time_end"]));
		$shift = new Shifts;
		$shift->Name = $name;
		$shift->Time_start = $start;
		$shift->Time_end = $end;
		$shift->save();
		return redirect("/shifts");
	}

	public function find($scheduleId, $shiftId, $dayOfWeek){
		$details = Schedule_details::where('Schedule_id', '=', $scheduleId)
								->where('ShiftId', '=', $shiftId)
								->where('DayOfWeek', '=', $dayOfWeek)
								->get()
								->toArray();
		$schedule = Schedules::find($scheduleId);
		$shiftDetails = Shifts::find($shiftId);
		$DayOfWeek = null;
		$date = date('d/m/Y', strtotime(
			$schedule->Date_start . ' + '.($dayOfWeek - 2).' days'));
		if($dayOfWeek == 8){
			$DayOfWeek = "Chủ nhật";
		}
		else{
			$DayOfWeek = "Thứ " . $dayOfWeek;
		}
		$data = [
			"shiftName" => $shiftDetails->Name,
			"shiftStartTime" => $shiftDetails->Time_start,
			"shiftEndTime" => $shiftDetails->Time_end,
			"ScheduleName" => $schedule->Name,
			"dayOfWeek" => $DayOfWeek,
			"date" => $date,
			"isAdmin" => true,
			"employees" => []
		];
		foreach($details as $detail){
			$userDetails = Users::find($detail["UserId"]);
			array_push($data["employees"], [
				"empName" => $userDetails->DisplayName
			]);
		}
		return View("employeeInShift", $data);	
	}

	public function deleteShiftFromSchedule(){
    if(havePermission(5)){
  		$scheduleId = $_POST['scheduleId'];
  		$dayOfWeek  = $_POST['dayOfWeek'];
  		$shift      = $_POST['shift'];
  		$employeeId = $_POST['employeeId'];
  		if($_SESSION['Role'] == ADMIN_ROLE){
  			Schedule_details::where("Schedule_id", "=", $scheduleId)
												->where("DayOfWeek", "=", $dayOfWeek)
												->where("ShiftId", "=", $shift)
												->where("UserId", "=", $employeeId)
  											->delete();
  			echo "success";
  		}
  		else{
  			echo "nope";
  		}
    }
    else{
      echo "bạn không có quyền vào trang này";
    }
	}

	public function addShiftToSchedule(){
    if(havePermission(5)){
  		$scheduleId = $_POST['scheduleId'];
  		$dayOfWeek  = $_POST['dayOfWeek'];
  		$shift      = $_POST['shift'];
  		$employeeId = $_POST['employeeId'];
  		$foundShift = Schedule_details::where("Schedule_id", "=", $scheduleId)
  										->where("DayOfWeek", "=", $dayOfWeek)
  										->where("ShiftId", "=", $shift)
  										->where("UserId", "=", $employeeId)
  										->get();
  		if(count($foundShift) != 0){
  			echo "nope";
  			return;
  		}
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
  			$html .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
  			$html .= "<a href='".MAIN_PATH."/schedules/shift/find/".$scheduleId."/".$shift."' style='color: inherit;'>
  				<i class='fa fa-list' style='cursor: pointer; color: inherit;'></i>
  			</a>";
  			$html .= "</div>";
  			echo $html;
  		}
    }		
    else{
			echo $_SESSION['Uid'];
      echo "bạn không có quyền vào trang này";
    }
	}
}
?>