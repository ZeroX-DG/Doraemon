<?php
use Model\Storages;
use Model\StorageContent;
use Model\Products;
use Model\StorageImport;
use Model\StorageExport;
use Model\temp_import;
class StorageController{
	public function Index(){
		$Products = Products::all();
		$storages = Storages::all();
		$data = [];

    foreach($Products as $product) {
			$product_details = [];
			$temp = temp_import::where('product_id', '=', $product->Id)->get();

			if (count($temp) > 0) {
				
				foreach($temp as $tem) {
					$product_details["temp_amount"] = $tem->amount;
					$storage = Storages::where('Id', '=', $tem->storage_id)->first();
					if ($storage->Name == "Kho nhà") {
						$product_details["temp_storage_home"] = true;
					}
					else if ($storage->Name == "Kho quán") {
						$product_details["temp_storage_work"] = true;
					}
					else {
						$product_details["temp_storage_home"] = false;
						$product_details["temp_storage_work"] = false;
					}
				}
			}
      $product_details["product_name"] = $product->Name;
      $product_details["product_price"] = number_format($product->Price);
			$product_details["product_id"] = $product->Id;
      // so luong kho nha
      $product_details["product_amount_storage_1"] = 
        $this::getProductAmountInStorage($product->Id, "kho nhà");
      // so luong kho quan
      $product_details["product_amount_storage_2"] = 
        $this::getProductAmountInStorage($product->Id, "kho quán");

			array_push($data, $product_details);
		}
		
    return View("StorageManagement", [
      "isAdmin" => $_SESSION['Role'] == ADMIN_ROLE,
      "data" => $data,
			"storages" => $storages
    ]);
	}

  public function getProductAmountInStorage($productId, $storageName){
    $data = [];
    $storageId = Storages::whereRaw("LOWER(Name) = ?", $storageName)->first()->Id;
    $product = StorageContent::where("StorageId", "=", $storageId)
              ->where("ProductId", "=", $productId)
              ->first();
    $productAmount = isset($product->Amount) ? $product->Amount : 0;
    return $productAmount;
  }

	function deleteStorage()
	{
		$id = $_POST['Id'];
		Storages::where('Id', '=', $id)->delete();
		echo "done";
	}

	public function deleteProduct(){
		if (havePermission(10)) {
			$id = $_POST['Id'];
			Products::where('Id', '=', $id)->delete();
			echo "done";
		}
		else {
			echo "Bạn không có quyền vào trang này";
		}
	}

	public function viewAddProduct(){
		if (havePermission(8)) {
			return View("AddProductToStorange",[
				"isAdmin" => $_SESSION['Role'] == ADMIN_ROLE
			]);
		}
		else {
			echo "Bạn không có quyền vào trang này";
		}
	}

	public function addProduct(){
		if (havePermission(8)) {
			$productName = $_POST['name'];
			$productPrice = str_replace(".", "", $_POST['price']);
			$product = Products::where('Name', '=', $productName)->first();

			if($product == null){
				$newProduct = new Products;
				$newProduct->Name = $productName;
				$newProduct->Price = $productPrice;
				$newProduct->save();
			}
			else{
				return View("AddProductToStorange",[
					"isAdmin" => $_SESSION['Role'] == ADMIN_ROLE,
					"message" => "sản phẩm đã tồn tại !"
				]);
			}
			redirect('/storage');
		}
		else {
			echo "Bạn không có quyền vào trang này";
		}
	}

	public function viewEditProduct($productId){
		if (havePermission(11)) {
			$product = Products::find($productId);
			return View('editProductInStorange', [
				"price" => $product->Price,
				"name" => $product->Name,
				"isAdmin" => $_SESSION['Role'] == ADMIN_ROLE
			]);
		}
		else {
			echo "Bạn không có quyền vào trang này";
		}
	}

	public function EditProduct($productId){
		if (havePermission(11)) {
			$productName = $_POST['name'];
			$productPrice = str_replace(".", "", $_POST['price']);
			$product = Products::find($productId);
			$product->Name = $productName;
			$product->Price = $productPrice;
			$product->save();
			redirect('/storage');
		}
		else {
			echo "Bạn không có quyền vào trang này";
		}
	}

	public function viewAddStorage(){
		return View("AddStorage", ["isAdmin" => $_SESSION['Role'] == ADMIN_ROLE]);
	}

	public function AddStorage(){
		$name = $_POST['name'];
		$storage = new Storages;
		$storage->Name = $name;
		$storage->save();
		redirect('/storage');
	}

	public function ViewEditStorage($id){
		$storage = Storages::find($id);
		return View("editStorage", [
			"name" => $storage->Name,
			"isAdmin" => $_SESSION['Role'] == ADMIN_ROLE
		]);
	}

	public function EditStorage($id){
		$storage = Storages::find($id);
		$storage->Name = $_POST['name'];
		$storage->save();
		redirect('/storage');
	}

