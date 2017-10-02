<?php
use Model\Storages;
use Model\StorageContent;
use Model\Products;
use Model\StorageImport;
use Model\StorageExport;
class StorageController{
	public function Index(){
		$Storages = Storages::all();
		return View("StorageManagement", [
			"isAdmin" => $_SESSION['Role'] == ADMIN_ROLE,
			"storages" => $Storages
		]);
	}

	function deleteStorage()
	{
		$id = $_POST['Id'];
		Storages::where('Id', '=', $id)->delete();
		echo "done";
	}

	public function showContent($id){
		$data = [];
		$storageContent = StorageContent::where("StorageId", "=", $id)
										->get()
										->toArray();
		foreach ($storageContent as $content) {
			$product = Products::find($content["ProductId"]);
			$content["ProductName"] = $product->Name;
			$content["ProductId"] = $product->Id;
			array_push($data, $content);
		}
		return View("StorageContent", [
			"isAdmin" => $_SESSION['Role'] == ADMIN_ROLE,
			"content" => $data,
			"storageId" => $id
		]);
	}

	public function deleteProduct($storageId){
		$id = $_POST['Id'];
		StorageContent::where("Id", "=", $storageId)
						->where("ProductId", "=", $id)
						->delete();
		echo "done";
	}

	public function viewAddProduct(){
		return View("AddProductToStorange",["isAdmin" => $_SESSION['Role'] == ADMIN_ROLE]);
	}

	public function addProduct($storageId){
		$productName = $_POST['name'];
		$product = Products::where('Name', '=', $productName)->first();

		$productId = null;
		if($product == null){
			$newProduct = new Products;
			$newProduct->Name = $productName;
			$newProduct->save();
			$productId = $newProduct->Id;
		}
		else{
			$productId = $product->Id;
		}
		$current_content = StorageContent::where('StorageId', '=', $storageId)
									->where('ProductId', '=', $productId)
									->first();
		if($current_content == null){
			$content = new StorageContent;
			$content->StorageId = $storageId;
			$content->ProductId = $productId;
			$content->Amount = 0;
			$content->save();
		}
		else{
			$current_content->Amount = $current_content->Amount + 0;
			$current_content->save();
		}
		redirect('/storage/'.$storageId);
	}

	public function viewEditProduct($storageId, $productId){
		$content = StorageContent::where('StorageId', '=', $storageId)
									->where('ProductId', '=', $productId)
									->first();
		$product = Products::find($productId);
		return View('editProductInStorange', [
			"amount" => $content->Amount,
			"name" => $product->Name,
			"isAdmin" => $_SESSION['Role'] == ADMIN_ROLE
		]);
	}

	public function EditProduct($storageId, $productId){
		$productName = $_POST['name'];
		$product = Products::find($productId);
		$product->Name = $productName;
		$product->save();
		redirect('/storage/'.$storageId);
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
		$content->Amount = $content->Amount + (int) $amount;
		$content->save();
		redirect('/storage/'.$storageId);
	}

	public function exportProduct($storageId, $productId, $date, $amount){
		$export = new StorageExport;
		$export->StorageId = $storageId;
		$export->ProductId = $productId;
		$export->Export_date = $date;
		$export->Amount = $amount;
		$export->save();

		$content = StorageContent::where('StorageId', '=', $storageId)
									->where('ProductId', '=', $productId)
									->first();
		$content->Amount = $content->Amount - (int) $amount;
		$content->save();
		redirect('/storage/'.$storageId);
	}

	public function importAndExportProduct($storageId){
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
		$r_imports = [];
		$exports = StorageExport::all()->toArray();
		$r_exports = [];
		foreach ($imports as $import){
			$productName = Products::find($import["ProductId"])->Name;
			$storageName = Storages::find($import["StorageId"])->Name;
			$import["storageName"] = $storageName;
			$import["Name"] = $productName;
			array_push($r_imports, $import);
		}
		foreach ($exports as $export){
			$productName = Products::find($export["ProductId"])->Name;
			$storageName = Storages::find($export["StorageId"])->Name;
			$export["storageName"] = $storageName;
			$export["Name"] = $productName;
			array_push($r_exports, $export);
		}

		return View("storageHistory", [
			"isAdmin" => $_SESSION['Role'] == ADMIN_ROLE,
			"imports" => $r_imports, 
			"exports" => $r_exports
		]);
	}
}
?>