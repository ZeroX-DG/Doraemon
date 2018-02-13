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
				return View("AddSchedule", ["isAdmin" => $_SESSION['Role'] == ADMIN_ROLE]);
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
			"isAdmin" => $_SESSION['Role'] == ADMIN_ROLE,
			"scheduleInfo" => $scheduleInfo,
			"otherSchedule" => $schedules,
			"data" => $data,
			"dayOfWeekList" => [
				["day" => 2],
				["day" => 3],
				["day" => 4],
				["day" => 5],
				["day" => 6],
				["day" => 7],
				["day" => 8]
			]
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
    $employees = Users::all();
    $data = [];
    foreach($employees as $employee) {
      $shiftData = [];
			$shiftData["name"] = $employee->DisplayName;
      $shiftData["content"] = [];
      for($day = 2; $day < 9; $day++) {
        $shiftInDay = Schedule_details::where('Schedule_id', '=', $schedule_id)
                                      ->where('DayOfWeek', '=', $day)
                                      ->where('UserId', '=', $employee->Id)
                                      ->get();
        $shifts = [];
        foreach($shiftInDay as $item) {
          $shift = Shifts::where('Id', '=', $item->ShiftId)->first();
          array_push($shifts, $shift->Name . '<br>' . $shift->Time_start . '->' . $shift->Time_end);
        }
        $shiftData["content"]["t" . $day] = implode("<br><br>" ,$shifts);
      }
      array_push($data, $shiftData);
    }
    return View("WeekSchedule", [
      "isAdmin" => $_SESSION['Role'] == ADMIN_ROLE, 
      "data" => $data
    ]);
	}
	
	public function viewScheduleDetailsByDay($scheduleId, $dayOfWeek) {
		// plan:
		// select schedule details
		// foreach details
		// get shift in that details
		// foreach shifts
		// get user in that shift in that schedule
		// return array of user
		// data type
		/*
			data => [
				shifts => [
					shift => name,
					users => [
						sdasd, asdasd ,asd asd,asd
					]
				]
			]
		*/
		$shifts = [];
		$data = [];
		$schedule_details = Schedule_details::where('Schedule_id', '=', $scheduleId)
																				->where('DayOfWeek', '=', $dayOfWeek)
																				->get();
		foreach($schedule_details as $detail) {
			array_push($shifts, $detail->ShiftId);
		}
		foreach($shifts as $shift) {
			$temp = [];
			$current_shift = Shifts::where('Id', '=', $shift)->first();
			$temp["shiftName"] = $current_shift->Name;
			$temp["Time_start"] = $current_shift->Time_start;
			$temp["Time_end"] = $current_shift->Time_end;
			$temp["employees"] = [];
			$shiftDetails = Schedule_details::where('Schedule_id', '=', $scheduleId)
																			->where('ShiftId', $shift)
																			->where('DayOfWeek', '=', $dayOfWeek)
																			->get();
			foreach($shiftDetails as $shiftDetail) {
				$emp = Users::where('Id', '=', $shiftDetail->UserId)->first()->DisplayName;
				array_push($temp["employees"], ["name" => $emp]);
			}
			array_push($data, $temp);
		}
		$date = Schedule_details::where('Schedule_id', '=', $scheduleId)
														->where('DayOfWeek', '=', $dayOfWeek)
														->first()
														->Date;
		
		return View("ScheduleByDay", [
			"data" => $data,
			"date" => $date,
			"dayOfWeek" => $dayOfWeek,
			"isAdmin" => $_SESSION['Role'] == ADMIN_ROLE
		]);
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

        $actual_time_in = strtotime($attendance->Time_in);
        $actual_time_out = strtotime($attendance->Time_out);

        $remained_time = $actual_time_out - $actual_time_in;
        $AttendanceHour += round($remained_time / (60 * 60), 1);
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
			'isAdmin' => $_SESSION['Role'] == ADMIN_ROLE, 
			'dayOfWeek' => $date,
			'date' => date("d", strtotime($time)),
			'month' => date("m", strtotime($time)),
			'data'=>$arrayOfBooleans,
			'shiftCount' => $shiftCount,
			'shifts' => $shifts,
			'scheduleId' => $scheduleId
		]);
	}
}
?>