<?php
namespace Model;
use Illuminate\Database\Eloquent\Model as Eloquent;
class StorageContent extends Eloquent {
	protected $primaryKey = 'Id';
	protected $fillable = ["StorageId", "ProductId", "Amount"];
	public $timestamps = false;
}
?>