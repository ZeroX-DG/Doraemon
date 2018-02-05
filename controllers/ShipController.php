<?php
use Model\Ships;
use Model\Users;
class ShipController{
	public function Index(){
		$ships = Ships::where("Status", "=", 0)->get()->toArray();
		$data = [];
		foreach ($ships as $ship) {
			$user = Users::where("Id", "=", $ship["Shipper"])->first();
			$ship["Shipper"] = $user->DisplayName;
			$ship["Distance"] = number_with_comma($ship["Distance"]) . " Km";
			$ship["Total"] = number_with_comma($ship["Total"]) . " vnđ";
			$ship["ShipPrice"] = number_with_comma($ship["ShipPrice"]) . " vnđ";
			array_push($data, $ship);
		}
		return View("Shipmanagement", ["isAdmin" => $_SESSION['Role'] == ADMIN_ROLE, "ships" => $data]);
	}

	public function ViewAddShip(){
		if (havePermission(12)) {
			$data = Users::where("Role", "=", EMPLOYEE_ROLE)->get();
			return View("addShip", ["isAdmin" => $_SESSION['Role'] == ADMIN_ROLE, "employees" => $data]);
		}
		else {
			echo "bạn không có quyền vào trang này";
		}
	}

	public function addShip(){
		if (havePermission(12)) {
			$address = $_POST['address'];
			$phone = $_POST['phone'];
			$total = str_replace(",", "",$_POST['total']);
			$shipPrice = $_POST['ShipPrice'];
			$amount = $_POST['amount'];
			$distance = $_POST['distance'];
			$shipper = $_POST['shipper'];

			$ship = new Ships;
			$ship->Address = $address;
			$ship->Phone = $phone;
			$ship->Total = $total;
			$ship->ShipPrice = $shipPrice;
			$ship->Amount = $amount;
			$ship->Distance = $distance;
			$ship->Shipper = $shipper;
			$ship->save();
			redirect("/ships");
		}
		else {
			echo "bạn không có quyền vào trang này";
		}
	}

	public function History()
	{
		$ships = Ships::where("Status", "=", 1)->get()->toArray();
		$data = [];
		foreach ($ships as $ship) {
			$user = Users::where("Id", "=", $ship["Shipper"])->first();
			$ship["Shipper"] = $user->DisplayName;
			$ship["Distance"] = number_with_comma($ship["Distance"]) . " Km";
			$ship["Total"] = number_with_comma($ship["Total"]) . " vnđ";
			$ship["ShipPrice"] = number_with_comma($ship["ShipPrice"]) . " vnđ";
			array_push($data, $ship);
		}
		return View("Shipmanagement", [
			"isAdmin" => $_SESSION['Role'] == ADMIN_ROLE, 
			"ships" => $data, 
			"history" => true
		]);
	}

	public function Cancel(){
		if (havePermission(15)) {
			$id = $_POST['Id'];
			Ships::where("Id", "=", $id)->delete();
			echo "done";
		}
		else {
			echo "bạn không có quyền vào trang này";
		}
	}

