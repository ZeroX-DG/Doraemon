<?php
namespace Model;
use Illuminate\Database\Eloquent\Model as Eloquent;
class StorageImport extends Eloquent {
	protected $table = "storage_import_history";
	protected $fillable = ["StorageId", "ProductId", "Import_date", "Amount"];
	public $timestamps = false;
}
?>