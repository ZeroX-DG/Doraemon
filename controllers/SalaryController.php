<?php
use Model\Users;
use Model\Employee_Attendance;
use Model\Shifts;
use Illuminate\Database\Capsule\Manager as Capsule;

class SalaryController{
	public function Index(){
		$data = [];
		$employees = Users::where("Role", "=", EMPLOYEE_ROLE)->get()->toArray();
		foreach ($employees as $employee) {
			// get user attendance and shift
			$attendances = Employee_Attendance::where("UserId", "=", $employee["Id"])
												->where(Capsule::raw('MONTH(Time)'), '=', Capsule::raw('MONTH(CURRENT_DATE())'))
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
			$employee["AttendanceHour"] = $AttendanceHour;
			array_push($data, $employee);
		}
		return View("SalaryCalculator", ["isAdmin" => true, "employees" => $data]);
	}
}

?>