	public function ViewEdit($id){
		if (havePermission(14)) {
			$emps = Users::where("Role", "=", EMPLOYEE_ROLE)->get();
			$ship = Ships::where('Id', '=', $id)->first();
			$shipPrice = [
				[
					"price" => "5000",
					"selected" => $ship->ShipPrice == 5000 ? "selected" : "",
					"label" => "5,000"
				],
				[
					"price" => "10000",
					"selected" => $ship->ShipPrice == 10000 ? "selected" : "",
					"label" => "10,000"
				],
				[
					"price" => "15000",
					"selected" => $ship->ShipPrice == 15000 ? "selected" : "",
					"label" => "15,000"
				]
			];
			foreach ($emps as $emp) {
				if($ship->Shipper == $emp->Id){
					$emp->selected = "selected";
				}
				else{
					$emp->selected = "";
				}
			}
			return View("editShip", [
				"isAdmin" => $_SESSION['Role'] == ADMIN_ROLE, 
				"employees" => $emps,
				"ship" => $ship,
				"shipPrice" => $shipPrice
			]);
		}
		else {
			echo "bạn không có quyền vào trang này";
		}
	}
	public function Edit($id){
		if (havePermission(14)) {
			$ship = Ships::find($id);

			$address = $_POST['address'];
			$phone = $_POST['phone'];
			$total = str_replace(",", "",$_POST['total']);
			$shipPrice = $_POST['ShipPrice'];
			$amount = $_POST['amount'];
			$distance = $_POST['distance'];
			$shipper = $_POST['shipper'];

			$ship->Address = $address;
			$ship->Phone = $phone;
			$ship->Total = $total;
			$ship->ShipPrice = $shipPrice;
			$ship->Amount = $amount;
			$ship->Distance = $distance;
			$ship->Shipper = $shipper;
			$ship->save();
			redirect("/ships");
		}
		else {
			echo "bạn không có quyền vào trang này";
		}
	}

	public function Done(){
		if (havePermission(13)) {
			$id = $_POST['Id'];
			$ship = Ships::find($id);
			$ship->Status = 1;
			$ship->save();
			echo "done";
		}
		else {
			echo "bạn không có quyền vào trang này";
		}
	}

	public function ViewSearch(){
		if(isset($_GET['address']) || 
			isset($_GET['shipper']) ||
			isset($_GET['distance']) ||
			isset($_GET['phone'])){
			// search here
			$address = isset($_GET['address']) ? $_GET['address'] : "";
			$shipper = isset($_GET['shipper']) ? $_GET['shipper'] : "";
			$distance = isset($_GET['distance']) ? $_GET['distance'] : "";
			$phone = isset($_GET['phone']) ? $_GET['phone'] : "";
			$result = $this->search($address, $shipper, $distance, $phone);
			return View("searchShip", ["isAdmin" => $_SESSION['Role'] == ADMIN_ROLE, "ships" => $result]);
		}
		else{
			return View("searchShip", ["isAdmin" => $_SESSION['Role'] == ADMIN_ROLE]);
		}	
	}

	public function search($address, $shipper, $distance, $phone){
		$result = null;
		if($address != ""){
			$result = Ships::where("Address" , "LIKE", "%".$address."%");
		}
		if($shipper != "" && $result != null){
			$result = $result->where("Shipper" , "LIKE", "%".$shipper."%");
		}
		else if($shipper != "" && $result == null){
			$possible_shipper = Users::where('DisplayName', 'LIKE', "%".$shipper."%")->get();
			$shippers = [];
			foreach($possible_shipper as $shipper) {
				array_push($shippers, $shipper->Id);
			}
			$result = Ships::whereIn("Shipper" , $shippers);
		}
		if($distance != "" && $result != null){
			$result = $result->where("Distance" , "=", $distance);
		}
		else if($distance != "" && $result == null){
			$result = Ships::where("Distance" , "=", $distance);
		}
		if($phone != "" && $result != null){
			$result = $result->where("Phone" , "LIKE", "%".$phone."%");
		}
		else if($phone != "" && $result == null){
			$result = Ships::where("Phone" , "LIKE", "%".$phone."%");
		}
		$ships = $result == null ? [] : $result->get();
		$data = [];
		foreach ($ships as $ship) {
			$user = Users::where("Id", "=", $ship["Shipper"])->first();
			$ship["Shipper"] = $user->DisplayName;
			$ship["Distance"] = number_with_comma($ship["Distance"]) . " Km";
			$ship["Total"] = number_with_comma($ship["Total"]) . " vnđ";
			$ship["ShipPrice"] = number_with_comma($ship["ShipPrice"]) . " vnđ";
			array_push($data, $ship);
		}
		return $data;
	}
}
?>