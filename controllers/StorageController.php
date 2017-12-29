<?php
use Model\Storages;
use Model\StorageContent;
use Model\Products;
use Model\StorageImport;
use Model\StorageExport;
class StorageController{
	public function Index(){
		$Products = Products::all();
    $storages = Storages::all();
		$data = [];
    foreach($Products as $product) {
      $product_details = [];
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
		$id = $_POST['Id'];
		Products::where('Id', '=', $id)->delete();
		echo "done";
	}

	public function viewAddProduct(){
		return View("AddProductToStorange",[
      "isAdmin" => $_SESSION['Role'] == ADMIN_ROLE
    ]);
	}

	public function addProduct(){
		$productName = $_POST['name'];
    $productPrice = $_POST['price'];
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

	public function viewEditProduct($productId){
		$product = Products::find($productId);
		return View('editProductInStorange', [
			"price" => $product->Price,
			"name" => $product->Name,
			"isAdmin" => $_SESSION['Role'] == ADMIN_ROLE
		]);
	}

	public function EditProduct($productId){
		$productName = $_POST['name'];
    $productPrice = str_replace(".", "", $_POST['price']);
		$product = Products::find($productId);
		$product->Name = $productName;
    $product->Price = $productPrice;
		$product->save();
    redirect('/storage');
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
		redirect('/storage');
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