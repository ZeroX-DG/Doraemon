<?php
use Model\Schedules;
use Model\Shifts;
use Model\Employee_Attendance;
use Model\Users;
use Model\Schedule_details;
class AttendanceController{

	public function Index(){
		// data to render
		$data = [];
		// get the latest schedule
		$scheduleInfo = Schedules::orderBy('id', 'desc')->first();
		// get the employee schedule
		$userId = $_SESSION['Uid'];
		$dayOfWeekStr = date('w');
		$dayOfWeek = $dayOfWeekStr == '0' ? 8 : $dayOfWeekStr + 1;
		// get current time
		date_default_timezone_set("Asia/Ho_Chi_Minh");		
		$time = date('H:i:s');
		$date = date('Y-m-d');
		if(
			$scheduleInfo->Date_start >= $date && 
			$scheduleInfo->Date_end <= $date
		){
			return View("EmployeeAttendance");
		}
		$employeeSchedule = Schedules::find($scheduleInfo->Id)
										->details()
										->where('userId', '=', $userId)
										->where('dayOfWeek', '=', $dayOfWeek)
										->get();
		
		// check for IP match
		$attendanceIP = Employee_Attendance::where('IP', '=', get_client_ip())
										->first();
		$IPTaken = null;
		if($attendanceIP != null){
			$IPTaken = $attendanceIP->UserId != $_SESSION['Uid'];
		}
										
		if($IPTaken){
			return View("EmployeeAttendance", [
				"Message" => "Không được điểm danh hộ !", 
				"hasMessage" => true,
				"MessageType" => "danger"
			]);
		}
		// compare to employee schedule
		date_default_timezone_set("Asia/Ho_Chi_Minh");
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
		$attendance->IP = get_client_ip(); // in the helper file
		$attendance->save();
		return View("EmployeeAttendance", [
			"Message" => "Điểm danh thành công",
			"hasMessage" => true,
			"MessageType" => "success !"
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
				"Time_in" => $attendance->Time_in,
				"Time_out" => $attendance->Time_out,
				"ShiftName" => $shiftInfo->Name,
				"ShiftTime" => $shiftInfo->Time_start . " - " . $shiftInfo->Time_end,
				"Note" => $attendance->Note
			];
			array_push($data, $info);
		}
		return View("EmployeeAttendanceList", [
			"attendances" => $data,
			"DisplayName" => $displayname
		]);
	}

	public function listAll(){
		if($_SESSION['Role'] == ADMIN_ROLE){
			$attendances = Employee_Attendance::orderBy('Date', 'DESC')->get();
			$data = [];
			foreach($attendances as $attendance){
				$shiftInfo = Shifts::find($attendance->ShiftId);
				$userInfo = Users::find($attendance->UserId);
				$info = [
					"DisplayName" => $userInfo->DisplayName,
					"Date" => $attendance->Date,
					"Time_in" => $attendance->Time_in,
					"Time_out" => $attendance->Time_out,
					"ShiftName" => $shiftInfo->Name,
					"ShiftTime" => $shiftInfo->Time_start . " - " . $shiftInfo->Time_end
				];
				array_push($data, $info);
			}
			return View("AllEmployeeAttendance", [
				"attendances" => $data,
				"isAdmin" => true
			]);
		}
	}

	public function viewAttendanceSchedules(){
		if($_SESSION['Role'] == ADMIN_ROLE){
		$schedules = Schedules::all();
			$data = [];
			foreach ($schedules as $schedule) {
				$arr = [
					"name" => $schedule->Name,
					"duration" => date(
						"d/m/Y",
						strtotime($schedule->Date_start)) . 
						" -> " . 
						date("d/m/Y",strtotime($schedule->Date_end)
					),
					"id" => $schedule->Id
				];
				array_push($data, $arr);
			}
			return View("scheduleList", [
				"isAdmin" => true,
				"schedules" => $data
			]);
		}
	}

