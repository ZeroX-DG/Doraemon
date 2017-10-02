<?php
namespace Model;
use Illuminate\Database\Eloquent\Model as Eloquent;
class StorageExport extends Eloquent {
	protected $table = "storage_export_history";
	protected $fillable = ["StorageId", "ProductId", "Export_date", "Amount"];
	public $timestamps = false;
}
?>