  public function newImport() {
    $data = json_decode($_GET['data']);
    foreach($data as $record) {
      $listStorage = [];
      $record = (array)$record;
      if ($record['isWorkStorage']) {
        $id = Storages::where('Name', '=', 'Kho quán')->first()->Id;
        array_push($listStorage, $id);
      }
      if ($record['isHomeStorage']) {
        $id = Storages::where('Name', '=', 'Kho nhà')->first()->Id;
        array_push($listStorage, $id);
      }
      
      foreach ($listStorage as $storageId) {
        $this->importProduct(
          $storageId, $record['productId'], date('Y-m-d'), $record['amount']
        );
      }
		}
		temp_import::truncate();
    redirect("/storage");
	}
	
	public function addTempImport() {
		$data = $_POST['data'];
		temp_import::truncate();
		foreach($data as $temp) {
			if ($temp["isHomeStorage"] == "true") {
				$import = new temp_import;
				$import->product_id = $temp["productId"];
				$import->amount = $temp["amount"];
				$import->storage_id = Storages::where('Name', '=', 'Kho nhà')->first()->Id;
				$import->save();
			}

			if ($temp["isWorkStorage"] == "true") {
				$import = new temp_import;
				$import->product_id = $temp["productId"];
				$import->amount = $temp["amount"];
				$import->storage_id = Storages::where('Name', '=', 'Kho quán')->first()->Id;
				$import->save();
			}
		}
	}

	public function importProduct($storageId, $productId, $date, $amount){
		$import = new StorageImport;
		$import->StorageId = $storageId;
		$import->ProductId = $productId;
		$import->Import_date = $date;
		$import->Amount = $amount;
		$import->save();

		$content = StorageContent::where('StorageId', '=', $storageId)
									->where('ProductId', '=', $productId)
									->first();

    if(isset($content)) {
  		$content->Amount = $content->Amount + (int) $amount;
  		$content->save();
    }
    else {
      $storageContent = new StorageContent;
      $storageContent->StorageId = $storageId;
      $storageContent->ProductId = $productId;
      $storageContent->Amount = $amount;
      $storageContent->save();
    }
		//redirect('/storage');
	}

	public function exportProduct($storageId, $productId, $date, $amount){
		$export = new StorageExport;
		$export->StorageId = $storageId;
		$export->ProductId = $productId;
		$export->Export_date = $date;
		$export->Amount = $amount;
		$export->save();

    $storageName = Storages::where('Id', '=', $storageId)->first()->Name;
    if ($storageName == 'Kho nhà') {
      $workStorageId = Storages::where('Name', '=', 'Kho quán')->first()->Id;
      $this->importProduct($workStorageId, $productId, $date, $amount);
    }
		$content = StorageContent::where('StorageId', '=', $storageId)
									->where('ProductId', '=', $productId)
									->first();
		if(isset($content)) {
      $content->Amount = $content->Amount - (int) $amount;
      $content->save();
    }
    else {
      $storageContent = new StorageContent;
      $storageContent->StorageId = $storageId;
      $storageContent->ProductId = $productId;
      $storageContent->Amount = $amount;
      $storageContent->save();
    }
		redirect('/storage');
	}

	public function importAndExportProduct(){
    $storageId = $_POST['storage'];
		$productId = $_POST['Id'];
		$date = $_POST['import_date'];
		$amount = $_POST['amount'];
		$act = $_POST['act'];
		if($act == "import"){
			$this->importProduct($storageId, $productId, $date, $amount);
		}
		else{
			$this->exportProduct($storageId, $productId, $date, $amount);
		}
	}

	public function history(){
		$imports = StorageImport::all()->toArray();
		$home_imports = [];
    $work_imports = [];
		$exports = StorageExport::all()->toArray();
		$home_exports = [];
    $work_exports = [];
		foreach ($imports as $import){
			$productName = Products::find($import["ProductId"])->Name;
			$storageName = Storages::find($import["StorageId"])->Name;
			$import["storageName"] = $storageName;
			$import["Name"] = $productName;
      if ($storageName == 'Kho nhà') {
        array_push($home_imports, $import);
      }
			else {
        array_push($work_imports, $import);
      }
		}
		foreach ($exports as $export){
			$productName = Products::find($export["ProductId"])->Name;
			$storageName = Storages::find($export["StorageId"])->Name;
			$export["storageName"] = $storageName;
			$export["Name"] = $productName;
			if ($storageName == 'Kho nhà') {
        array_push($home_exports, $export);
      }
      else {
        array_push($work_exports, $export);
      }
		}

		return View("storageHistory", [
			"isAdmin" => $_SESSION['Role'] == ADMIN_ROLE,
      "home_imports" => $home_imports, 
      "work_imports" => $work_imports,
			"home_exports" => $home_exports, 
			"work_exports" => $work_exports
		]);
	}
}
?>