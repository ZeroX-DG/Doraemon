<?php
namespace Model;
use Illuminate\Database\Eloquent\Model as Eloquent;
class Users extends Eloquent {
	protected $table = "users";
	protected $primaryKey = 'Id';
	protected $fillable = ["UserName", "PassWord", "DisplayName","Role"];
	public $timestamps = false;
}
?>