	public function viewScheduleDetails($id, $date){
		//$employees = Users::where('Role', '=', EMPLOYEE_ROLE)->get();
		if($_SESSION['Role'] == ADMIN_ROLE){
			$details = Schedule_details::where("Schedule_id", '=', $id)
										->where("DayOfWeek", '=', $date)
										->get();
			$employees = [];
			$schedule = Schedules::find($id);
			$currentDate = date(
				"Y-m-d", 
				strtotime(
					$schedule->Date_start . " + " .($date - 2) . " days"
				)
			);
			foreach ($details as $detail) {
				$user = Users::find($detail->UserId);
				$shift = Shifts::find($detail->ShiftId);
				$condition = "chưa chấm";
				$time_in_hour = "00";
				$time_in_minute = "00";
				$time_out_hour = "00";
				$time_out_minute = "00";
				date_default_timezone_set("Asia/Ho_Chi_Minh");
				$attendances = Employee_Attendance::where("UserId", "=", $detail->UserId)
									->where("Date", "=", date("Y-m-d", strtotime($currentDate)))
									->where("ShiftId", "=", $detail->ShiftId)
									->first();
				
				if(count($attendances) != 0){
					$condition = "đã chấm";
					$time_in_hour = date("H", strtotime($attendances->Time_in));
					$time_in_minute = date("i", strtotime($attendances->Time_in));
					$time_out_hour = date("H", strtotime($attendances->Time_out));
					$time_out_minute = date("i", strtotime($attendances->Time_out));
				}
				$note = '';
				if (isset($attendances->Note))
					$note = $attendances->Note;
				$arr = [
					"user" => $user,
					"shift" => $shift,
					"condition"=> $condition,
					"time_in_hour" => $time_in_hour, 
					"time_in_minute" => $time_in_minute,
					"time_out_hour" => $time_out_hour, 
					"time_out_minute" => $time_out_minute,
					"checked" => $condition == "đã chấm",
					"note" => $note
				];
				array_push($employees, $arr);
			}
			//print_r($note);
			$dayOfWeek = [];
			$strDayOfWeek = null;
			if($date == 8){
				$strDayOfWeek = "Chủ nhật";
			}
			else{
				$strDayOfWeek = "Thứ " . $date;
			}
			for($i = 2; $i < 9; $i++){
				$asWord = null;
				if($i == 8){
					$asWord = "Chủ nhật";
					
				}
				else{
					$asWord = "Thứ " . $i;
				}
				$arr = ["asNumber" => $i, "asWord" => $asWord];
				array_push($dayOfWeek, ["day"=>$arr]);
			}
			
			return View("employeeList", [
				"isAdmin" => true, 
				"employees" => $employees,
				"scheduleInfo" => $schedule,
				"dayOfWeek" => $dayOfWeek,
				"strDayOfWeek" => $strDayOfWeek,
				"currentDate" => $currentDate,
				"currentDateVN" => date("d/m/Y", strtotime($currentDate))
			]);
		}
	}

	public function adminTakeAttendance(){
		if($_SESSION['Role'] == ADMIN_ROLE){
			$userId = $_POST['Uid'];
			$shiftId = $_POST['ShiftId'];
			$time_in_hour = $_POST['time_in_hour'];
			$time_in_minute = $_POST['time_in_minute'];
			$time_out_hour = $_POST['time_out_hour'];
			$time_out_minute = $_POST['time_out_minute'];
			$status = $_POST['status'];
			$note = $_POST['note'];
			$date = date('Y-m-d', strtotime($_POST['date']));
			if ($status != 'check') {
				Employee_Attendance::where("UserId", "=", $userId)
								->where("ShiftId", "=", $shiftId)
								->where("Date", "=", $date)
								->delete();
				echo "done";
				return;
			}

			$attendance = Employee_Attendance::where("UserId", "=", $userId)
																				->where("ShiftId", "=", $shiftId)
																				->where("Date", "=", $date)
																				->first();
			if (count($attendance) == 0) {
				$attendance = new Employee_Attendance;
			}
			$time_in = date('H:i:s', strtotime($time_in_hour . ":" .$time_in_minute . ":" . "00"));
			$time_out = date('H:i:s', strtotime($time_out_hour . ":" .$time_out_minute . ":" . "00"));
			$attendance->UserId = $userId;
			$attendance->Date = $date;
			$attendance->Time_in = $time_in;
			$attendance->Time_out = $time_out;
			$attendance->ShiftId = $shiftId;
			$attendance->IP = "0.0.0.0";
			$attendance->Note = $note;
			$attendance->save();
			echo "done";
		}
	}
}
?>																																										