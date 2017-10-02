<?php
namespace Model;
use Illuminate\Database\Eloquent\Model as Eloquent;
class Storages extends Eloquent {
	protected $primaryKey = 'Id';
	protected $fillable = ["Name"];
	public $timestamps = false;
}
?>