<?php
namespace Model;
use Illuminate\Database\Eloquent\Model as Eloquent;
class Users extends Eloquent {
	protected $fillable = ["UserName", "PassWord", "DisplayName","Role"];
	public $timestamps = false;
}
?>