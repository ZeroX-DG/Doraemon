<?php
namespace Model;
use Illuminate\Database\Eloquent\Model as Eloquent;
class Ships extends Eloquent {
	public $table = "Ships";
	protected $fillable = ["Address", "Phone", "Total", "ShipPrice", "Amount", "Distance", "Shipper"];
	protected $primaryKey = "Id";
	public $timestamps = false;
}
?>