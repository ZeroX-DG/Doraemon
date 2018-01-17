<?php
use Model\Schedules;
use Model\Users;
use Model\Shifts;
use Model\Schedule_details;
use Model\Employee_Attendance;
use Illuminate\Database\Capsule\Manager as Capsule;
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

	// public function details($scheduleId = null){
	// 	// data to render
	// 	$data = [];
	// 	// schedule info
	// 	$scheduleInfo = null;
	// 	if($scheduleId == null){
	// 		// if there's no schedule id 
	// 		// then get the latest one !
	// 		$scheduleInfo = Schedules::orderBy('id', 'desc')->first();
	// 	}
	// 	else{
	// 		$scheduleInfo = Schedules::find($scheduleId);
	// 	}	
		
	// 	if($scheduleInfo == null){
	// 		if($_SESSION['Role'] == ADMIN_ROLE){
	// 			return View("AddSchedule", ["isAdmin" => true]);
	// 		}
	// 	}
	// 	$scheduleInfo = $scheduleInfo->toArray();
	// 	// get employee list
	// 	$employees = Users::where('Role', '>', ADMIN_ROLE)->get();
	// 	// call getEmployeeSchedule with employee id
	// 	foreach($employees as $employee){
	// 		$schedules = $this->getEmployeeSchedule($scheduleInfo["Id"], $employee->Id);
	// 		// get shift details
	// 		$shifts_details = []; 
	// 		for($i = 2; $i < 9; $i++){
	// 			$shifts_details["t" . $i] = [];
	// 		}
	// 		foreach($schedules as $schedule){
	// 			// key for array
	// 			$dayOfWeek = "t".$schedule["DayOfWeek"];
				
	// 			$shift = Shifts::find($schedule["ShiftId"])->toArray();
	// 			$timeStartParts = date_parse($shift["Time_start"]);
	// 			$timeEndParts = date_parse($shift["Time_end"]);
	// 			$timeAt = "";
	// 			if($timeStartParts["hour"] < 12){
	// 				$timeAt = "morning";
	// 			}
	// 			else if($timeStartParts["hour"] < 17){
	// 				$timeAt = "afternoon";
	// 			}
	// 			else{
	// 				$timeAt = "night";
	// 			}
	// 			$shift["Time_start"] = $timeStartParts["hour"] . 
	// 									":" . 
	// 									$timeStartParts["minute"];
	// 			$shift["Time_end"] = $timeEndParts["hour"] . 
	// 									":" . 
	// 									$timeEndParts["minute"];
	// 			$shift["timeAt"] = $timeAt;
	// 			$shift["ShiftId"] = $schedule["ShiftId"];
	// 			$shift["Date"] = $schedule["Date"];
	// 			$shift["DayOfWeek"] = $schedule["DayOfWeek"];
	// 			array_push($shifts_details[$dayOfWeek], $shift);
	// 			// sort shifts
	// 			usort($shifts_details[$dayOfWeek], function ( $a, $b ) {
	// 			    return strtotime($a["Time_start"]) - strtotime($b["Time_start"]);
	// 			});
	// 		}

	// 		array_push($data, [
	// 			"Uid" => $employee->Id,
	// 			"DisplayName" => $employee->DisplayName, 
	// 			"Shifts" => $shifts_details,
	// 			]);
	// 	}
	// 	// Other schedule
	// 	$otherSchedule = Schedules::all()->toArray();
	// 	// is admin
	// 	$isAdmin = $_SESSION["Role"] == ADMIN_ROLE;
	// 	// all shift
	// 	$allShifts = Shifts::all();
	// 	// render
	// 	View("ScheduleDetails", [
	// 		"schedules" => $data, 
	// 		"scheduleInfo" => $scheduleInfo,
	// 		"otherSchedule" => $otherSchedule,
	// 		"isAdmin" => $isAdmin,
	// 		"employees" => $employees,
	// 		"allShifts" => $allShifts,
	// 	]);
	// }

	public function addSchedule()
	{
    if(havePermission(4)){
  		if($_SESSION['Role'] == ADMIN_ROLE){
  			$schedule = new Schedules;
  			$schedule->Name = $_POST['name'];
  			$schedule->Date_start = date($_POST['date_start']);
  			$schedule->Date_end = date('Y-m-d', strtotime(
  				$_POST['date_start']. ' + 6 days'
  			));
  			$schedule->save();
  			redirect('/schedules');
  		}
    }
    else{
      echo "bạn không có quyền vào trang này";
    }
	}

	public function deleteSchedule(){
    if(havePermission(6)){
  		if($_SESSION['Role'] == ADMIN_ROLE){
  			$id = $_POST['scheduleId'];
  			$schedule = Schedules::find($id);
  			$schedule->delete();
  			redirect('/schedules');
  		}
    }
    else{
      echo "bạn không có quyền vào trang này";
    }
	}

	public function editScheduleName(){
    if(havePermission(5)){
  		if($_SESSION['Role'] == ADMIN_ROLE){
  			$id = $_POST['scheduleId'];
  			$name = $_POST['name'];
  			$schedule = Schedules::find($id);
  			$schedule->Name = $name;
  			$schedule->save();
  			redirect('/schedules');
  		}
    }
    else{
      echo "bạn không có quyền vào trang này";
    }
	}

	public function details($scheduleId = null) {
		$scheduleInfo = null;
		$schedules = Schedules::all()->toArray();
	
		if(count($schedules) == 0){
			if($_SESSION['Role'] == ADMIN_ROLE){
				return View("AddSchedule", ["isAdmin" => true]);
			}
		}
		if($scheduleId == null){
			// if there's no schedule id 
			// then get the latest one !
			$scheduleInfo = Schedules::orderBy('id', 'desc')->first();
		}
		else{
			$scheduleInfo = Schedules::find($scheduleId);
		}	
		
		if($scheduleInfo){
			$scheduleInfo = $scheduleInfo->toArray();
		}
		else{
			echo "lịch không tồn tại";
			return;
		}
    if ($_SESSION['Role'] != ADMIN_ROLE) {
      return $this->viewScheduleDetails($scheduleInfo["Id"]); 
    }
		$data = [];
		for($i = 2; $i < 9; $i++){
			array_push($data, ["Id" => $i]);
		}
		return View("ScheduleDetails", [
			"isAdmin" => true,
			"scheduleInfo" => $scheduleInfo,
			"otherSchedule" => $schedules,
			"data" => $data
		]);
	}

  public function viewScheduleDetails($schedule_id) {
    /* 
    data = [
      "shifts" => [
        "name" => "ca sang",
        "content" => [
          "t2" => [asdasd, asdasd, adsadasd], // each day
          "t3" => [asasd, asdasd, asdasd]
        ]
      ]
    ]
    */
    $shifts = Shifts::all();
    $data = [];
    foreach($shifts as $shift) {
      $shiftData = [];
			$shiftData["name"] = $shift->Name;
			$shiftData["time"] = $shift->Time_start . " -> " . $shift->Time_end;
      $shiftData["content"] = [];
      for($day = 2; $day < 9; $day++) {
        $shiftInDay = Schedule_details::where('Schedule_id', '=', $schedule_id)
                                      ->where('DayOfWeek', '=', $day)
                                      ->where('ShiftId', '=', $shift->Id)
                                      ->get();
        $employees = [];
        foreach($shiftInDay as $item) {
          $employee = Users::where('Id', '=', $item->UserId)->first();
          array_push($employees, $employee->DisplayName);
        }
        $shiftData["content"]["t" . $day] = implode("<br><br>" ,$employees);
      }
      array_push($data, $shiftData);
    }
    return View("WeekSchedule", ["isAdmin" => true, "data" => $data]);
  }

	public function detailOfDate($scheduleId, $date) {
		// plan:
		// select users
		// for each users take the id to find the schedule details
		// with the associate DayOfWeek,ScheduleId and userId
		// then save each shiftId into an array
		// select all the shifts
		// check if the shiftId is in the previous array
		// if yes then put in another array the value true
		// else put in false
		// so that when we render we will get a nice array of array of boolean
		$users = Users::all();
		$shifts = Shifts::all();
		$schedule_begin_date = Schedules::where('Id', '=', $scheduleId)
																		->first()->Date_start;
		$time = date("Y-m-d", strtotime($schedule_begin_date . " +".($date - 2)." days"));
		$arrayOfBooleans = [];

		foreach($users as $user) {
			$arrayOfShifts = [];
			$arrayOfBoolean = [];
			$raw = Schedule_details::where('Schedule_id', '=', $scheduleId)
															->where('DayOfWeek', '=', $date)
															->where('userId', '=', $user->Id)
															->get();
      $attendances = Employee_Attendance::where("UserId", "=", $user->Id)
                        ->where(Capsule::raw('MONTH(Date)'), '=', Capsule::raw('MONTH(CURRENT_DATE())'))
                        ->get();
      $AttendanceHour = 0;
      foreach ($attendances as $attendance) {
        $shift = Shifts::find($attendance->ShiftId);
        // compare with shift
        $start_time = strtotime($shift->Time_start);
        $end_time = strtotime($shift->Time_end);

        $totalHour = $end_time - $start_time;
        $actual_time = strtotime($attendance->Time);

        $late_time = $actual_time - $start_time;

        $remained_time = $totalHour - $late_time;
        $AttendanceHour += round($remained_time / 60 / 60, 1);
      }
      // return in hour
			if(count($raw) > 0){
				foreach($raw as $record) {
					array_push($arrayOfShifts, $record->ShiftId);
				}
				// push the user represent the data
				$arrayOfBoolean["user"] = $user;
				$arrayOfBoolean["checkState"] = [];
        $arrayOfBoolean["AttendanceHour"] = $AttendanceHour;
				foreach($shifts as $shift) {
					if(in_array($shift->Id, $arrayOfShifts)){
						array_push($arrayOfBoolean["checkState"], ["id" => $shift->Id, "state" => true]);
					}
					else {
						array_push($arrayOfBoolean["checkState"], ["id" => $shift->Id, "state" => false]);
					}
				}
			}
			else {
				$arrayOfBoolean = [];
				$arrayOfBoolean["user"] = $user;
				$arrayOfBoolean["checkState"] = [];
        $arrayOfBoolean["AttendanceHour"] = $AttendanceHour;
				foreach($shifts as $shift) {
					array_push($arrayOfBoolean["checkState"], ["id" => $shift->Id, "state" => false]);
				}
			}
			array_push($arrayOfBooleans, $arrayOfBoolean);
		}
		
		$shiftCount = count($shifts);
		return View("ScheduleDetailsOfDate", [
			'isAdmin' => true, 
			'dayOfWeek' => $date,
			'date' => date("d", strtotime($time)),
			'month' => date("m", strtotime($time)),
			'data'=>$arrayOfBooleans,
			'shiftCount' => $shiftCount,
			'shifts' => $shifts,
			'scheduleId' => $scheduleId
		]);
	}

  public function showEmployeeSchedule($scheduleId = null) {
    /*
    response data
    [
      [Shift, [mon=>[], tue=>[], wen=>[]]]
    ]
    */
    // first get all the shifts
    // loop through them
    // find list of date from mon -> sun of that shift
    // push to array
    // return it
    if($scheduleId == null) {
      $scheduleId = Schedules::all()->last()->Id;
    }
    $data = [];
    $shifts = Shifts::all();
    foreach($shifts as $shift) {
      $userList = [];
      $schedule_details = Schedule_details::where('Schedule_id', $scheduleId)
                                          ->where('ShiftId', $shift->Id)
                                          ->orderBy('DayOfWeek')
                                          ->get();
      $userOfDay = [];
      foreach($schedule_details as $detail) { 
        $user = Users::where('Id', '=', $detail->UserId)->first();
        array_push($userOfDay, ["name" => $user->DisplayName]);      
      }
      array_push($userList, [
       "usersOfDay" => $userOfDay
      ]);
      array_push($data, ["shift" => $shift->Name, "users" => $userList]);
    }
    return View("viewWeekSchedule", ["isAdmin" => false, "data" => $data]);
  }
}
?>