<?php
use Model\Schedules;
use Model\Shifts;
use Model\Employee_Attendance;
use Model\Users;
class AttendanceController{

	public function Index(){
		// data to render
		$data = [];
		// get the latest schedule
		$scheduleInfo = Schedules::orderBy('id', 'desc')->first();
		// get the employee schedule
		$userId = $_SESSION['Uid'];
		$dayOfWeekStr = date('w');
		$dayOfWeek = $dayOfWeekStr == '0' ? 8 : int($dayOfWeekStr) + 1;
		$employeeSchedule = Schedules::find($scheduleInfo->Id)
										->details()
										->where('userId', '=', $userId)
										->where('dayOfWeek', '=', $dayOfWeek)
										->get();
		// get current time
		date_default_timezone_set("Asia/Ho_Chi_Minh");		
		$time = date('H:i:s');
		$date = date('Y-m-d');
		// compare to employee schedule
		foreach($employeeSchedule as $schedule){
			$shift = Shifts::find($schedule->ShiftId);
			$timeStart = date($shift->Time_start);
			$timeEnd = date($shift->Time_end);
			if($timeStart <= $time && $timeEnd >= $time){
				// check if user have already taken attendance
				$attendance = Employee_Attendance::where('Date', '=', $date)
													->where('ShiftId', '=', $shift->Id)
													->where('UserId', '=', $_SESSION['Uid'])
													->get();
				if(count($attendance) == 0){
					$data = $shift;
				}
			}
		}
		// print_r($data);
		// render
		return View("EmployeeAttendance", ["shift" => $data]);
	}

	public function takeAttendance(){
		$userId = $_SESSION['Uid'];
		date_default_timezone_set("Asia/Ho_Chi_Minh");		
		$date = date('Y-m-d');
		$time = date('H:i:s');
		$shiftId = $_POST['ShiftId'];
		$attendance = new Employee_Attendance;
		$attendance->UserId = $userId;
		$attendance->Date = $date;
		$attendance->Time = $time;
		$attendance->ShiftId = $shiftId;
		$attendance->save();
		return View("EmployeeAttendance", [
			"Message" => "Điểm danh thành công",
			"hasMessage" => true
			]);
	}

	public function showList(){
		$displayname = Users::find($_SESSION['Uid'])->DisplayName;
		$attendances = Employee_Attendance::where('UserId', '=', $_SESSION['Uid'])
											->get();
		$data = [];
		foreach($attendances as $attendance){
			$shiftInfo = Shifts::find($attendance->ShiftId);
			$info = [
				"Date" => $attendance->Date,
				"Time" => $attendance->Time,
				"ShiftName" => $shiftInfo->Name,
				"ShiftTime" => $shiftInfo->Time_start . " - " . $shiftInfo->Time_end
			];
			array_push($data, $info);
		}
		return View("EmployeeAttendanceList", [
			"attendances" => $data,
			"DisplayName" => $displayname
			]);
	}
}
?>																																										