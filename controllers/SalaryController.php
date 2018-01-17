<?php
use Model\Users;
use Model\Employee_Attendance;
use Model\Shifts;
use Model\salary_history;
use Model\salary_history_details;
use Illuminate\Database\Capsule\Manager as Capsule;

class SalaryController{
	public function Index(){
		$data = [];
		$employees = Users::all()->toArray();
		foreach ($employees as $employee) {
			// get user attendance and shift
			$attendances = Employee_Attendance::where("UserId", "=", $employee["Id"])
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
			$employee["AttendanceHour"] = $AttendanceHour;
			array_push($data, $employee);
		}
		return View("SalaryCalculator", ["isAdmin" => true, "employees" => $data]);
	}
  public function saveToHistory(){
    $data = $_POST['data'];
    $month = $_POST['month'];
    // data example:
    // [
    //   {
    //     uid: 1,
    //     work_hour: ...,
    //     salary_by_hour: ...,
    //     bonus: ...,
    //     cash_advance: ...,
    //     total_salary: ...
    //   }
    // ]
    date_default_timezone_set("Asia/Ho_Chi_Minh");    
    $date = date('Y-m-d');
    $newHistory = new salary_history;
    $newHistory->Name = "bảng lương tháng " . $month . "/" . date("Y");
    $newHistory->createAt = $date; 
    // then insert to the new history
    if($newHistory->save()){
      foreach($data as $record){
        $detail = new salary_history_details;
        $detail->userId = $record['uid'];
        $detail->historyId = $newHistory->id;
        $detail->work_hour = $record['work_hour'];
        $detail->salary_by_hour = $record['salary_by_hour'];
        $detail->bonus = $record['bonus'];
        $detail->cash_advance = $record['cash_advance'];
        $detail->total_salary = $record['total_salary'];
        $detail->save();
      }
    }
  }

  public function viewHistory($historyId=null) {
    if($historyId == null ) {
      $data = salary_history::orderBy('id', 'DESC')->get();
      return View('SalaryHistoryList', ["isAdmin" => true, "data" => $data]);
    }
    else {
      $raw = salary_history_details::where('historyId', '=', $historyId)
                                      ->get()
                                      ->toArray();
      $data = [];
      foreach($raw as $record) {
        $user = Users::where('Id', '=', $record['userId'])->first();
        $record['userName'] = $user->DisplayName;
        $record['salary_by_hour'] = number_format($record['salary_by_hour']) . ' vnđ';
        $record['bonus'] = number_format($record['bonus']) . ' vnđ';
        $record['cash_advance'] = number_format($record['cash_advance']) . ' vnđ';
        $record['total_salary'] = number_format($record['total_salary']) . ' vnđ';
        array_push($data, $record);
      }

      return View("SalaryHistory", ["isAdmin" => true, "data" => $data]);
    }
  }
}

?>